<?php
// db.nutrition.php
// Data access functions for nutrition logs in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Add a nutrition log entry for a player.
 *
 * @param int $player_id
 * @param string $meal
 * @param int|null $calories
 * @param int|null $protein
 * @param int|null $carbs
 * @param int|null $fat
 * @return bool
 */
function addNutrition($player_id, $meal, $calories, $protein, $carbs, $fat) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO nutrition_logs (player_id, meal, calories, protein, carbs, fat) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('isiiii', $player_id, $meal, $calories, $protein, $carbs, $fat);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get all nutrition logs for a player ordered by date descending.
 *
 * @param int $player_id
 * @return array
 */
function getNutritionLogsByPlayer($player_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM nutrition_logs WHERE player_id = ? ORDER BY logged_at DESC");
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