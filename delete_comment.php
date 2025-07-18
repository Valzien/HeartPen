<?php
session_start();
require 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Harus login']);
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST['id'] ?? 0;
$reply_id = $_POST['reply_id'] ?? 0;

// === HAPUS KOMENTAR UTAMA ===
if ($id) {
    $stmt = $pdo->prepare("SELECT id FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak boleh hapus komentar orang lain']);
        exit;
    }

    $del = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $del->execute([$id]);

    echo json_encode(['status' => 'deleted']);
    exit;
}

// === HAPUS BALASAN KOMENTAR ===
if ($reply_id) {
    $stmt = $pdo->prepare("SELECT id FROM comment_replies WHERE id = ? AND user_id = ?");
    $stmt->execute([$reply_id, $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak boleh hapus balasan orang lain']);
        exit;
    }

    $del = $pdo->prepare("DELETE FROM comment_replies WHERE id = ?");
    $del->execute([$reply_id]);

    echo json_encode(['status' => 'deleted']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
