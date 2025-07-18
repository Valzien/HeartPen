<?php
include 'koneksi.php';
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'following' => false];

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
    echo json_encode($response);
    exit;
}

$follower_id = $_SESSION['user_id'];
$following_id = (int) $_POST['user_id'];

if ($following_id === $follower_id || $following_id <= 0) {
    echo json_encode($response);
    exit;
}

// Cek apakah sudah follow
$stmt = $pdo->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?");
$stmt->execute([$follower_id, $following_id]);
$is_following = $stmt->fetch();

if ($is_following) {
    // UNFOLLOW
    $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?")
        ->execute([$follower_id, $following_id]);
    $response['following'] = false;
} else {
    // FOLLOW
    $pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)")
        ->execute([$follower_id, $following_id]);
    $response['following'] = true;

    // Buat notifikasi
    $stmtUser = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmtUser->execute([$follower_id]);
    $follower = $stmtUser->fetch();

    if ($follower && $follower['username']) {
        $usernameSafe = htmlspecialchars($follower['username']);
        $msg = "mulai mengikuti kamu.";
        $url = "user.php?user=" . urlencode($follower['username']);
        sendNotification($pdo, $following_id, $msg, $url);
    }
}

$response['success'] = true;
echo json_encode($response);
