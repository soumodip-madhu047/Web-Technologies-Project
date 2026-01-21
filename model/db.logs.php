<?php
// db.logs.php
// Manage sleep, hydration, and injury logs for players.

require_once __DIR__ . '/db.php';

/**
 * Add a player log entry.
 *
 * @param int $player_id
 * @param string $type 'sleep', 'hydration', or 'injury'
 * @param string $value Sleep hours or hydration amount or injury description
 * @return bool
 */
function addPlayerLog($player_id, $type, $value) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO player_logs (player_id, log_type, value) VALUES (?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('iss', $player_id, $type, $value);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Retrieve logs for a player optionally filtered by type.
 *
 * @param int $player_id
 * @param string|null $type
 * @return array
 */
function getPlayerLogs($player_id, $type = null) {
    $db = getDb();
    if ($type) {
        $stmt = $db->prepare("SELECT * FROM player_logs WHERE player_id = ? AND log_type = ? ORDER BY recorded_at DESC");
        if (!$stmt) return [];
        $stmt->bind_param('is', $player_id, $type);
    } else {
        $stmt = $db->prepare("SELECT * FROM player_logs WHERE player_id = ? ORDER BY recorded_at DESC");
        if (!$stmt) return [];
        $stmt->bind_param('i', $player_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    $stmt->close();
    return $logs;
}

?>