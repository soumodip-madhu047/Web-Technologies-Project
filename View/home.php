<?php
require_once __DIR__ . '/../Model/init.php';
$user = currentUser();
if ($user) {
    // Redirect logged-in users to their dashboard.
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
?>
<h2>Welcome to the Athlete Fitness Tracker</h2>
<p>This application helps players track workouts, measurements and nutrition, coaches manage training plans, and admins oversee the system.</p>
<p>Please <a href="<?php echo asset_url('View/login.php'); ?>">log in</a> or <a href="<?php echo asset_url('View/signup.php'); ?>">create an account</a> to get started.</p>
<?php include __DIR__ . '/layout/footer.php'; ?>