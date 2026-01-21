<?php
// db.measurements.php
// Data access functions for body measurements in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Add a measurement entry for a player.
 *
 * @param int $player_id
 * @param float $weight
 * @param float|null $body_fat
 * @param float|null $muscle_mass
 * @return bool
 */
function addMeasurement($player_id, $weight, $body_fat, $muscle_mass) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO measurements (player_id, weight, body_fat, muscle_mass) VALUES (?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('iddd', $player_id, $weight, $body_fat, $muscle_mass);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get all measurements for a player ordered by date descending.
 *
 * @param int $player_id
 * @return array
 */
function getMeasurementsByPlayer($player_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM measurements WHERE player_id = ? ORDER BY recorded_at DESC");
    if (!$stmt) return [];
    $stmt->bind_param('i', $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
    $stmt->close();
    return $list;
}

?>