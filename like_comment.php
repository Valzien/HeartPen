<?php
include 'koneksi.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauth']);
    exit;
}

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['id'] ?? null;
$reply_id = $_POST['reply_id'] ?? null;

// === LIKE KOMENTAR BIASA ===
if ($comment_id) {
    $check = $pdo->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $check->execute([$comment_id, $user_id]);

    if ($check->fetch()) {
        $del = $pdo->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $del->execute([$comment_id, $user_id]);
        echo json_encode(['status' => 'unliked']);
    } else {
        $ins = $pdo->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
        $ins->execute([$comment_id, $user_id]);

        // Ambil data komentar dan user pemilik
        $cStmt = $pdo->prepare("SELECT c.user_id, p.baca, p.title FROM comments c JOIN posts p ON c.post_id = p.id WHERE c.id = ?");
        $cStmt->execute([$comment_id]);
        $data = $cStmt->fetch();

        if ($data && $data['user_id'] != $user_id) {
            $msg = "menyukai komentarmu di: " . htmlspecialchars($data['title']);
            $url = "read.php?baca=" . urlencode($data['baca']) . "#comment-$comment_id";
            sendNotification($pdo, $data['user_id'], $msg, $url);
        }

        echo json_encode(['status' => 'liked']);
    }
    exit;
}

// === LIKE BALASAN KOMENTAR ===
if ($reply_id) {
    $check = $pdo->prepare("SELECT id FROM reply_likes WHERE reply_id = ? AND user_id = ?");
    $check->execute([$reply_id, $user_id]);

    if ($check->fetch()) {
        $del = $pdo->prepare("DELETE FROM reply_likes WHERE reply_id = ? AND user_id = ?");
        $del->execute([$reply_id, $user_id]);
        echo json_encode(['status' => 'unliked']);
    } else {
        $ins = $pdo->prepare("INSERT INTO reply_likes (reply_id, user_id) VALUES (?, ?)");
        $ins->execute([$reply_id, $user_id]);

        $rStmt = $pdo->prepare("SELECT r.user_id, c.id AS comment_id, p.baca, p.title
            FROM comment_replies r 
            JOIN comments c ON r.comment_id = c.id 
            JOIN posts p ON c.post_id = p.id 
            WHERE r.id = ?");
        $rStmt->execute([$reply_id]);
        $data = $rStmt->fetch();

        if ($data && $data['user_id'] != $user_id) {
            $msg = "menyukai balasanmu di: \"" . htmlspecialchars($data['title']) . "\"";
            $url = "read.php?baca=" . urlencode($data['baca']) . "#comment-" . $data['comment_id'];
            sendNotification($pdo, $data['user_id'], $msg, $url);
        }

        echo json_encode(['status' => 'liked']);
    }
    exit;
}

echo json_encode(['status' => 'fail']);
