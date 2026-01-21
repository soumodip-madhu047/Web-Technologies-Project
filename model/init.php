<?php
// init.php
// Include this file at the top of every controller and view. It sets
// up the environment by including helper functions, starting the
// session and performing auto login via remember me cookies.

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

// Start session and attempt auto login from cookie.
autoLoginFromCookie();

?>