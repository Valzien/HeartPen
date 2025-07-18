<?php
session_start();
require 'koneksi.php';
header('Content-Type: application/json');

// Harus login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauth']);
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$is_public = isset($_POST['is_public']) && $_POST['is_public'] == "1" ? 1 : 0;

if (!$name) {
    echo json_encode(['status' => 'fail', 'message' => 'Nama tidak boleh kosong']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO bookmark_lists (user_id, name, is_public) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $name, $is_public]);

    echo json_encode([
        'status' => 'success',
        'list_id' => $pdo->lastInsertId(),
        'name' => $name,
        'is_public' => $is_public
    ]);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
