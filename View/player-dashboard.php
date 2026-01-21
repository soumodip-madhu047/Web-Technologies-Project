<?php
require_once __DIR__ . '/../Model/init.php';
requireRole('player');
$user = currentUser();

// Load data
require_once __DIR__ . '/../Model/db.workouts.php';
require_once __DIR__ . '/../Model/db.measurements.php';
require_once __DIR__ . '/../Model/db.nutrition.php';
require_once __DIR__ . '/../Model/db.plans.php';
require_once __DIR__ . '/../Model/db.messages.php';
require_once __DIR__ . '/../Model/db.users.php';
require_once __DIR__ . '/../Model/db.logs.php';

$workouts = getWorkoutsByPlayer($user['id']);
$measurements = getMeasurementsByPlayer($user['id']);
$nutritionLogs = getNutritionLogsByPlayer($user['id']);
$plans = getPlansByPlayer($user['id']);
$messages = getMessagesForUser($user['id']);
$coaches = getUsersByRole('coach');
$logsSleep = getPlayerLogs($user['id'], 'sleep');
$logsHydration = getPlayerLogs($user['id'], 'hydration');
$logsInjury = getPlayerLogs($user['id'], 'injury');

include __DIR__ . '/layout/header.php';
?>
<h2>Player Dashboard</h2>

<!-- Workouts section -->
<h3 id="workouts">Log Workout</h3>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="add_workout">
    <label for="workout_type">Type</label>
    <input type="text" id="workout_type" name="workout_type" required>
    <label for="duration">Duration (minutes)</label>
    <input type="number" id="duration" name="duration" min="1" required>
    <label for="intensity">Intensity</label>
    <select id="intensity" name="intensity">
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
    </select>
    <button type="submit">Add Workout</button>
</form>

<?php if ($workouts): ?>
    <h3>Workout History</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Type</th><th>Duration</th><th>Intensity</th></tr>
        </thead>
        <tbody>
        <?php foreach ($workouts as $w): ?>
            <tr>
                <td><?php echo date('Y-m-d H:i', strtotime($w['log_date'])); ?></td>
                <td><?php echo htmlspecialchars($w['workout_type']); ?></td>
                <td><?php echo intval($w['duration']); ?> min</td>
                <td><?php echo htmlspecialchars($w['intensity']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Measurements section -->
<h3 id="measurements">Record Measurement</h3>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="add_measurement">
    <label for="weight">Weight (kg)</label>
    <input type="number" step="0.1" id="weight" name="weight" required>
    <label for="body_fat">Body Fat (%)</label>
    <input type="number" step="0.1" id="body_fat" name="body_fat">
    <label for="muscle_mass">Muscle Mass (kg)</label>
    <input type="number" step="0.1" id="muscle_mass" name="muscle_mass">
    <button type="submit">Add Measurement</button>
</form>

<?php if ($measurements): ?>
    <h3>Measurement History</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Weight</th><th>Body Fat</th><th>Muscle Mass</th></tr>
        </thead>
        <tbody>
        <?php foreach ($measurements as $m): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($m['recorded_at'])); ?></td>
                <td><?php echo number_format($m['weight'], 1); ?> kg</td>
                <td><?php echo $m['body_fat'] !== null ? number_format($m['body_fat'], 1) . '%' : '-'; ?></td>
                <td><?php echo $m['muscle_mass'] !== null ? number_format($m['muscle_mass'], 1) . ' kg' : '-'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Nutrition section -->
<h3 id="nutrition">Log Meal</h3>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="add_nutrition">
    <label for="meal">Meal Description</label>
    <input type="text" id="meal" name="meal" required>
    <label for="calories">Calories</label>
    <input type="number" id="calories" name="calories">
    <label for="protein">Protein (g)</label>
    <input type="number" id="protein" name="protein">
    <label for="carbs">Carbs (g)</label>
    <input type="number" id="carbs" name="carbs">
    <label for="fat">Fat (g)</label>
    <input type="number" id="fat" name="fat">
    <button type="submit">Add Meal</button>
</form>

