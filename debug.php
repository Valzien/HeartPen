<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

// ambil id dari GET, default ke 1 biar gak kosong
$id = $_GET['id'] ?? 1;

// ambil post + user
$stmt = $pdo->prepare("SELECT p.id, p.title, p.content, p.cover, p.created_at,
                              u.username, u.profile_photo 
                       FROM posts p
                       LEFT JOIN users u ON p.user_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

echo "<pre>";
echo "DEBUG POST DATA:\n\n";
var_dump($post);
echo "</pre>";

if (!$post) {
    echo "<p style='color:red;'>Data dengan ID $id tidak ditemukan di database!</p>";
} else {
    echo "<h3>Data berhasil ditemukan!</h3>";
    echo "<p><strong>Judul:</strong> " . htmlspecialchars($post['title']) . "</p>";
    echo "<p><strong>Penulis:</strong> " . htmlspecialchars($post['username']) . "</p>";
    echo "<p><strong>Konten:</strong> " . nl2br(htmlspecialchars(substr($post['content'], 0, 200))) . "...</p>";
}

echo "<p><a href='?id=1'>Tes ID 1</a> | <a href='?id=2'>Tes ID 2</a> | <a href='?id=3'>Tes ID 3</a></p>";
?>
