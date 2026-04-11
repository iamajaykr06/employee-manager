<?php
session_start();
require_once 'config/database.php';

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage.php');
    exit;
}

$pdo = getDbConnection();

// Check if employee exists
$stmt = $pdo->prepare("SELECT id, name FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    $_SESSION['message'] = 'Employee not found.';
    header('Location: manage.php');
    exit;
}

// If confirmation is provided via POST, perform deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        try {
            $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['message'] = 'Employee deleted successfully.';
            header('Location: manage.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Failed to delete employee. Database error.';
            error_log('Delete error: ' . $e->getMessage());
        }
    } else {
        header('Location: manage.php');
        exit;
    }
}

include 'includes/header.php';
?>

    <h2>Delete Employee</h2>
    <div class="message error-msg">
        <p><strong>Warning!</strong> You are about to delete the following employee record. This action cannot be undone.</p>
    </div>

    <table>
        <tr><th>ID</th><td><?= htmlspecialchars($employee['id']) ?></td></tr>
        <tr><th>Name</th><td><?= htmlspecialchars($employee['name']) ?></td></tr>
    </table>

    <form method="post">
        <input type="hidden" name="confirm" value="yes">
        <button type="submit" class="btn btn-danger">Yes, Delete Permanently</button>
        <a href="manage.php" class="btn">Cancel</a>
    </form>

<?php
if (isset($error)) {
    echo '<div class="message error-msg">' . htmlspecialchars($error) . '</div>';
}
include 'includes/footer.php';
?>