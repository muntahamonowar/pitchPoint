<?php
declare(strict_types=1);
// Enforces strict type checking so PHP warns you when using wrong data types.


// Load the Investment model.
// This model handles all database queries related to investments.
require_once __DIR__ . '/../models/Investment.php';


/**
 * InvestmentsController
 *
 * Controls the admin Investments page.
 * The admin can view all investment records made by investors.
 */
class InvestmentsController extends BaseAdminController
{
    /**
     * INDEX METHOD
     * ------------
     * Shows the Investments Overview page.
     *
     * Steps:
     * 1. Load all investment records with project + investor details
     * 2. Prepare page title
     * 3. Render the view with data
     */
    public function index(): void
    {
        // Fetch all investment records with JOINs:
        //  - investment details
        //  - investor name
        //  - project title
        $investments = Investment::allWithRelations();

        // Title shown at the top of the page
        $title = 'Investments Overview';

        // Render admin/views/investments/index.php
        // compact() creates an array ['title' => $title, 'investments' => $investments]
        $this->render('investments/index', compact('title','investments'));
    }
}
