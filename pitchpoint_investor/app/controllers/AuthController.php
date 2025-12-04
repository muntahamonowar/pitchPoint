<?php // Opening PHP tag to start PHP code execution

class AuthController extends Controller // Define the AuthController class that extends the base Controller class
{
    public function logout() // Define the logout method to handle user logout
{
    // Make sure session is started
    if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
        session_start(); // Start a new session or resume existing session
    }

    // Clear all session data
    $_SESSION = []; // Clear all session variables by assigning an empty array

    // Optional: also clear the session cookie
    if (ini_get('session.use_cookies')) { // Check if session cookies are enabled in PHP configuration
        $params = session_get_cookie_params(); // Get the current session cookie parameters
        setcookie( // Set a cookie with expired time to delete it from the browser
            session_name(), // Get the session cookie name
            '', // Set cookie value to empty string
            time() - 42000, // Set expiration time to past (effectively deleting the cookie)
            $params['path'], // Use the same path as the original session cookie
            $params['domain'], // Use the same domain as the original session cookie
            $params['secure'], // Use the same secure flag as the original session cookie
            $params['httponly'] // Use the same httponly flag as the original session cookie
        );
    }

    // Destroy the session
    session_destroy(); // Destroy all data registered to the session

    // After logout, redirect to staff public index
    header('Location: /pitchPoint/pitchpoint_staff/public/index.php'); // Send HTTP redirect header to the staff public index page
    exit; // Terminate script execution after redirect
}


}
