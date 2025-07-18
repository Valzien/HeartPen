<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$follower_id = $_SESSION['user_id'];
$following_id = $_GET['user_id'] ?? 0;

if (!$following_id || $following_id == $follower_id) {
    header("Location: index.php");
    exit;
}

// hapus follow
$pdo->prepare("DELETE FROM follows WHERE follower_id=? AND following_id=?")
    ->execute([$follower_id, $following_id]);

// redirect
header("Location: user.php?user=" . urlencode($_GET['username'] ?? ''));
exit;
?>