<?php if ($nutritionLogs): ?>
    <h3>Nutrition History</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Meal</th><th>Calories</th><th>Protein</th><th>Carbs</th><th>Fat</th></tr>
        </thead>
        <tbody>
        <?php foreach ($nutritionLogs as $n): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($n['logged_at'])); ?></td>
                <td><?php echo htmlspecialchars($n['meal']); ?></td>
                <td><?php echo $n['calories'] !== null ? intval($n['calories']) : '-'; ?></td>
                <td><?php echo $n['protein'] !== null ? intval($n['protein']) . ' g' : '-'; ?></td>
                <td><?php echo $n['carbs'] !== null ? intval($n['carbs']) . ' g' : '-'; ?></td>
                <td><?php echo $n['fat'] !== null ? intval($n['fat']) . ' g' : '-'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Health Logs section -->
<h3 id="health">Log Sleep, Hydration & Injury</h3>
<form action="../Controller/actions.php" method="post" style="margin-bottom: 1rem;">
    <input type="hidden" name="action" value="add_log">
    <input type="hidden" name="type" value="sleep">
    <label for="sleep_hours">Sleep Hours</label>
    <input type="number" step="0.1" id="sleep_hours" name="value" placeholder="e.g. 7.5">
    <button type="submit">Add Sleep Log</button>
</form>

<form action="../Controller/actions.php" method="post" style="margin-bottom: 1rem;">
    <input type="hidden" name="action" value="add_log">
    <input type="hidden" name="type" value="hydration">
    <label for="hydration_liters">Hydration (L)</label>
    <input type="number" step="0.1" id="hydration_liters" name="value" placeholder="e.g. 2.0">
    <button type="submit">Add Hydration Log</button>
</form>

<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="add_log">
    <input type="hidden" name="type" value="injury">
    <label for="injury_desc">Injury/Notes</label>
    <input type="text" id="injury_desc" name="value" placeholder="Describe injury or notes">
    <button type="submit">Add Injury Log</button>
</form>

<?php if ($logsSleep || $logsHydration || $logsInjury): ?>
    <h3>Health Logs</h3>
    <?php if ($logsSleep): ?>
        <h4>Sleep</h4>
        <table>
            <thead><tr><th>Date</th><th>Hours</th></tr></thead>
            <tbody>
            <?php foreach ($logsSleep as $log): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($log['recorded_at'])); ?></td>
                    <td><?php echo htmlspecialchars($log['value']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if ($logsHydration): ?>
        <h4>Hydration</h4>
        <table>
            <thead><tr><th>Date</th><th>Liters</th></tr></thead>
            <tbody>
            <?php foreach ($logsHydration as $log): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($log['recorded_at'])); ?></td>
                    <td><?php echo htmlspecialchars($log['value']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if ($logsInjury): ?>
        <h4>Injury/Notes</h4>
        <table>
            <thead><tr><th>Date</th><th>Description</th></tr></thead>
            <tbody>
            <?php foreach ($logsInjury as $log): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($log['recorded_at'])); ?></td>
                    <td><?php echo htmlspecialchars($log['value']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<!-- Plans section -->
<?php if ($plans): ?>
    <h3 id="plans">My Plans</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Title</th><th>Coach</th><th>Notes</th></tr>
        </thead>
        <tbody>
        <?php foreach ($plans as $p): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($p['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($p['title']); ?></td>
                <td><?php echo htmlspecialchars($p['coach_name']); ?></td>
                <td><?php echo htmlspecialchars($p['notes']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Messages section -->
<h3 id="messages">Send Message</h3>
<form action="../Controller/actions.php" method="post">
    <input type="hidden" name="action" value="send_message">
    <label for="receiver_id">To</label>
    <select id="receiver_id" name="receiver_id" required>
        <?php foreach ($coaches as $coach): ?>
            <option value="<?php echo $coach['id']; ?>"><?php echo htmlspecialchars($coach['name']); ?></option>
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
        <thead>
            <tr><th>Date</th><th>From</th><th>Subject</th><th>Message</th></tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $msg): ?>
            <?php markMessageRead($msg['id']); /* mark as read */ ?>
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