<?php
session_start();
include '../includes/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch the poll details if an ID is provided
if (isset($_GET['id'])) {
    $poll_id = $_GET['id'];

    // Fetch the poll data
    $poll_stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
    $poll_stmt->execute([$poll_id]);
    $poll = $poll_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch existing options
    $options_stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ?");
    $options_stmt->execute([$poll_id]);
    $options = $options_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle poll update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_poll'])) {
    $question = $_POST['question'];

    // Update the poll question
    $stmt = $pdo->prepare("UPDATE polls SET question = ? WHERE id = ?");
    $stmt->execute([$question, $poll_id]);

    // Update options
    // First, delete existing votes related to the options
    $delete_stmt = $pdo->prepare("DELETE FROM votes WHERE option_id IN (SELECT id FROM options WHERE poll_id = ?)");
    $delete_stmt->execute([$poll_id]);

    // Then, delete existing options
    $delete_options_stmt = $pdo->prepare("DELETE FROM options WHERE poll_id = ?");
    $delete_options_stmt->execute([$poll_id]);

    // Insert updated options
    if (isset($_POST['options']) && !empty($_POST['options'])) {
        foreach ($_POST['options'] as $option) {
            if (!empty($option)) { // Only insert non-empty options
                $stmt = $pdo->prepare("INSERT INTO options (poll_id, option_text) VALUES (?, ?)");
                $stmt->execute([$poll_id, $option]);
            }
        }
    }

    // Redirect back to the admin dashboard after update
    header('Location: admin_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Edit Poll</title>
</head>
<body>
    <div class="update-container">
        <h2>Edit Poll</h2>
        <?php if (isset($poll)): ?>
            <form method="POST" action="">
                <input type="text" name="question" value="<?= htmlspecialchars($poll['question']); ?>" required>
                <h4>Options:</h4>
                <?php foreach ($options as $option): ?>
                    <input type="text" name="options[]" value="<?= htmlspecialchars($option['option_text']); ?>" required>
                <?php endforeach; ?>
                <input type="text" name="options[]" placeholder="Option (new)">
                <input type="text" name="options[]" placeholder="Option (new)">
                <button type="submit" name="update_poll">Update Poll</button>
            </form>
        <?php else: ?>
            <p>Poll not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
