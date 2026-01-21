<?php
require_once __DIR__ . '/../Model/init.php';
$user = currentUser();
if ($user) {
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
// Get messages
$error = isset($_GET['error']) ? sanitize($_GET['error']) : '';
$success = isset($_GET['success']) ? sanitize($_GET['success']) : '';
?>
<h2>Sign Up</h2>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="message success"><?php echo $success; ?></div>
<?php endif; ?>
<form action="../Controller/signupCheck.php" method="post">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <label for="role">Role</label>
    <select id="role" name="role" required>
        <option value="player">Player</option>
        <option value="coach">Coach</option>
    </select>

    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Log in</a></p>
<?php include __DIR__ . '/layout/footer.php'; ?>