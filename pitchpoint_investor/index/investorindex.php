<?php
// D:\xampp\htdocs\pitchPoint\pitchpoint_investor\index\investorindex.php

// Resolve project root (one level up from /index)
$ROOT = dirname(__DIR__);                     // $ROOT = D:\xampp\htdocs\pitchPoint\pitchpoint_investor

// Load config, DB and core classes (order matters)
require $ROOT . '/app/config/config.php';     // Load application configuration (constants, settings)
require $ROOT . '/app/config/database.php';   // Load database connection helper
require $ROOT . '/app/core/Model.php';        // Load base Model class
require $ROOT . '/app/core/Controller.php';   // Load base Controller class

// Autoload controllers and models from /app
spl_autoload_register(function ($class) use ($ROOT) {   // Register anonymous function as autoloader
    $paths = [                                          // List of file paths to look for the class
        $ROOT . "/app/controllers/$class.php",          // Path where controllers live
        $ROOT . "/app/models/$class.php",               // Path where models live
    ];
    foreach ($paths as $p) {                            // Loop through each possible path
        if (file_exists($p)) {                          // If the file exists
            require $p;                                 // Include the class file
            return;                                     // Stop searching after first match
        }
    }
});

// Start or resume session
session_start();                                        // Enable session handling for authentication etc.

// Load auth-related controllers and WAF
require_once __DIR__ . '/../../auth/controller/userController.php';    // Load userController (login/logout logic)
require_once __DIR__ . '/../../auth/controller/SignUpController.php';  // Load SignUpController (registration logic)
require_once __DIR__ . '/../../auth/waf/theFire.php';                  // Load Web Application Firewall (security layer)

// ROUTER — handles requests from login & signup via ?action=...
$action = $_GET['action'] ?? null;                      // Get 'action' from query string or null if missing

switch ($action) {                                      // Decide what to do based on 'action' value

    case 'login':                                       // If action = 'login'
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {    // If this is a POST request (form submitted)
            userController::login($_POST);              // Call static login method with submitted form data
            exit;                                       // Stop script after handling login
        }
        header("Location: /pitchPoint/auth/login.php"); // On GET request, redirect user to login page
        exit;                                           // Stop script after redirect

    case 'signup':                                      // If action = 'signup'
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {    // If this is a POST request (form submitted)
            SignUpController::register($_POST);         // Call static register method with submitted form data
            exit;                                       // Stop script after handling signup
        }
        header("Location: /pitchPoint/auth/signUp.php");// On GET request, redirect user to signup page
        exit;                                           // Stop script after redirect

    case 'verify':                                      // If action = 'verify' (email verification link)
        if (!empty($_GET['token'])) {                   // If a verification token is provided
            SignUpController::verifyEmail($_GET['token']); // Verify email using provided token
            exit;                                       // Stop script after verification
        }
        header("Location: /pitchPoint/auth/login.php?verify=invalid"); // Redirect to login with invalid token message
        exit;                                           // Stop script after redirect

    case 'logout':                                      // If action = 'logout'
        if (session_status() === PHP_SESSION_NONE) {    // If session is not active yet
            session_start();                            // Start the session
        }
        $_SESSION = [];                                 // Clear all session variables
        if (ini_get('session.use_cookies')) {           // If PHP is using cookies for sessions
            $params = session_get_cookie_params();      // Get current cookie parameters
            setcookie(                                  // Overwrite session cookie with an expired one
                session_name(),                         // Cookie name (session name)
                '',                                     // Empty value
                time() - 42000,                         // Set expiration time in the past
                $params['path'],                        // Same path as original cookie
                $params['domain'],                      // Same domain as original cookie
                $params['secure'],                      // Same secure flag
                $params['httponly']                     // Same HttpOnly flag
            );
        }
        session_destroy();                              // Fully destroy the session on the server
        header('Location: /pitchPoint/pitchpoint_staff/public/index.php'); // Redirect to staff public index
        exit;                                           // Stop script after redirect

    default:                                            // For all other cases (or when action is null)

        // Basic router: ?url=controller/action/param1/param2/...
        $url = $_GET['url'] ?? 'investor/dashboard';    // Get 'url' from query string or default to investor/dashboard

        $parts = array_values(                          // Reindex array keys from 0,1,2...
            array_filter(                               // Remove empty elements from array
                explode('/', trim($url, '/'))           // Split URL by '/' after trimming leading/trailing slashes
            )
        );

        // Special handling for auth routes like auth/login or auth/logout
        if (!empty($parts[0]) && $parts[0] === 'auth') { // If first segment is 'auth'
            if (!empty($parts[1]) && $parts[1] === 'login') { // If second segment is 'login'
                header('Location: /pitchPoint/auth/login.php'); // Redirect to login page
                exit;                                       // Stop script after redirect
            }
            if (!empty($parts[1]) && $parts[1] === 'logout') { // If second segment is 'logout'
                if (session_status() === PHP_SESSION_NONE) {   // Ensure session is started
                    session_start();
                }
                $_SESSION = [];                               // Clear session variables
                if (ini_get('session.use_cookies')) {         // If PHP uses session cookies
                    $params = session_get_cookie_params();    // Get cookie parameters
                    setcookie(
                        session_name(),
                        '',
                        time() - 42000,
                        $params['path'],
                        $params['domain'],
                        $params['secure'],
                        $params['httponly']
                    );
                }
                session_destroy();                            // Destroy session data
                header('Location: /pitchPoint/pitchpoint_staff/public/index.php'); // Redirect after logout
                exit;                                         // Stop script after redirect
            }
            // For other auth routes, fall through to AuthController via normal routing
        }

        // Build controller and action from URL parts
        $controllerName = ucfirst($parts[0] ?? 'investor') . 'Controller'; // First segment -> Controller name (default InvestorController)
        $action         = $parts[1] ?? 'dashboard';         // Second segment -> action method (default dashboard)
        $params         = array_slice($parts, 2);           // Remaining segments are parameters

        // Dispatch: check that controller exists
        if (!class_exists($controllerName)) {               // If the controller class is not defined
            http_response_code(404);                        // Send HTTP 404 Not Found status
            echo "Controller not found: $controllerName";   // Output error message
            exit;                                           // Stop script
        }

        $controller = new $controllerName();                // Instantiate the controller

        // If requested action doesn't exist, try 'index' as fallback
        if (!method_exists($controller, $action)) {         // If specified action method is not found
            if (method_exists($controller, 'index')) {      // If controller has an index() method
                $action = 'index';                          // Use index() as fallback action
            } else {                                        // If index() is also missing
                http_response_code(404);                    // Send HTTP 404 Not Found status
                $methods = implode(                         // Build comma-separated string of methods
                    ', ',
                    array_filter(                           // Filter out internal/private methods
                        get_class_methods($controller),     // Get all methods of the controller
                        fn($m) => $m[0] !== '_'             // Keep only methods that do not start with '_'
                    )
                );
                echo "Action not found: $controllerName::$action<br>Available: $methods"; // Show debug info
                exit;                                       // Stop script
            }
        }

        // Call the resolved controller action with any parameters
        call_user_func_array([$controller, $action], $params); // Execute controller method with params
}                                                              // End of switch (router for ?action)
