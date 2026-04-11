<?php
session_start();
require_once 'config/database.php';

$pdo = getDbConnection();
$message = '';

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $message = '<div class="message success">Employee deleted successfully.</div>';
    } catch (PDOException $e) {
        $message = '<div class="message error-msg">Delete failed.</div>';
        error_log('Delete error: ' . $e->getMessage());
    }
}

// Build query with filters
$where = [];
$params = [];
$orderBy = "id ASC";

// Salary > 50000
if (isset($_GET['high_salary']) && $_GET['high_salary'] == 1) {
    $where[] = "salary > 50000";
}
// Gender filter
if (!empty($_GET['gender_filter'])) {
    $where[] = "gender = ?";
    $params[] = $_GET['gender_filter'];
}
// Department filter (if column exists)
if (!empty($_GET['dept_filter'])) {
    $where[] = "department = ?";
    $params[] = $_GET['dept_filter'];
}
// Sorting
if (!empty($_GET['sort'])) {
    $orderBy = match($_GET['sort']) {
        'name_asc' => "name ASC",
        'name_desc' => "name DESC",
        'salary_asc' => "salary ASC",
        'salary_desc' => "salary DESC",
        default => "id ASC"
    };
}
// Search
if (!empty($_GET['search'])) {
    $where[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%{$_GET['search']}%";
    $params[] = "%{$_GET['search']}%";
}

$sql = "SELECT * FROM employees";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$employees = $stmt->fetchAll();

// Count total employees
$totalStmt = $pdo->query("SELECT COUNT(*) FROM employees");
$totalEmployees = $totalStmt->fetchColumn();

include 'includes/header.php';
echo $message;
?>

    <div class="filter-section">
        <form method="get" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;">
            <div class="form-group" style="flex:1;">
                <label>Search</label>
                <input type="text" name="search" placeholder="Name or Email" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender_filter">
                    <option value="">All</option>
                    <option value="Male" <?= ($_GET['gender_filter'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($_GET['gender_filter'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($_GET['gender_filter'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="dept_filter">
                    <option value="">All</option>
                    <option value="General" <?= ($_GET['dept_filter'] ?? '') === 'General' ? 'selected' : '' ?>>General</option>
                    <option value="IT" <?= ($_GET['dept_filter'] ?? '') === 'IT' ? 'selected' : '' ?>>IT</option>
                    <option value="HR" <?= ($_GET['dept_filter'] ?? '') === 'HR' ? 'selected' : '' ?>>HR</option>
                    <option value="Sales" <?= ($_GET['dept_filter'] ?? '') === 'Sales' ? 'selected' : '' ?>>Sales</option>
                </select>
            </div>
            <div class="form-group">
                <label>Sort By</label>
                <select name="sort">
                    <option value="">Default (ID)</option>
                    <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                    <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Name Z-A</option>
                    <option value="salary_asc" <?= ($_GET['sort'] ?? '') === 'salary_asc' ? 'selected' : '' ?>>Salary Low-High</option>
                    <option value="salary_desc" <?= ($_GET['sort'] ?? '') === 'salary_desc' ? 'selected' : '' ?>>Salary High-Low</option>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="high_salary" value="1" <?= isset($_GET['high_salary']) ? 'checked' : '' ?>> Salary > ₹50,000</label>
            </div>
            <button type="submit" class="btn">Apply Filters</button>
            <a href="manage.php" class="btn">Reset</a>
        </form>
    </div>

    <h3>Total Employees: <?= $totalEmployees ?></h3>

    <table>
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Gender</th><th>Hobbies</th>
            <th>Salary (₹)</th><th>Department</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($employees)): ?>
            <tr><td colspan="8" style="text-align:center;">No employees found.</td></tr>
        <?php else: ?>
            <?php foreach ($employees as $emp): ?>
                <tr>
                    <td><?= $emp['id'] ?></td>
                    <td><?= htmlspecialchars($emp['name']) ?></td>
                    <td><?= htmlspecialchars($emp['email']) ?></td>
                    <td><?= $emp['gender'] ?></td>
                    <td><?= htmlspecialchars($emp['hobbies']) ?></td>
                    <td>₹<?= number_format($emp['salary'], 2) ?></td>
                    <td><?= htmlspecialchars($emp['department'] ?? 'General') ?></td>
                    <td>
                        <a href="edit.php?id=<?= $emp['id'] ?>" class="btn" style="padding:5px 10px;">Edit</a>
                        <a href="?delete=<?= $emp['id'] ?>" class="btn btn-danger" style="padding:5px 10px;" onclick="return confirm('Delete this employee?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

<?php include 'includes/footer.php'; ?>