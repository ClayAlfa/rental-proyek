<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

echo "<h1>Test Form Submission</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>POST Data Received:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test database connection
    try {
        $test = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "<p>Database connection OK. User count: $test</p>";
    } catch (Exception $e) {
        echo "<p>Database error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Simple Test Form</h2>
        <form method="POST" action="test_form.php">
            <div class="mb-3">
                <label>Test Field:</label>
                <input type="text" name="test_field" class="form-control" value="Test Value">
            </div>
            <button type="submit" class="btn btn-primary">Submit Test</button>
        </form>
        
        <hr>
        
        <h3>Without JavaScript - Pure HTML Form</h3>
        <form method="POST" action="test_form.php">
            <input type="text" name="username" value="testuser" required>
            <input type="text" name="full_name" value="Test User" required>
            <select name="role_id" required>
                <option value="1">Admin</option>
                <option value="2">User</option>
            </select>
            <input type="submit" value="Submit Pure HTML">
        </form>
    </div>
</body>
</html>
