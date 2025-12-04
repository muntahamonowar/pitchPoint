<?php
declare(strict_types=1);
// Strict typing → PHP will enforce correct types and prevent hidden bugs.

session_start();
// Start session so admin login, CSRF, and access control work.


// ========================================
// LOAD REQUIRED CORE FILES
// ========================================

require_once __DIR__ . '/config/database.php';
// Load database connection (db.php or similar)


// Authentication system (located OUTSIDE admin folder)
require_once __DIR__ . '/../auth/helpers/admin_auth.php';
// Contains admin session helpers (require_admin(), admin_current(), etc.)

require_once __DIR__ . '/../auth/controller/AdminAuthController.php';
// Controls admin login / logout actions.

require_once __DIR__ . '/controllers/BaseAdminController.php';
// Base controller extended by all admin controllers.



// ========================================
// MAP FRIENDLY URL CONTROLLER NAMES
// TO ACTUAL CLASS NAMES
// ========================================
//
// Example:
// ?c=projects&a=index → ProjectsController::index()
//
$controllerMap = [
    'auth'          => 'AuthController',
    'dashboard'     => 'DashboardController',
    'users'         => 'UsersController',
    'projects'      => 'ProjectsController',
    'investments'   => 'InvestmentsController',

    'approvals'     => 'ApprovalsController',
    'notifications' => 'NotificationsController',
    'emails'        => 'EmailsController',
];


// ========================================
// READ CONTROLLER & ACTION FROM URL
// ========================================
$c = strtolower($_GET['c'] ?? 'dashboard');  // Default: dashboard
$a = strtolower($_GET['a'] ?? 'index');      // Default: index action



// =======================================================
// SPECIAL HANDLING FOR ADMIN AUTHENTICATION
// (Login, logout, verify)
// =======================================================
// The admin authentication system is stored under /auth folder
// and NOT inside the admin/controllers folder.
// Therefore, it needs custom routing.
if ($c === 'auth') {

    // Protect ALL auth actions EXCEPT login, dologin, logout
    if (!in_array($a, ['login', 'dologin', 'logout'], true)) {
        require_admin(); // Only logged-in admins can access others
    }

    // Handle auth routes
    switch ($a) {

        case 'login':
            // Simply redirect to the real login page
            header('Location: /pitchPoint/auth/loginAdmin.php');
            exit;

        case 'dologin':
            // Only accept POST login submissions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                AdminAuthController::login($_POST);
            } else {
                header('Location: /pitchPoint/auth/loginAdmin.php');
                exit;
            }
            break;

        case 'logout':
            // Logout destroys session and redirects
            AdminAuthController::logout();
            break;

        default:
            // Unknown action under auth
            http_response_code(404);
            echo 'Action not found.';
            exit;
    }

    exit; // Stop routing here for auth actions
}



// =======================================================
// VALIDATE CONTROLLER
// =======================================================

if (!isset($controllerMap[$c])) {
    http_response_code(404);
    echo 'Unknown controller.';
    exit;
}

// Determine controller class name
$controllerClass = $controllerMap[$c];

// Determine file path where this controller is stored
$controllerFile  = __DIR__ . '/controllers/' . $controllerClass . '.php';


// Check the controller file actually exists
if (!file_exists($controllerFile)) {
    http_response_code(500);
    echo 'Controller file not found: ' . htmlspecialchars($controllerFile);
    exit;
}

require_once $controllerFile;


// Verify the class exists after loading the file
if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo 'Controller class not found: ' . htmlspecialchars($controllerClass);
    exit;
}



// =======================================================
// ACCESS CONTROL — REQUIRE ADMIN LOGIN
// =======================================================
//
// Only login and logout are allowed without login.
// All other controllers require admin session.
//
require_admin();



// =======================================================
// CREATE CONTROLLER INSTANCE AND EXECUTE ACTION
// =======================================================
$controller = new $controllerClass();

// Verify requested action exists within controller
if (!method_exists($controller, $a)) {
    http_response_code(404);
    echo 'Action not found.';
    exit;
}

// Call the method dynamically
$controller->$a();
