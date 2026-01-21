<?php
// ajax.php
// Handles AJAX requests such as notifications in the Athlete Fitness Tracker project.

require_once __DIR__ . '/../Model/init.php';
require_once __DIR__ . '/../Model/db.notifications.php';

ensureSession();
$user = currentUser();
if (!$user) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$action = $_GET['action'] ?? '';
switch ($action) {
    case 'get_notifications':
        $notifs = getUnreadNotifications($user['id']);
        $count = count($notifs);
        // Return at most the 5 latest notifications for display.
        $list = array_slice($notifs, 0, 5);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count, 'notifications' => $list]);
        break;
    case 'mark_read':
        markNotificationsRead($user['id']);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unknown action']);
        break;
}

?>