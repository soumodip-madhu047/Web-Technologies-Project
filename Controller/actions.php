<?php
// actions.php
// Central handler for form POST actions in the Athlete Fitness Tracker.

require_once __DIR__ . '/../Model/init.php';
require_once __DIR__ . '/../Model/db.users.php';
require_once __DIR__ . '/../Model/db.workouts.php';
require_once __DIR__ . '/../Model/db.measurements.php';
require_once __DIR__ . '/../Model/db.nutrition.php';
require_once __DIR__ . '/../Model/db.plans.php';
require_once __DIR__ . '/../Model/db.exercises.php';
require_once __DIR__ . '/../Model/db.messages.php';
require_once __DIR__ . '/../Model/db.notifications.php';
require_once __DIR__ . '/../Model/db.logs.php';

requireLogin();
$user = currentUser();

if (!isPost()) {
    redirect('../View/' . $user['role'] . '-dashboard.php');
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_workout':
        // Player only
        if ($user['role'] !== 'player') {
            break;
        }
        $type = sanitize($_POST['workout_type'] ?? '');
        $duration = intval($_POST['duration'] ?? 0);
        $intensity = sanitize($_POST['intensity'] ?? 'Medium');
        if ($type && $duration > 0 && in_array($intensity, ['Low','Medium','High'])) {
            addWorkout($user['id'], $type, $duration, $intensity);
        }
        redirect('../View/player-dashboard.php');
        break;

    case 'add_measurement':
        if ($user['role'] !== 'player') {
            break;
        }
        $weight = floatval($_POST['weight'] ?? 0);
        $body_fat = isset($_POST['body_fat']) && $_POST['body_fat'] !== '' ? floatval($_POST['body_fat']) : null;
        $muscle = isset($_POST['muscle_mass']) && $_POST['muscle_mass'] !== '' ? floatval($_POST['muscle_mass']) : null;
        if ($weight > 0) {
            addMeasurement($user['id'], $weight, $body_fat, $muscle);
        }
        redirect('../View/player-dashboard.php');
        break;

    case 'add_nutrition':
        if ($user['role'] !== 'player') {
            break;
        }
        $meal = sanitize($_POST['meal'] ?? '');
        $cal = $_POST['calories'] === '' ? null : intval($_POST['calories']);
        $protein = $_POST['protein'] === '' ? null : intval($_POST['protein']);
        $carbs = $_POST['carbs'] === '' ? null : intval($_POST['carbs']);
        $fat = $_POST['fat'] === '' ? null : intval($_POST['fat']);
        if ($meal) {
            addNutrition($user['id'], $meal, $cal, $protein, $carbs, $fat);
        }
        redirect('../View/player-dashboard.php');
        break;

    case 'create_plan':
        if ($user['role'] !== 'coach') {
            break;
        }
        $player_id = intval($_POST['player_id'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        if ($player_id > 0 && $title) {
            $planId = addPlan($user['id'], $player_id, $title, $notes);
            if ($planId) {
                // Notify player of new plan.
                addNotification($player_id, 'New training plan: ' . $title);
            }
        }
        redirect('../View/coach-dashboard.php');
        break;

    case 'approve_user':
        if ($user['role'] !== 'admin') {
            break;
        }
        $uid = intval($_POST['user_id'] ?? 0);
        if ($uid > 0) {
            approveUser($uid);
            addNotification($uid, 'Your account has been approved');
        }
        redirect('../View/admin-dashboard.php');
        break;

    case 'send_message':
        // All roles can send messages.
        $receiver = intval($_POST['receiver_id'] ?? 0);
        $subject = sanitize($_POST['subject'] ?? '');
        $body = sanitize($_POST['body'] ?? '');
        if ($receiver > 0 && $subject && $body) {
            sendMessage($user['id'], $receiver, $subject, $body);
        }
        // Redirect back to dashboard.
        redirect('../View/' . $user['role'] . '-dashboard.php');
        break;

    case 'add_exercise':
        if (!in_array($user['role'], ['admin','coach'])) {
            break;
        }
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $muscle_group = sanitize($_POST['muscle_group'] ?? '');
        if ($name) {
            addExercise($name, $description, $muscle_group, $user['id']);
        }
        redirect('../View/' . $user['role'] . '-dashboard.php');
        break;

    case 'add_log':
        // Handle health log entries (sleep, hydration, injury) for players
        if ($user['role'] !== 'player') {
            break;
        }
        $type = sanitize($_POST['type'] ?? '');
        $value = trim($_POST['value'] ?? '');
        // Only process recognized log types
        if (in_array($type, ['sleep','hydration','injury']) && $value !== '') {
            // For numeric logs, convert to float to ensure valid numeric input
            if (in_array($type, ['sleep','hydration'])) {
                // Cast numeric values; if invalid, set to null to ignore
                $num = floatval($value);
                if ($num > 0) {
                    $value = $num;
                }
            }
            addPlayerLog($user['id'], $type, $value);
        }
        redirect('../View/player-dashboard.php');
        break;

    default:
        // Unhandled action; redirect to appropriate dashboard.
        redirect('../View/' . $user['role'] . '-dashboard.php');
        break;
}

?>