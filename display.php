<?php
session_start();
$data = $_GET;

// Optionally show session data
$sessionData = $_SESSION['last_employee'] ?? null;
$cookieTime = $_COOKIE['last_submission'] ?? 'Not set';

include 'includes/header.php';
?>

    <h2>Submission Successful</h2>
    <div class="message success">Employee record added successfully!</div>

    <h3>Employee Details (via GET)</h3>
    <table>
        <tr><th>Field</th><th>Value</th></tr>
        <tr><td>ID</td><td><?= htmlspecialchars($data['id'] ?? 'N/A') ?></td></tr>
        <tr><td>Name</td><td><?= htmlspecialchars($data['name'] ?? '') ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($data['email'] ?? '') ?></td></tr>
        <tr><td>Gender</td><td><?= htmlspecialchars($data['gender'] ?? '') ?></td></tr>
        <tr><td>Hobbies</td><td><?= htmlspecialchars($data['hobbies'] ?? '') ?></td></tr>
        <tr><td>Salary</td><td>₹<?= htmlspecialchars(number_format($data['salary'] ?? 0, 2)) ?></td></tr>
    </table>

    <h3>Session Data</h3>
<?php if ($sessionData): ?>
    <p>Name from session: <?= htmlspecialchars($sessionData['name']) ?></p>
<?php else: ?>
    <p>No session data found.</p>
<?php endif; ?>

    <h3>Cookie</h3>
    <p>Last submission timestamp: <?= htmlspecialchars($cookieTime) ?></p>

    <p><a href="manage.php" class="btn">View All Employees</a></p>

<?php include 'includes/footer.php'; ?>