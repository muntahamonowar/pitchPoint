<?php
// public/logout.php - Simple logout handler using auth folder
declare(strict_types=1);

require_once __DIR__ . '/../../auth/helpers/staff_auth.php';
staff_logout();

