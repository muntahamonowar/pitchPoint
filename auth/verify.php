<?php
// verify.php
require_once __DIR__ . '/waf/theFire.php';
require_once __DIR__ . '/model/userModel.php';
session_start();

$token = $_GET['token'] ?? '';

if (!$token) {
    header('Location: /pitchPoint/auth/login.php?verify=missing');
    exit;
}

// Check for admin token
if (!empty($_SESSION['admin_verification_token']) && $token === $_SESSION['admin_verification_token']) {
    unset($_SESSION['admin_verification_token']);
    
    // Load admin auth helper to set up proper admin session
    require_once __DIR__ . '/helpers/admin_auth.php';
    require_once __DIR__ . '/model/AdminModel.php';
    
    // Find admin by email
    $admin = AdminModel::findByEmail('rafia@pitchpoint.com');
    
    if ($admin) {
        // Use adminLogin function to properly set up session
        adminLogin($admin);
    } else {
        // Fallback: set basic admin session if not found in DB
        $_SESSION['admin'] = [
            'admin_id'   => 1,
            'admin_name' => 'Admin',
            'email'      => 'rafia@pitchpoint.com',
        ];
        logAdminActivity(1, 'Logged in (via email verification)', 'Success');
        logAdminToFile(1, 'LOGIN', 'rafia@pitchpoint.com', 'Success');
    }
    
    header('Location: /pitchPoint/pitchpoint_admin/index.php');
    exit;
}

// Check for staff token
if (!empty($_SESSION['staff_verification_token']) && $token === $_SESSION['staff_verification_token']) {
    $email = $_SESSION['staff_verification_email'] ?? '';
    unset($_SESSION['staff_verification_token'], $_SESSION['staff_verification_email']);
    
    // Load staff auth helper to set up proper staff session
    require_once __DIR__ . '/helpers/staff_auth.php';
    require_once __DIR__ . '/model/StaffModel.php';
    
    // Find staff by email
    $staff = StaffModel::findByEmail($email);
    
    if ($staff && $staff['staff_id']) {
        // Set up session manually to avoid duplicate logging
        $_SESSION['user_id']   = (int)$staff['user_id'];
        $_SESSION['user_name'] = $staff['name'];
        $_SESSION['user_role'] = $staff['role'];
        $_SESSION['staff_id']  = (int)$staff['staff_id'];
        
        // Log verification activity (staff_login would log "Logged in", we want "Logged in (via email verification)")
        log_staff_activity((int)$staff['staff_id'], 'Logged in (via email verification)', 'Success');
        log_staff_to_file((int)$staff['staff_id'], 'LOGIN', $staff['email'] ?? '', 'Success');
        
        session_regenerate_id(true);
    }
    
    header('Location: /pitchPoint/pitchpoint_staff/public/staff.php');
    exit;
}

// Regular user verification path
$ok = userModel::verifyAccount($token);

if ($ok) {
    header('Location: /pitchPoint/auth/login.php?verify=success');
    exit;
} else {
    header('Location: /pitchPoint/auth/login.php?verify=invalid');
    exit;
}
