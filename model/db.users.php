<?php
// db.users.php
// User data access functions for the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Find a user by their email address.
 *
 * @param string $email
 * @return array|null
 */
function findUserByEmail($email) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Retrieve a user by ID.
 *
 * @param int $id
 * @return array|null
 */
function getUserById($id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Create a new user record. Users are created with pending status
 * until an admin approves them.
 *
 * @param string $name
 * @param string $email
 * @param string $password Plain text password; will be hashed.
 * @param string $role 'player' or 'coach'
 * @return bool
 */
function createUser($name, $email, $password, $role) {
    $db = getDb();
    $hash = md5($password); // simple MD5 for demonstration; use stronger hashing in real apps
    $status = 'pending';
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('sssss', $name, $email, $hash, $role, $status);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Approve a pending user. Only admins should call this.
 *
 * @param int $id
 * @return bool
 */
function approveUser($id) {
    $db = getDb();
    $stmt = $db->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
    if (!$stmt) return false;
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get all users with status 'pending'.
 *
 * @return array
 */
function getPendingUsers() {
    $db = getDb();
    $result = $db->query("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at ASC");
    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

/**
 * Get all users by role (optionally only approved). Useful for listing
 * players when creating plans.
 *
 * @param string $role
 * @param bool $approvedOnly
 * @return array
 */
function getUsersByRole($role, $approvedOnly = true) {
    $db = getDb();
    $query = "SELECT * FROM users WHERE role = ?";
    if ($approvedOnly) {
        $query .= " AND status = 'approved'";
    }
    $query .= " ORDER BY name ASC";
    $stmt = $db->prepare($query);
    if (!$stmt) return [];
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    return $users;
}

?>