<?php
session_start();
require_once 'config/database.php';

$errors = [];
$formData = [
    'name' => '', 'email' => '', 'gender' => '',
    'hobbies' => [], 'salary' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = trim($_POST['name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['gender'] = $_POST['gender'] ?? '';
    $formData['hobbies'] = $_POST['hobbies'] ?? [];
    $formData['salary'] = trim($_POST['salary'] ?? '');

    // Validation
    if (empty($formData['name'])) $errors['name'] = 'Name is required.';
    if (empty($formData['email'])) $errors['email'] = 'Email is required.';
    elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email format.';
    if (empty($formData['gender'])) $errors['gender'] = 'Please select a gender.';
    if (empty($formData['hobbies'])) $errors['hobbies'] = 'Select at least one hobby.';
    if (empty($formData['salary'])) $errors['salary'] = 'Salary is required.';
    elseif (!is_numeric($formData['salary']) || $formData['salary'] <= 0) $errors['salary'] = 'Enter a valid positive salary.';

    if (empty($errors)) {
        try {
            $pdo = getDbConnection();
            $hobbiesStr = implode(',', $formData['hobbies']);
            $stmt = $pdo->prepare("INSERT INTO employees (name, email, gender, hobbies, salary) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $formData['name'],
                $formData['email'],
                $formData['gender'],
                $hobbiesStr,
                $formData['salary']
            ]);
            $newId = $pdo->lastInsertId();

            // Store in session
            $_SESSION['last_employee'] = $formData;
            // Set cookie with last submission time
            setcookie('last_submission', date('Y-m-d H:i:s'), time() + 86400, '/');

            // Redirect with GET data
            $query = http_build_query([
                'name' => $formData['name'],
                'email' => $formData['email'],
                'gender' => $formData['gender'],
                'hobbies' => implode(', ', $formData['hobbies']),
                'salary' => $formData['salary'],
                'id' => $newId
            ]);
            header("Location: display.php?$query");
            exit;
        } catch (PDOException $e) {
            $errors['db'] = 'Database error: Email might already exist.';
            error_log('Insert error: ' . $e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<?php if (!empty($errors['db'])): ?>
    <div class="message error-msg"><?= htmlspecialchars($errors['db']) ?></div>
<?php endif; ?>

    <form method="post" novalidate>
        <div class="form-group">
            <label>Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($formData['name']) ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="error"><?= $errors['name'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Gender *</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="Male" <?= $formData['gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
                <label><input type="radio" name="gender" value="Female" <?= $formData['gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
                <label><input type="radio" name="gender" value="Other" <?= $formData['gender'] === 'Other' ? 'checked' : '' ?>> Other</label>
            </div>
            <?php if (isset($errors['gender'])): ?>
                <div class="error"><?= $errors['gender'] ?></div>
            <?php endif; ?>
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
            <?php if (isset($errors['hobbies'])): ?>
                <div class="error"><?= $errors['hobbies'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Salary (₹) *</label>
            <input type="number" step="0.01" name="salary" value="<?= htmlspecialchars($formData['salary']) ?>">
            <?php if (isset($errors['salary'])): ?>
                <div class="error"><?= $errors['salary'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-success">Submit</button>
    </form>

<?php include 'includes/footer.php'; ?>