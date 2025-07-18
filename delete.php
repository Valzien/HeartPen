<?php 
include 'koneksi.php';
session_start();
if (!isset($_SESSION['user_id'])) header('Location: login.php');

$baca = $_GET['baca'] ?? '';
$user_id = $_SESSION['user_id'];

// Ambil username user
$stmt_user = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

if ($baca && $user) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE baca = ? AND user_id = ?");
    $stmt->execute([$baca, $user_id]);
}

// Redirect ke profil pengguna
header('Location: user.php?user=' . urlencode($user['username']));
exit;
