<?php
session_start();
include '../includes/db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: register.php');
    exit;
}

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $poll_id = $_POST['poll_id'];
    $option_id = $_POST['option_id'];
    $user_id = $_SESSION['user_id'];

    // Insert the vote into the database
    $stmt = $pdo->prepare("INSERT INTO votes (user_id, poll_id, option_id) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $poll_id, $option_id]);

    // Redirect back to the user dashboard
    header('Location: index.php');
    exit;
}
