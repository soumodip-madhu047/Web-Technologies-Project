// main.js
// Client‑side behaviours for the Athlete Fitness Tracker.

// Fetch unread notifications and update the badge. Runs on load and
// periodically thereafter. Uses the Fetch API.
function fetchNotifications() {
    const badge = document.querySelector('.notif-count');
    if (!badge) return;
    fetch('../Controller/ajax.php?action=get_notifications')
        .then((response) => response.json())
        .then((data) => {
            if (data && typeof data.count === 'number') {
                badge.textContent = data.count;
            }
        })
        .catch((error) => {
            console.error('Error fetching notifications:', error);
        });
}

// Mark all notifications as read.
function markNotificationsRead() {
    fetch('../Controller/ajax.php?action=mark_read')
        .then((response) => response.json())
        .then(() => {
            fetchNotifications();
        })
        .catch((error) => {
            console.error('Error marking notifications read:', error);
        });
}

document.addEventListener('DOMContentLoaded', () => {
    fetchNotifications();
    // Poll for notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
    const notifIcon = document.querySelector('.notif-icon');
    if (notifIcon) {
        notifIcon.addEventListener('click', (e) => {
            e.preventDefault();
            markNotificationsRead();
        });
    }
});
