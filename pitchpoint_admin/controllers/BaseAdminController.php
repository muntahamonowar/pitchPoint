<?php
declare(strict_types=1);
// Enable strict typing to make PHP more strict about data types.

/**
 * BaseAdminController
 *
 * All admin controllers (DashboardController, UsersController, etc.)
 * extend this class so they can use:
 *   - render()   → to show a view with the admin layout
 *   - redirect() → to move to another page
 */
abstract class BaseAdminController
{
    /**
     * Render a view inside the admin layout (header + sidebar + footer).
     *
     * @param string $viewPath  Path to the main view file, e.g. 'users/index'
     * @param array  $data      Variables to pass to the view as $title, $users, etc.
     */
    protected function render(string $viewPath, array $data = []): void
    {
        // Get the currently logged-in admin (from session).
        // This can be used inside header/sidebar to show admin name/email.
        $currentAdmin = admin_current();

        // Turn array keys into variables for the view.
        // Example: ['title' => 'Users'] becomes $title = 'Users';
        // EXTR_SKIP prevents overwriting existing variables.
        extract($data, EXTR_SKIP);

        // Base directory for all admin view files.
        $base = __DIR__ . '/../views';

        // Include the common admin layout parts in this order:
        // 1) header
        // 2) sidebar
        // 3) main view (based on $viewPath)
        // 4) footer
        include $base . '/partials/header.php';
        include $base . '/partials/sidebar.php';
        include $base . '/' . $viewPath . '.php';
        include $base . '/partials/footer.php';
    }

    /**
     * Redirect to another admin route on index.php
     *
     * @param string $query  A query string, e.g. 'c=users&a=index'
     */
    protected function redirect(string $query): void
    {
        // Send a Location header to the browser to change URL.
        header('Location: index.php?' . $query);

        // Always call exit after header redirect to stop script execution.
        exit;
    }
}
