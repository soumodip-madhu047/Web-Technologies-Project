<?php
// helpers.php
// Common utility functions for the Athlete Fitness Tracker project.

/**
 * Redirect to a different URL and exit. Use this to prevent any
 * additional output being sent after a redirect.
 *
 * @param string $url Relative or absolute URL to redirect to.
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Sanitize a string by trimming and escaping HTML special characters. Use
 * this on any user‑supplied data that will be output to the browser.
 *
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a base-aware URL for static assets like CSS and JS. This helper
 * determines the project root relative to the current script (handling
 * requests from both the root index and files within the View folder) and
 * concatenates the provided relative path. Use this when constructing
 * href/src attributes in templates to avoid broken links.
 *
 * @param string $path Relative path to the asset (e.g. 'css/style.css').
 * @return string Full URL path starting from the project root.
 */
function asset_url($path) {
    // Current script path, e.g. /athlete-fitness-mvc/index.php or
    // /athlete-fitness-mvc/View/login.php
    $script = $_SERVER['PHP_SELF'];
    // Remove the file portion to get directory, e.g. /athlete-fitness-mvc or /athlete-fitness-mvc/View
    $dir = rtrim(dirname($script), '/');
    // If the current directory includes '/View', strip it to reach the project root
    if (preg_match('#/View$#', $dir)) {
        $dir = rtrim(dirname($dir), '/');
    }
    return $dir . '/' . ltrim($path, '/');
}

/**
 * Determine if the current request method is POST.
 *
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

?>