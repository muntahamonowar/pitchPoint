<?php
// app/controllers/guestcontroller.php
declare(strict_types=1);

require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../../config/db.php';

// handles all pages that a user can see
//home page with al projects
//individual project details
class GuestController
{
    public function home(): void//loads the home page nad checks if guest searches for something , asks the project model for matching public projects and then sends the data to the home view
    {
        $q = trim($_GET['q'] ?? '');
        $projects = Project::getPublicProjects($q);
        $search = $q; // pass to view
        require __DIR__ . '/../views/guest/home.php';
    }
//shows details for a single project
   //checks project id in theurl
   //then asks the model to fetch that specificproject
   //if found then shows it 
   //if not then shows error 404
    public function showProject(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $project = $id > 0 ? Project::getPublicById($id) : null;
        http_response_code($project ? 200 : 404);
        require __DIR__ . '/../views/guest/project.php';
    }
}
