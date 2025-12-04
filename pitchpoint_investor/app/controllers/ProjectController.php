<?php // Opening PHP tag to start PHP code execution

class ProjectController extends Controller // Define the ProjectController class that extends the base Controller class
{
    private Project $projectModel; // Declare a private property to store the Project model instance
    private Investor $investorModel; // Declare a private property to store the Investor model instance
    private Interest $interestModel; // Declare a private property to store the Interest model instance
    private Investment $investmentModel; // Declare a private property to store the Investment model instance

    public function __construct() // Define the constructor method that runs when the controller is instantiated
    {
        $this->projectModel    = new Project(); // Initialize the Project model instance
        $this->investorModel   = new Investor(); // Initialize the Investor model instance
        $this->interestModel   = new Interest(); // Initialize the Interest model instance
        $this->investmentModel = new Investment(); // Initialize the Investment model instance
    }

    /**
     * Small redirect helper so we ALWAYS hit
     * /pitchPoint/pitchpoint_investor/index/investorindex.php?url=...
     */
    private function go(string $route): void // Define a private helper method to redirect to a specific route
    {
        header('Location: /pitchPoint/pitchpoint_investor/index/investorindex.php?url=' . ltrim($route, '/')); // Send HTTP redirect header with the route parameter
        exit; // Terminate script execution after redirect
    }

    // /project or /project/index
    public function index() // Define the index method that handles the default project route
    {
        $this->go('project/explore'); // Redirect to the project explore page
    }

    // /project/explore
    public function explore() // Define the explore method to display and filter projects
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        // search + category filter from URL
        $q     = trim($_GET['q'] ?? ''); // Get search query from GET parameter, trim whitespace, or empty string if not set
        $catId = isset($_GET['cat']) ? (int)$_GET['cat'] : null; // Get category ID from GET parameter, cast to integer, or null if not set

        // get all categories for the dropdown
        $categories = $this->projectModel->categories(); // Get all categories from the project model

        // use explore() with search + category
        $projects = $this->projectModel->explore( // Call explore method with search and category filters
            $q === '' ? null : $q, // Pass search query if not empty, otherwise null
            null, // No limit specified
            $catId ?: null // Pass category ID if set, otherwise null
        ); // End of explore method call

        $this->view('project/explore', [ // Render the explore view with the following data
            'projects'   => $projects, // Pass projects array
            'categories' => $categories, // Pass categories array
            'q'          => $q, // Pass search query
            'catId'      => $catId, // Pass category ID
        ]); // End of view method call
    }

    // /project/show/{id}
    public function show($projectId) // Define the show method to display a single project's details
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $proj = $this->projectModel->find((int)$projectId); // Find the project by ID, casting to integer
        if (!$proj) { // Check if project was not found
            http_response_code(404); // Set HTTP response code to 404 Not Found
            exit('Project not found'); // Exit script with error message
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        $inv    = $userId ? $this->investorModel->byUserId($userId) : null; // Get investor record if user ID exists, otherwise null

        $isInterested = $inv // Check if investor is interested in this project
            ? $this->interestModel->has($inv['investor_id'], $proj['project_id']) // Check interest status if investor exists
            : false; // Set to false if no investor record

        $this->view('project/show', compact('proj', 'inv', 'isInterested')); // Render the show view with project, investor, and interest status data
    }

    // /project/toggleInterest/{id}
    public function toggleInterest($projectId) // Define the toggleInterest method to add or remove interest in a project
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set

        if (!$userId) { // Check if user ID is missing
            // not logged in → send to login
            $this->go('auth/login'); // Redirect to login page if user is not authenticated
        }

        $inv = $this->investorModel->byUserId($userId); // Get investor record by user ID
        if ($inv) { // Check if investor record exists
            $this->interestModel->toggle($inv['investor_id'], (int)$projectId); // Toggle interest status for this project
        }

        // after marking interest, go to profile page
        $this->go('investor/profile'); // Redirect to investor profile page after toggling interest
    }

    /**
     * /project/invest/{id}
     *
     * STEP 1 (GET): Show payment "gateway" page.
     * STEP 2 (POST): Validate amount + method, create investment and
     *                redirect to My Investments.
     */
    public function invest($projectId) // Define the invest method to handle investment process
    {
        if (session_status() === PHP_SESSION_NONE) { // Check if session is not already started
            session_start(); // Start a new session or resume existing session
        }

        $userId = $_SESSION['user_id'] ?? null; // Get the user ID from session, or null if not set
        if (!$userId) { // Check if user ID is missing
            // not logged in → go to login
            $this->go('auth/login'); // Redirect to login page if user is not authenticated
        }

        $projectId = (int)$projectId; // Cast project ID to integer

        // load project
        $proj = $this->projectModel->find($projectId); // Find the project by ID
        if (!$proj) { // Check if project was not found
            http_response_code(404); // Set HTTP response code to 404 Not Found
            exit('Project not found'); // Exit script with error message
        }

        // load investor linked to this user
        $inv = $this->investorModel->byUserId($userId); // Get investor record by user ID

        // -------- GET: redirect to existing payment.php --------
        if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Check if request method is GET
            // Redirect to the existing payment/payment.php file
            // It handles the form display and investor creation
            header('Location: /pitchPoint/payment/payment.php?project_id=' . $projectId); // Send HTTP redirect header to payment page with project ID
            exit; // Terminate script execution after redirect
        }

        // -------- POST: confirm investment + insert into DB --------
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if request method is POST
            $amount = (float)($_POST['amount'] ?? 0); // Get investment amount from POST data, cast to float, or 0 if not set
            $method = $_POST['method'] ?? 'card'; // Get payment method from POST data, or 'card' as default

            if ($amount <= 0) { // Check if amount is invalid (zero or negative)
                // invalid amount → back to payment page
                $this->go("project/invest/$projectId"); // Redirect back to invest page if amount is invalid
            }

            if ($inv) { // Check if investor record exists
                $this->investmentModel->create( // Call create method to insert investment record
                    $inv['investor_id'], // Pass investor ID
                    $projectId, // Pass project ID
                    $amount, // Pass investment amount
                    $method // Pass payment method
                ); // End of create method call
            }

            // now show My Investments
            $this->go('investor/investments'); // Redirect to investments page after successful investment
        }

        // any other HTTP method → just go back to details
        $this->go("project/show/$projectId"); // Redirect to project details page for any other HTTP method
    }

    /**
     * /project/confirmInvest/{id}
     *
     * Kept for backward compatibility.
     * If some old form still posts here, delegate to invest().
     */
    public function confirmInvest($projectId) // Define the confirmInvest method for backward compatibility
    {
        $this->invest($projectId); // Delegate to the invest method
    }
}
