<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$username = $_GET['user'] ?? '';
$post_id = $_GET['id'] ?? null;
$new_status = $_GET['to'] ?? 'draft';

if (!in_array($new_status, ['publish', 'draft', 'archived'])) exit;

// Ambil user berdasarkan username
if ($username) {
    $user_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $user_stmt->execute([$username]);
    $user_data = $user_stmt->fetch();

    if (!$user_data || $user_data['id'] != $user_id) {
        exit("Unauthorized");
    }

    $user_id = $user_data['id'];
}

// Update status
if ($post_id) {
    $stmt = $pdo->prepare("UPDATE posts SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_status, $post_id, $user_id]);
}

// Redirect balik ke profil
header("Location: user.php?user=" . urlencode($username ?: $_SESSION['username']));
