<?php
require_once __DIR__ . '/../Model/init.php';
requireRole('coach');
$user = currentUser();

// Load data
require_once __DIR__ . '/../Model/db.users.php';
require_once __DIR__ . '/../Model/db.plans.php';
require_once __DIR__ . '/../Model/db.messages.php';
require_once __DIR__ . '/../Model/db.exercises.php';

$players = getUsersByRole('player');
$plans = getPlansByCoach($user['id']);
$messages = getMessagesForUser($user['id']);
$exercises = getAllExercises();

include __DIR__ . '/layout/header.php';
?>
<h2>Coach Dashboard</h2>

<!-- Players section -->
<h3 id="players">Create Training Plan</h3>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="create_plan">
    <label for="player_id">Select Player</label>
    <select id="player_id" name="player_id" required>
        <?php foreach ($players as $p): ?>
            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <label for="title">Title</label>
    <input type="text" id="title" name="title" required>
    <label for="notes">Notes</label>
    <textarea id="notes" name="notes" rows="3"></textarea>
    <button type="submit">Create Plan</button>
</form>

<?php if ($plans): ?>
    <h3 id="plans">My Plans</h3>
    <table>
        <thead><tr><th>Date</th><th>Title</th><th>Player</th><th>Notes</th></tr></thead>
        <tbody>
        <?php foreach ($plans as $pl): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($pl['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($pl['title']); ?></td>
                <td><?php echo htmlspecialchars($pl['player_name']); ?></td>
                <td><?php echo htmlspecialchars($pl['notes']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Exercises section -->
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

<!-- Add exercise form (coaches can add) -->
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

<!-- Messages -->
<h3 id="messages">Send Message</h3>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="send_message">
    <label for="receiver_id">To Player</label>
    <select id="receiver_id" name="receiver_id" required>
        <?php foreach ($players as $plr): ?>
            <option value="<?php echo $plr['id']; ?>"><?php echo htmlspecialchars($plr['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <label for="subject">Subject</label>
    <input type="text" id="subject" name="subject" required>
    <label for="body">Message</label>
    <textarea id="body" name="body" rows="3" required></textarea>
    <button type="submit">Send</button>
</form>

<?php if ($messages): ?>
    <h3>Inbox</h3>
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