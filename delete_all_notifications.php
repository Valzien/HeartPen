<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Hapus semua notifikasi milik user
$pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$user_id]);

header("Location: notifications.php");
exit;
?>
