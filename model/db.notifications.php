<?php
// db.notifications.php
// Data access functions for notifications in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Create a notification for a user.
 *
 * @param int $user_id
 * @param string $content
 * @return bool
 */
function addNotification($user_id, $content) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO notifications (user_id, content) VALUES (?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('is', $user_id, $content);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get unread notifications for a user. Also returns the count.
 *
 * @param int $user_id
 * @return array
 */
function getUnreadNotifications($user_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    if (!$stmt) return [];
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();
    return $notifications;
}

/**
 * Mark all notifications for a user as read.
 *
 * @param int $user_id
 * @return bool
 */
function markNotificationsRead($user_id) {
    $db = getDb();
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    if (!$stmt) return false;
    $stmt->bind_param('i', $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>