<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: register.php'); // Redirect to registration if not logged in
    exit;
}

// Check for logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy(); // Destroy the session
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Fetch all polls
$stmt = $pdo->query("SELECT * FROM polls");
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check user votes
$user_id = $_SESSION['user_id'];
$user_votes = $pdo->prepare("SELECT poll_id FROM votes WHERE user_id = ?");
$user_votes->execute([$user_id]);
$voted_polls = $user_votes->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>User Dashboard</title>
</head>
<body>
    <div class="container">
        <h2>Available Polls</h2>
        <a href="?action=logout" style="float: right;">Logout</a> 
        <div class="polls">
            <?php foreach ($polls as $poll): ?>
                <div class="poll">
                    <h3><?= htmlspecialchars($poll['question']); ?></h3>
                    <form method="POST" action="vote.php">
                        <?php
                        // Fetch poll options
                        $options_stmt = $pdo->prepare("SELECT * FROM options WHERE poll_id = ?");
                        $options_stmt->execute([$poll['id']]);
                        $options = $options_stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <?php foreach ($options as $option): ?>
                            <input type="radio" name="option_id" value="<?= $option['id']; ?>" 
                            <?php if (in_array($poll['id'], $voted_polls)): ?> 
                                disabled 
                            <?php endif; ?> 
                            required> <?= htmlspecialchars($option['option_text']); ?><br>
                        <?php endforeach; ?>

                        <input type="hidden" name="poll_id" value="<?= $poll['id']; ?>">
                        <?php if (in_array($poll['id'], $voted_polls)): ?>
                            <p>You have already voted for this poll.</p>
                        <?php else: ?>
                            <button type="submit">Vote</button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
