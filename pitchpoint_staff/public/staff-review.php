<?php
// public/staff-review.php
declare(strict_types=1);

// Use authentication from auth folder 
require_once __DIR__ . '/../../auth/helpers/staff_auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/controllers/staffcontroller.php';

// Require staff login using auth folder method
require_staff();

$controller = new StaffController(db());
$controller->review();
