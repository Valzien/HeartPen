<?php
session_start();
require 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauth']);
    exit;
}

$user_id = $_SESSION['user_id'];
$list_id = $_POST['id'] ?? 0;

if (!$list_id) {
    echo json_encode(['status' => 'invalid']);
    exit;
}

// Pastikan list milik user
$check = $pdo->prepare("SELECT id FROM bookmark_lists WHERE id = ? AND user_id = ?");
$check->execute([$list_id, $user_id]);

if (!$check->fetch()) {
    echo json_encode(['status' => 'forbidden']);
    exit;
}

// Hapus semua isi post_bookmarks terkait list ini
$delPost = $pdo->prepare("DELETE FROM post_bookmarks WHERE list_id = ?");
$delPost->execute([$list_id]);

// Hapus list
$delList = $pdo->prepare("DELETE FROM bookmark_lists WHERE id = ?");
$delList->execute([$list_id]);

echo json_encode(['status' => 'deleted']);
