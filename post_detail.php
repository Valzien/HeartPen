<?php
include 'header.php';
include 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='p-6'>Post tidak ditemukan.</div>";
    include 'footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

$isLiked = false;
if (isset($_SESSION['user_id'])) {
    $check = $pdo->prepare("SELECT id FROM post_likes WHERE user_id = ? AND post_id = ?");
    $check->execute([$_SESSION['user_id'], $post['id']]);
    $isLiked = $check->fetch() ? true : false;
}
?>
