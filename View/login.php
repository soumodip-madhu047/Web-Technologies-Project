<?php
require_once __DIR__ . '/../Model/init.php';
$user = currentUser();
if ($user) {
    // Already logged in
    switch ($user['role']) {
        case 'player':
            redirect('player-dashboard.php');
            break;
        case 'coach':
            redirect('coach-dashboard.php');
            break;
        case 'admin':
        default:
            redirect('admin-dashboard.php');
            break;
    }
}
include __DIR__ . '/layout/header.php';

// Capture messages from query parameters
$error = isset($_GET['error']) ? sanitize($_GET['error']) : '';
$success = isset($_GET['success']) ? sanitize($_GET['success']) : '';
?>
<h2>Login</h2>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="message success"><?php echo $success; ?></div>
<?php endif; ?>
<form action="../Controller/loginCheck.php" method="post">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <div>
        <label><input type="checkbox" name="remember"> Remember me</label>
    </div>

    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="signup.php">Sign up</a></p>
<?php include __DIR__ . '/layout/footer.php'; ?>