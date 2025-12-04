<?php // Opening PHP tag to start PHP code execution
// app/core/Controller.php
abstract class Controller { // Define an abstract base controller class that all controllers will extend
    protected function view(string $path, array $data = []) { // Define a protected method to render views with optional data array
        extract($data); // Extract array keys as variables for use in the view template
        require __DIR__ . '/../views/layouts/header.php'; // Include the header layout file
        require __DIR__ . '/../views/' . $path . '.php'; // Include the specific view file based on the path parameter
        require __DIR__ . '/../views/layouts/footer.php'; // Include the footer layout file
    }
    protected function redirect(string $route): void // Define a protected method to redirect to a specific route
{
    // $route is like 'investor/profile' or 'auth/login'
    $url = '/pitchPoint/pitchpoint_investor/index/investorindex.php?url=' . ltrim($route, '/'); // Build the full URL by prepending the base path and removing leading slashes from route
    header('Location: ' . $url); // Send HTTP redirect header to the browser
    exit; // Terminate script execution after redirect
}

}
