<?php
// db.plans.php
// Data access functions for training plans in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Create a new plan. Returns the newly inserted plan ID or false on failure.
 *
 * @param int $coach_id
 * @param int $player_id
 * @param string $title
 * @param string $notes
 * @return int|false
 */
function addPlan($coach_id, $player_id, $title, $notes) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO plans (coach_id, player_id, title, notes) VALUES (?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('iiss', $coach_id, $player_id, $title, $notes);
    $result = $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $result ? $id : false;
}

/**
 * Add an exercise to a plan with sets and reps. This function is
 * optional for demonstration and may not be used in the UI.
 *
 * @param int $plan_id
 * @param int $exercise_id
 * @param int|null $sets
 * @param int|null $reps
 * @param string|null $notes
 * @return bool
 */
function addExerciseToPlan($plan_id, $exercise_id, $sets, $reps, $notes) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO plan_exercises (plan_id, exercise_id, sets, reps, notes) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('iiiss', $plan_id, $exercise_id, $sets, $reps, $notes);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Get all plans assigned to a player.
 *
 * @param int $player_id
 * @return array
 */
function getPlansByPlayer($player_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT p.*, u.name AS coach_name FROM plans p JOIN users u ON p.coach_id = u.id WHERE p.player_id = ? ORDER BY p.created_at DESC");
    if (!$stmt) return [];
    $stmt->bind_param('i', $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plans = [];
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
    $stmt->close();
    return $plans;
}

/**
 * Get all plans created by a coach.
 *
 * @param int $coach_id
 * @return array
 */
function getPlansByCoach($coach_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT p.*, u.name AS player_name FROM plans p JOIN users u ON p.player_id = u.id WHERE p.coach_id = ? ORDER BY p.created_at DESC");
    if (!$stmt) return [];
    $stmt->bind_param('i', $coach_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plans = [];
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
    $stmt->close();
    return $plans;
}

/**
 * Get detailed information about a plan including its exercises.
 *
 * @param int $plan_id
 * @return array|null
 */
function getPlanDetails($plan_id) {
    $db = getDb();
    // Fetch plan itself
    $stmt = $db->prepare("SELECT * FROM plans WHERE id = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param('i', $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plan = $result->fetch_assoc();
    $stmt->close();
    if (!$plan) return null;
    // Fetch exercises for plan
    $stmt2 = $db->prepare("SELECT pe.*, e.name, e.description FROM plan_exercises pe JOIN exercises e ON pe.exercise_id = e.id WHERE pe.plan_id = ?");
    if ($stmt2) {
        $stmt2->bind_param('i', $plan_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $plan['exercises'] = [];
        while ($row = $res2->fetch_assoc()) {
            $plan['exercises'][] = $row;
        }
        $stmt2->close();
    } else {
        $plan['exercises'] = [];
    }
    return $plan;
}

?>