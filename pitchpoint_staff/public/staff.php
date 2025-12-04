<?php
// public/staff.php
declare(strict_types=1);

// Use authentication from auth folder 
require_once __DIR__ . '/../../auth/helpers/staff_auth.php';
require_once __DIR__ . '/../../auth/controller/StaffAuthController.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/controllers/staffcontroller.php';
 


// Handle auth actions (login/logout) before requiring authentication
$action = $_GET['action'] ?? null;

if ($action === 'dologin') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        StaffAuthController::login($_POST);
    } else {
        header('Location: /pitchPoint/auth/loginStaff.php');
        exit;
    }
    exit;
}

if ($action === 'logout') {
    StaffAuthController::logout();
    exit;
}

// Require staff login for all other actions
require_staff();




$controller = new StaffController(db());
$controller->dashboard();
include __DIR__ . '/../app/views/partials/footer.php';

