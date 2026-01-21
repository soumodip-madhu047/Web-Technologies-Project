<?php
// db.user_tokens.php
// Functions for managing persistent login tokens (remember me).

require_once __DIR__ . '/db.php';

/**
 * Store a new remember me token for a user.
 *
 * @param int $user_id
 * @param string $token
 * @param string $expires_at
 * @return bool
 */
function createToken($user_id, $token, $expires_at) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('iss', $user_id, $token, $expires_at);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Retrieve a token record by the token string.
 *
 * @param string $token
 * @return array|null
 */
function getToken($token) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM user_tokens WHERE token = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Delete all tokens associated with a user. Useful on logout.
 *
 * @param int $user_id
 * @return bool
 */
function deleteTokensForUser($user_id) {
    $db = getDb();
    $stmt = $db->prepare("DELETE FROM user_tokens WHERE user_id = ?");
    if (!$stmt) return false;
    $stmt->bind_param('i', $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>