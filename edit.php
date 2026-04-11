<?php
session_start();
require_once 'config/database.php';

$pdo = getDbConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$message = '';

// Fetch existing data
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch();
if (!$employee) {
    header('Location: manage.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $hobbies = $_POST['hobbies'] ?? [];
    $salary = trim($_POST['salary'] ?? '');
    $department = trim($_POST['department'] ?? 'General');

    // Validation
    if (empty($name)) $errors['name'] = 'Name required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
    if (empty($gender)) $errors['gender'] = 'Select gender.';
    if (empty($hobbies)) $errors['hobbies'] = 'Select at least one hobby.';
    if (empty($salary) || !is_numeric($salary) || $salary <= 0) $errors['salary'] = 'Valid salary required.';

    if (empty($errors)) {
        try {
            $hobbiesStr = implode(',', $hobbies);
            $stmt = $pdo->prepare("UPDATE employees SET name=?, email=?, gender=?, hobbies=?, salary=?, department=? WHERE id=?");
            $stmt->execute([$name, $email, $gender, $hobbiesStr, $salary, $department, $id]);
            $message = '<div class="message success">Employee updated successfully.</div>';
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            $employee = $stmt->fetch();
        } catch (PDOException $e) {
            $errors['db'] = 'Update failed: Email may already exist.';
            error_log('Update error: ' . $e->getMessage());
        }
    }
}

// Pre-fill form with existing data (or updated)
$formData = $employee;
$formData['hobbies'] = explode(',', $formData['hobbies']);

include 'includes/header.php';
echo $message;
?>

    <h2>Edit Employee #<?= $id ?></h2>

    <form method="post">
        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($formData['name']) ?>">
            <?php if (isset($errors['name'])): ?><div class="error"><?= $errors['name'] ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>">
            <?php if (isset($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Gender *</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="Male" <?= $formData['gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
                <label><input type="radio" name="gender" value="Female" <?= $formData['gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
                <label><input type="radio" name="gender" value="Other" <?= $formData['gender'] === 'Other' ? 'checked' : '' ?>> Other</label>
            </div>
            <?php if (isset($errors['gender'])): ?><div class="error"><?= $errors['gender'] ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Hobbies *</label>
            <div class="checkbox-group">
                <?php $hobbyOptions = ['Reading', 'Gaming', 'Sports', 'Music', 'Travel']; ?>
                <?php foreach ($hobbyOptions as $hobby): ?>
                    <label>
                        <input type="checkbox" name="hobbies[]" value="<?= $hobby ?>"
                            <?= in_array($hobby, $formData['hobbies']) ? 'checked' : '' ?>>
                        <?= $hobby ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (isset($errors['hobbies'])): ?><div class="error"><?= $errors['hobbies'] ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Salary (₹) *</label>
            <input type="number" step="0.01" name="salary" value="<?= htmlspecialchars($formData['salary']) ?>">
            <?php if (isset($errors['salary'])): ?><div class="error"><?= $errors['salary'] ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Department</label>
            <select name="department">
                <option value="General" <?= ($formData['department'] ?? '') === 'General' ? 'selected' : '' ?>>General</option>
                <option value="IT" <?= ($formData['department'] ?? '') === 'IT' ? 'selected' : '' ?>>IT</option>
                <option value="HR" <?= ($formData['department'] ?? '') === 'HR' ? 'selected' : '' ?>>HR</option>
                <option value="Sales" <?= ($formData['department'] ?? '') === 'Sales' ? 'selected' : '' ?>>Sales</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Employee</button>
        <a href="manage.php" class="btn">Cancel</a>
    </form>

<?php include 'includes/footer.php'; ?>