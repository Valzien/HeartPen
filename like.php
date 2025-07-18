<?php
session_start();
include 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Kamu harus login dulu']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? 0;

if (!$post_id) {
    echo json_encode(['status' => 'error', 'message' => 'Post ID tidak valid']);
    exit;
}

// Cek apakah sudah like
$stmt = $pdo->prepare("SELECT id FROM post_likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);
$liked = $stmt->fetch();

if ($liked) {
    // UNLIKE
    $pdo->prepare("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?")->execute([$user_id, $post_id]);
    $pdo->prepare("UPDATE posts SET likes_count = likes_count - 1 WHERE id = ?")->execute([$post_id]);
    $totalLikes = $pdo->query("SELECT likes_count FROM posts WHERE id = $post_id")->fetchColumn();
    echo json_encode(['status' => 'unliked', 'total' => $totalLikes]);
} else {
    // LIKE
    $pdo->prepare("INSERT INTO post_likes (user_id, post_id, created_at) VALUES (?, ?, NOW())")->execute([$user_id, $post_id]);
    $pdo->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?")->execute([$post_id]);

    // Notifikasi ke pemilik post
    $q = $pdo->prepare("SELECT user_id, baca, title FROM posts WHERE id = ?");
    $q->execute([$post_id]);
    $post = $q->fetch();

    if ($post && $post['user_id'] != $user_id) {
        $msg = "menyukai postinganmu: \"" . htmlspecialchars($post['title']) . "\"</a>";
        $url = "read.php?baca=" . urlencode($post['baca']);
        sendNotification($pdo, $post['user_id'], $msg, $url);
    }


    $totalLikes = $pdo->query("SELECT likes_count FROM posts WHERE id = $post_id")->fetchColumn();
    echo json_encode(['status' => 'liked', 'total' => $totalLikes]);
}
