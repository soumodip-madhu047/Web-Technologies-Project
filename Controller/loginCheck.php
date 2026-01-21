<?php
// loginCheck.php
// Process login form submissions for the Athlete Fitness Tracker.

require_once __DIR__ . '/../Model/init.php';
require_once __DIR__ . '/../Model/db.users.php';

// Ensure this is a POST request.
if (!isPost()) {
    redirect('../View/login.php');
}

$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Look up the user by email.
$user = findUserByEmail($email);
if (!$user || md5($password) !== $user['password']) {
    redirect('../View/login.php?error=Invalid%20email%20or%20password');
}
// Check approval status.
if ($user['status'] !== 'approved') {
    redirect('../View/login.php?error=Account%20pending%20approval');
}

// Successful login: start session and redirect to dashboard.
loginUser($user, $remember);
switch ($user['role']) {
    case 'player':
        redirect('../View/player-dashboard.php');
        break;
    case 'coach':
        redirect('../View/coach-dashboard.php');
        break;
    case 'admin':
    default:
        redirect('../View/admin-dashboard.php');
        break;
}

?>