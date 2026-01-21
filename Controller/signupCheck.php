<?php
// signupCheck.php
// Process signup form submissions for the Athlete Fitness Tracker.

require_once __DIR__ . '/../Model/init.php';
require_once __DIR__ . '/../Model/db.users.php';
require_once __DIR__ . '/../Model/db.notifications.php';

if (!isPost()) {
    redirect('../View/signup.php');
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = sanitize($_POST['role'] ?? 'player');

if (!$name || !$email || !$password || !in_array($role, ['player','coach'])) {
    redirect('../View/signup.php?error=Invalid%20input');
}

// Check if user already exists.
if (findUserByEmail($email)) {
    redirect('../View/signup.php?error=Email%20already%20in%20use');
}

// Create the user. Set to pending until admin approves.
$success = createUser($name, $email, $password, $role);
if ($success) {
    // Notify the admin of a new registration.
    // Find admin user(s). For simplicity we assume the default admin has id=1.
    addNotification(1, 'New ' . $role . ' registration pending approval: ' . $name);
    redirect('../View/login.php?success=Registration%20successful.%20Await%20admin%20approval');
} else {
    redirect('../View/signup.php?error=Registration%20failed');
}

?>