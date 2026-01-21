<?php
// index.php
// Entry point for the Athlete Fitness Tracker application. This file
// performs user redirection based on login status or displays the
// public home page.

require_once __DIR__ . '/Model/init.php';

$user = currentUser();
if ($user) {
    // Redirect logged in users to their dashboard
    switch ($user['role']) {
        case 'player':
            redirect('View/player-dashboard.php');
            break;
        case 'coach':
            redirect('View/coach-dashboard.php');
            break;
        case 'admin':
        default:
            redirect('View/admin-dashboard.php');
            break;
    }
} else {
    // Show the public home page
    include __DIR__ . '/View/home.php';
}