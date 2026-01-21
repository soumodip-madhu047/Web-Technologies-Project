<?php
// auth.php
// Authentication helpers for the Athlete Fitness Tracker project.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/db.user_tokens.php';
require_once __DIR__ . '/db.users.php';

/**
 * Start a session if one hasn't already been started. This should be
 * called before accessing the $_SESSION superglobal.
 */
function ensureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Use strict mode to mitigate session fixation attacks.
        ini_set('session.use_strict_mode', 1);
        session_start();
    }
}

/**
 * Attempt to automatically log in a user using the remember me cookie.
 * If a valid token exists, the user's session is initialized. This
 * function should be called on every request via init.php.
 */
function autoLoginFromCookie() {
    ensureSession();
    if (!empty($_SESSION['user'])) {
        return;
    }
    if (isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];
        $tokenRow = getToken($token);
        if ($tokenRow && strtotime($tokenRow['expires_at']) > time()) {
            $user = getUserById($tokenRow['user_id']);
            if ($user) {
                loginUser($user, false);
            }
        } else {
            // Token expired or invalid; delete the cookie.
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }
}

/**
 * Log in a user by storing their data in the session and optionally
 * creating a persistent login token. Do not call this directly on
 * untrusted data—validate credentials first in loginCheck.php.
 *
 * @param array $user Associative array of user fields.
 * @param bool $remember Whether to issue a persistent login token.
 */
function loginUser($user, $remember = false) {
    ensureSession();
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'status' => $user['status']
    ];
    if ($remember) {
        // Generate a secure random token.
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        // Persist the token in the database and set the cookie.
        createToken($user['id'], $token, $expires);
        setcookie('remember_me', $token, time() + 60 * 60 * 24 * 30, '/');
    }
}

/**
 * Log out the current user by clearing the session and any persistent
 * login cookie. All associated tokens for the user are removed.
 */
function logoutUser() {
    ensureSession();
    if (!empty($_SESSION['user'])) {
        $userId = $_SESSION['user']['id'];
        deleteTokensForUser($userId);
    }
    // Clear session and cookie.
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    setcookie('remember_me', '', time() - 3600, '/');
    session_destroy();
}

/**
 * Ensure the user is logged in. If not, attempt an auto login from
 * the remember me cookie; if still not logged in, redirect to login.
 */
function requireLogin() {
    ensureSession();
    if (empty($_SESSION['user'])) {
        autoLoginFromCookie();
        if (empty($_SESSION['user'])) {
            redirect('../View/login.php');
        }
    }
}

/**
 * Ensure the current user has the specified role. Redirects to the
 * appropriate dashboard if the role requirement is not met.
 *
 * @param string $role
 */
function requireRole($role) {
    requireLogin();
    $user = $_SESSION['user'];
    if ($user['role'] !== $role) {
        // Redirect to the user's own dashboard.
        switch ($user['role']) {
            case 'player':
                redirect('../View/player-dashboard.php');
                break;
            case 'coach':
                redirect('../View/coach-dashboard.php');
                break;
            case 'admin':
            default:
                redirect('../View/admin-dashboard.php');
                break;
        }
    }
}

/**
 * Return the currently logged-in user's information or null if not
 * logged in. Note that it may not include all user fields; for
 * complete data use getUserById().
 *
 * @return array|null
 */
function currentUser() {
    ensureSession();
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

?>