<?php
// logout.php
// Log out the current user and redirect to login.

require_once __DIR__ . '/../Model/init.php';

logoutUser();

redirect('../View/login.php?success=Logged%20out');

?>