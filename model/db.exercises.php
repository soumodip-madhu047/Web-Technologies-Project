<?php
// db.exercises.php
// Data access functions for the exercise library in the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';

/**
 * Retrieve all exercises ordered by name.
 *
 * @return array
 */
function getAllExercises() {
    $db = getDb();
    $result = $db->query("SELECT * FROM exercises ORDER BY name ASC");
    $exercises = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $exercises[] = $row;
        }
    }
    return $exercises;
}

/**
 * Add a new exercise to the library. Only coaches and admins should
 * call this function.
 *
 * @param string $name
 * @param string $description
 * @param string|null $muscle_group
 * @param int|null $created_by
 * @return bool
 */
function addExercise($name, $description, $muscle_group, $created_by) {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO exercises (name, description, muscle_group, created_by) VALUES (?, ?, ?, ?)");
    if (!$stmt) return false;
    $stmt->bind_param('sssi', $name, $description, $muscle_group, $created_by);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

?>