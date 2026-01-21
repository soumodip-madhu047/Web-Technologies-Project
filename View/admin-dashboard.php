<?php
require_once __DIR__ . '/../Model/init.php';
requireRole('admin');
$user = currentUser();

require_once __DIR__ . '/../Model/db.users.php';
require_once __DIR__ . '/../Model/db.exercises.php';
require_once __DIR__ . '/../Model/db.messages.php';

$pending = getPendingUsers();
$exercises = getAllExercises();
$messages = getMessagesForUser($user['id']);

include __DIR__ . '/layout/header.php';
?>
<h2>Admin Dashboard</h2>

<!-- Pending users -->
<h3 id="users">Pending Users</h3>
<?php if ($pending): ?>
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($pending as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['email']); ?></td>
                <td><?php echo htmlspecialchars($p['role']); ?></td>
                <td>
                    <form action="../Controller/actions.php" method="post" style="display:inline;">
                        <input type="hidden" name="action" value="approve_user">
                        <input type="hidden" name="user_id" value="<?php echo $p['id']; ?>">
                        <button type="submit">Approve</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No pending users.</p>
<?php endif; ?>

<!-- Exercises library -->
<h3 id="exercises">Exercise Library</h3>
<?php if ($exercises): ?>
    <table>
        <thead><tr><th>Name</th><th>Muscle Group</th><th>Description</th></tr></thead>
        <tbody>
        <?php foreach ($exercises as $ex): ?>
            <tr>
                <td><?php echo htmlspecialchars($ex['name']); ?></td>
                <td><?php echo htmlspecialchars($ex['muscle_group']); ?></td>
                <td><?php echo htmlspecialchars($ex['description']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No exercises available.</p>
<?php endif; ?>

<!-- Add exercise -->
<h4>Add Exercise</h4>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="add_exercise">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required>
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"></textarea>
    <label for="muscle_group">Muscle Group</label>
    <input type="text" id="muscle_group" name="muscle_group">
    <button type="submit">Add Exercise</button>
</form>

<!-- Messages section -->
<?php if ($messages): ?>
    <h3>Messages</h3>
    <table>
        <thead><tr><th>Date</th><th>From</th><th>Subject</th><th>Message</th></tr></thead>
        <tbody>
        <?php foreach ($messages as $msg): ?>
            <?php markMessageRead($msg['id']); ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($msg['sent_at'])); ?></td>
                <td><?php echo htmlspecialchars($msg['sender_name']); ?></td>
                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                <td><?php echo htmlspecialchars($msg['body']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>