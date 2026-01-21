<?php
// db.messages.php
// Data access functions for messages in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/db.notifications.php';
require_once __DIR__ . '/db.users.php';

/**
 * Send a message from one user to another. A notification will also be
 * created for the receiver.
 *
 * @param int $sender_id
 * @param int $receiver_id
 * @param string $subject
 * @param string $body
 * @return bool
 */
function sendMessage($sender_id, $receiver_id, $subject, $body) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('iiss', $sender_id, $receiver_id, $subject, $body);
    $result = $stmt->execute();
    $stmt->close();
    if ($result) {
        $content = 'New message from ' . getUserById($sender_id)['name'] . ': ' . $subject;
        addNotification($receiver_id, $content);
    }
    return $result;
}

/**
 * Get all messages for a user (as receiver) ordered by date descending.
 *
 * @param int $user_id
 * @return array
 */
function getMessagesForUser($user_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT m.*, u.name AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? ORDER BY m.sent_at DESC");
    if (!$stmt) return [];
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
    return $messages;
}

/**
 * Mark a specific message as read.
 *
 * @param int $message_id
 * @return bool
 */
function markMessageRead($message_id) {
    $db = getDb();
    $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    if (!$stmt) return false;
    $stmt->bind_param('i', $message_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>