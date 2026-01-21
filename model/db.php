<?php
// db.php
// Database connection helper for the Athlete Fitness Tracker project.

// Update these variables if your database configuration differs.
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'athlete_fitness_final';

/**
 * Get a mysqli database connection. If the connection fails the script
 * will exit and display an error. Use this helper so that all models
 * share the same connection code.
 *
 * @return mysqli
 */
function getDb() {
    static $db;
    if ($db === null) {
        // Pull in global variables within the function scope.
        global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
        $db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        if ($db->connect_error) {
            die('Database connection failed: ' . $db->connect_error);
        }
        // Set utf8mb4 encoding for full Unicode support.
        $db->set_charset('utf8mb4');
    }
    return $db;
}

?>