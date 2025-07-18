<?php
session_start();
require 'koneksi.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'unauthorized']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $post_id = (int) ($_POST['post_id'] ?? 0);
    $list_id = isset($_POST['list_id']) ? (int) $_POST['list_id'] : null;

    if (!$post_id) {
        echo json_encode(['status' => 'fail', 'message' => 'Post ID tidak valid']);
        exit;
    }

    // Cek apakah sudah dibookmark sebelumnya
    if ($list_id) {
        $check = $pdo->prepare("SELECT id FROM post_bookmarks WHERE user_id = ? AND post_id = ? AND list_id = ?");
        $check->execute([$user_id, $post_id, $list_id]);
    } else {
        $check = $pdo->prepare("SELECT id FROM post_bookmarks WHERE user_id = ? AND post_id = ? AND list_id IS NULL");
        $check->execute([$user_id, $post_id]);
    }

    if ($check->fetch()) {
        // Sudah dibookmark, maka hapus
        if ($list_id) {
            $del = $pdo->prepare("DELETE FROM post_bookmarks WHERE user_id = ? AND post_id = ? AND list_id = ?");
            $del->execute([$user_id, $post_id, $list_id]);
        } else {
            $del = $pdo->prepare("DELETE FROM post_bookmarks WHERE user_id = ? AND post_id = ? AND list_id IS NULL");
            $del->execute([$user_id, $post_id]);
        }

        echo json_encode(['status' => 'unbookmarked']);
    } else {
        // Belum dibookmark, maka simpan
        if ($list_id) {
            $ins = $pdo->prepare("INSERT INTO post_bookmarks (user_id, post_id, list_id) VALUES (?, ?, ?)");
            $ins->execute([$user_id, $post_id, $list_id]);
        } else {
            $ins = $pdo->prepare("INSERT INTO post_bookmarks (user_id, post_id, list_id) VALUES (?, ?, NULL)");
            $ins->execute([$user_id, $post_id]);
        }

        echo json_encode(['status' => 'bookmarked']);
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
