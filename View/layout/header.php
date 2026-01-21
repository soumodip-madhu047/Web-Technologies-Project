<?php
// header.php
// Common header for all pages in the Athlete Fitness Tracker project.
// It displays the site title and navigation menu. The menu changes
// depending on whether the user is logged in and their role. A
// notification badge shows the number of unread notifications.

// currentUser() comes from init.php which should be included before
// including this header.
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Fitness Tracker</title>
    <!-- Load CSS and JS using asset_url helper to compute the correct path
         regardless of whether this page is rendered from the root index or a
         file within the View directory. -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <script src="<?php echo asset_url('js/main.js'); ?>" defer></script>
</head>
<body>
<header>
    <h1>Athlete Fitness Tracker</h1>
    <nav>
        <ul>
            <?php if (!$user): ?>
                <li><a href="<?php echo asset_url('View/login.php'); ?>">Login</a></li>
                <li><a href="<?php echo asset_url('View/signup.php'); ?>">Sign&nbsp;Up</a></li>
            <?php else: ?>
                <li><a href="<?php echo asset_url('View/' . $user['role'] . '-dashboard.php'); ?>">Dashboard</a></li>
                <?php if ($user['role'] === 'player'): ?>
                    <li><a href="<?php echo asset_url('View/player-dashboard.php'); ?>#workouts">Workouts</a></li>
                    <li><a href="<?php echo asset_url('View/player-dashboard.php'); ?>#measurements">Measurements</a></li>
                    <li><a href="<?php echo asset_url('View/player-dashboard.php'); ?>#nutrition">Nutrition</a></li>
                    <li><a href="<?php echo asset_url('View/player-dashboard.php'); ?>#health">Health</a></li>
                    <li><a href="<?php echo asset_url('View/player-dashboard.php'); ?>#plans">Plans</a></li>
                    <li><a href="<?php echo asset_url('View/player-dashboard.php'); ?>#messages">Messages</a></li>
                <?php elseif ($user['role'] === 'coach'): ?>
                    <li><a href="<?php echo asset_url('View/coach-dashboard.php'); ?>#players">Players</a></li>
                    <li><a href="<?php echo asset_url('View/coach-dashboard.php'); ?>#plans">Plans</a></li>
                    <li><a href="<?php echo asset_url('View/coach-dashboard.php'); ?>#messages">Messages</a></li>
                    <li><a href="<?php echo asset_url('View/coach-dashboard.php'); ?>#exercises">Exercises</a></li>
                <?php elseif ($user['role'] === 'admin'): ?>
                    <li><a href="<?php echo asset_url('View/admin-dashboard.php'); ?>#users">Users</a></li>
                    <li><a href="<?php echo asset_url('View/admin-dashboard.php'); ?>#exercises">Exercises</a></li>
                <?php endif; ?>
                <li class="notif-icon">
                    <span>🔔</span>
                    <span class="notif-count">0</span>
                </li>
                <li><a href="<?php echo asset_url('Controller/logout.php'); ?>">Logout&nbsp;(<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>)</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<div class="container">