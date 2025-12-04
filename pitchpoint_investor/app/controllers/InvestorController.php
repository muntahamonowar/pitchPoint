<?php // Opening PHP tag to start PHP code execution

class InvestorController extends Controller // Define the InvestorController class that extends the base Controller class
{
    private User $userModel; // Declare a private property to store the User model instance
    private Investor $investorModel; // Declare a private property to store the Investor model instance
    private Project $projectModel; // Declare a private property to store the Project model instance

    public function __construct() // Define the constructor method that runs when the controller is instantiated
    {
        $this->userModel     = new User(); // Initialize the User model instance
        $this->investorModel = new Investor(); // Initialize the Investor model instance
        $this->projectModel  = new Project(); // Initialize the Project model instance
    }

    /**
     * Small internal redirect helper
     * Builds URLs like: /pitchPoint/pitchpoint_investor/index/investorindex.php?url=...
     */
    private function go(string $route): void // Define a private helper method to redirect to a specific route
    {
        header('Location: /pitchPoint/pitchpoint_investor/index/investorindex.php?url=' . ltrim($route, '/')); // Send HTTP redirect header with the route parameter
        exit; // Terminate script execution after redirect
    }

    /**
     * /investor  → redirect to dashboard
     */
    public function index() // Define the index method that handles the default investor route
    {
        $this->go('investor/dashboard'); // Redirect to the investor dashboard page
    }

    /**
     * /investor/dashboard
     * Shows basic overview, invested projects, explore list
     */
    public function dashboard() // Define the dashboard method to display the investor dashboard
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            $this->go('auth/login'); // Redirect to login page if user is not authenticated
        }

        $user = $this->userModel->find($userId); // Find the user record by user ID
        $inv  = $user ? $this->investorModel->byUserId($user['user_id']) : null; // Get investor record if user exists, otherwise null

        $invested = $inv ? $this->investorModel->projectsInvested($inv['investor_id']) : []; // Get list of invested projects if investor exists, otherwise empty array
        $explore  = $this->projectModel->explore(null, 8); // Get 8 projects to explore (no search, limit 8)

        $this->view('investor/dashboard', compact('user', 'inv', 'explore', 'invested')); // Render the dashboard view with the data
    }

    /**
     * /investor/profile
     * - View name, email, bio
     * - Edit bio (using ?edit=1)
     * - Show interested projects
     */
    public function profile() // Define the profile method to display and edit investor profile
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            $this->go('auth/login'); // Redirect to login page if user is not authenticated
        }

        // Load user + investor + interested projects
        $user = $this->userModel->find($userId); // Find the user record by user ID
        if (!$user) { // Check if user record was not found
            $this->go('auth/login'); // Redirect to login page if user does not exist
        }

        $inv        = $this->investorModel->byUserId($user['user_id']) ?? null; // Get investor record by user ID, or null if not found
        $interested = $inv ? $this->investorModel->projectsInterested($inv['investor_id']) : []; // Get list of interested projects if investor exists, otherwise empty array

        $message = null; // Initialize message variable to null
        $editing = isset($_GET['edit']) && $_GET['edit'] === '1'; // Check if edit mode is enabled via GET parameter

        // Handle POST: update bio only
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if request method is POST
            $bio = $_POST['bio'] ?? ($user['bio'] ?? null); // Get bio from POST data, or use existing bio, or null

            // Keep name + email from existing user, change only bio
            $this->userModel->updateProfile( // Call updateProfile method to update user profile
                $user['user_id'], // Pass user ID
                $user['name'], // Pass existing name
                $user['email'], // Pass existing email
                $bio // Pass new or existing bio
            );

            // Reload user to see updated bio
            $user    = $this->userModel->find($userId); // Reload user record to get updated data
            $message = 'Profile updated successfully.'; // Set success message
            $editing = false; // Disable edit mode after successful update
        }

        $this->view('investor/profile', compact('user', 'message', 'editing', 'interested')); // Render the profile view with the data
    }

    /**
     * /investor/investments
     * List projects the investor already invested in
     */
    public function investments() // Define the investments method to display investor's investments
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            $this->go('auth/login'); // Redirect to login page if user is not authenticated
        }

        $user = $this->userModel->find($userId); // Find the user record by user ID
        $inv  = $user ? $this->investorModel->byUserId($user['user_id']) : null; // Get investor record if user exists, otherwise null

        $invested = $inv ? $this->investorModel->projectsInvested($inv['investor_id']) : []; // Get list of invested projects if investor exists, otherwise empty array

        $this->view('investor/investments', compact('user', 'inv', 'invested')); // Render the investments view with the data
    }
}
