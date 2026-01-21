<?php
// db.workouts.php
// Data access functions for workouts in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Add a workout log for a player.
 *
 * @param int $player_id
 * @param string $workout_type
 * @param int $duration
 * @param string $intensity
 * @return bool
 */
function addWorkout($player_id, $workout_type, $duration, $intensity) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO workouts (player_id, workout_type, duration, intensity) VALUES (?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('isis', $player_id, $workout_type, $duration, $intensity);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Retrieve all workouts for a player ordered by date descending.
 *
 * @param int $player_id
 * @return array
 */
function getWorkoutsByPlayer($player_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM workouts WHERE player_id = ? ORDER BY log_date DESC");
    if (!$stmt) return [];
    $stmt->bind_param('i', $player_id);
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