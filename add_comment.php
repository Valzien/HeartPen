<?php
session_start();
require 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error', 'message'=>'Harus login']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? 0;
$content = trim($_POST['comment'] ?? '');

if (!$post_id || !$content) {
    echo json_encode(['status'=>'error', 'message'=>'Komentar kosong']);
    exit;
}

try {
    // Simpan komentar
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $post_id, $content]);
    $comment_id = $pdo->lastInsertId();

    // Ambil data post untuk keperluan notifikasi
    $q = $pdo->prepare("SELECT user_id, baca, title FROM posts WHERE id = ?");
    $q->execute([$post_id]);
    $post = $q->fetch();

    if ($post && $post['user_id'] != $user_id) {
        $msg = "mengomentari postinganmu: \"" . htmlspecialchars($post['title']) . "\"";
        $url = "read.php?baca=" . urlencode($post['baca']) . "#comment-$comment_id";
        sendNotification($pdo, $post['user_id'], $msg, $url);
    }

    // Ambil user untuk render komentar
    $user = $pdo->prepare("SELECT username, profile_photo FROM users WHERE id = ?");
    $user->execute([$user_id]);
    $u = $user->fetch();

    $photo = $u['profile_photo']
        ? "<img src='" . htmlspecialchars($u['profile_photo']) . "' class='w-10 h-10 rounded-full object-cover'>"
        : "<div class='w-10 h-10 rounded-full bg-slate-400 flex items-center justify-center text-white font-bold'>" . strtoupper(substr($u['username'], 0, 1)) . "</div>";

    $html = "
    <div id='comment-$comment_id' class='flex items-start gap-4 border-b pb-4 comment-item'>
        $photo
        <div class='flex-1'>
            <div class='text-sm font-semibold'>
                <a href='user.php?user=" . urlencode($u['username']) . "' class='hover:underline'>" . htmlspecialchars($u['username']) . "</a>
                <span class='text-xs text-gray-500 ml-2'>baru saja</span>
            </div>
            <div class='text-sm text-gray-800'>" . nl2br(htmlspecialchars($content)) . "</div>
            <div class='flex gap-3 mt-1'>
                <button class='replyBtn text-xs text-blue-600 hover:underline' data-id='$comment_id'>Balas</button>
                <button class='likeCommentBtn text-xs text-red-600 hover:underline' data-id='$comment_id'>❤️ Suka</button>
                <button class='deleteComment text-xs text-black hover:underline' data-id='$comment_id'>Hapus</button>
            </div>
            <div class='replyForm hidden mt-2' data-id='$comment_id'>
                <textarea rows='2' class='w-full p-2 border rounded mb-2' placeholder='Tulis balasan...'></textarea>
                <button class='sendReply bg-black text-white px-3 py-1 rounded' data-id='$comment_id'>Kirim</button>
            </div>
        </div>
    </div>
    ";

    echo json_encode(['status' => 'success', 'html' => $html]);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
