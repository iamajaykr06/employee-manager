<?php
session_start();
require_once 'config/database.php';

$pdo = getDbConnection();
$results = [];
$searchPerformed = false;
$error = '';

// Get all distinct departments for dropdown
$deptStmt = $pdo->query("SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != ''");
$departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
    $searchPerformed = true;

    $name = trim($_GET['name'] ?? '');
    $email = trim($_GET['email'] ?? '');
    $gender = $_GET['gender'] ?? '';
    $minSalary = $_GET['min_salary'] ?? '';
    $maxSalary = $_GET['max_salary'] ?? '';
    $department = $_GET['department'] ?? '';
    $hobby = $_GET['hobby'] ?? '';

    // Build dynamic WHERE clause
    $where = [];
    $params = [];

    if (!empty($name)) {
        $where[] = "name LIKE ?";
        $params[] = "%$name%";
    }
    if (!empty($email)) {
        $where[] = "email LIKE ?";
        $params[] = "%$email%";
    }
    if (!empty($gender)) {
        $where[] = "gender = ?";
        $params[] = $gender;
    }
    if (!empty($minSalary) && is_numeric($minSalary)) {
        $where[] = "salary >= ?";
        $params[] = $minSalary;
    }
    if (!empty($maxSalary) && is_numeric($maxSalary)) {
        $where[] = "salary <= ?";
        $params[] = $maxSalary;
    }
    if (!empty($department)) {
        $where[] = "department = ?";
        $params[] = $department;
    }
    if (!empty($hobby)) {
        $where[] = "FIND_IN_SET(?, hobbies) > 0";
        $params[] = $hobby;
    }

    $sql = "SELECT * FROM employees";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY id ASC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Search query failed.';
        error_log('Search error: ' . $e->getMessage());
    }
}

include 'includes/header.php';
?>

    <h2>Advanced Search</h2>

    <form method="get" class="filter-section">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Name (contains)</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email (contains)</label>
                <input type="text" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="">Any</option>
                    <option value="Male" <?= ($_GET['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($_GET['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($_GET['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department">
                    <option value="">Any</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= htmlspecialchars($dept) ?>" <?= ($_GET['department'] ?? '') === $dept ? 'selected' : '' ?>><?= htmlspecialchars($dept) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Min Salary (₹)</label>
                <input type="number" step="0.01" name="min_salary" value="<?= htmlspecialchars($_GET['min_salary'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Max Salary (₹)</label>
                <input type="number" step="0.01" name="max_salary" value="<?= htmlspecialchars($_GET['max_salary'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Hobby</label>
                <select name="hobby">
                    <option value="">Any</option>
                    <option value="Reading" <?= ($_GET['hobby'] ?? '') === 'Reading' ? 'selected' : '' ?>>Reading</option>
                    <option value="Gaming" <?= ($_GET['hobby'] ?? '') === 'Gaming' ? 'selected' : '' ?>>Gaming</option>
                    <option value="Sports" <?= ($_GET['hobby'] ?? '') === 'Sports' ? 'selected' : '' ?>>Sports</option>
                    <option value="Music" <?= ($_GET['hobby'] ?? '') === 'Music' ? 'selected' : '' ?>>Music</option>
                    <option value="Travel" <?= ($_GET['hobby'] ?? '') === 'Travel' ? 'selected' : '' ?>>Travel</option>
                </select>
            </div>
        </div>
        <div style="margin-top: 15px;">
            <button type="submit" class="btn btn-success">Search</button>
            <a href="search.php" class="btn">Reset</a>
            <a href="manage.php" class="btn">Back to Manage</a>
        </div>
    </form>

<?php if ($searchPerformed): ?>
    <?php if ($error): ?>
        <div class="message error-msg"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <h3>Search Results (<?= count($results) ?> found)</h3>
        <?php if (empty($results)): ?>
            <p>No employees match your criteria.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Gender</th><th>Hobbies</th>
                    <th>Salary (₹)</th><th>Department</th><th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $emp): ?>
                    <tr>
                        <td><?= $emp['id'] ?></td>
                        <td><?= htmlspecialchars($emp['name']) ?></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        <td><?= $emp['gender'] ?></td>
                        <td><?= htmlspecialchars($emp['hobbies']) ?></td>
                        <td>₹<?= number_format($emp['salary'], 2) ?></td>
                        <td><?= htmlspecialchars($emp['department'] ?? 'General') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $emp['id'] ?>">Edit</a> |
                            <a href="delete.php?id=<?= $emp['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>