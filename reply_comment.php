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
$reply = trim($_POST['reply'] ?? '');

if ($comment_id && $reply !== '') {
    try {
        $stmt = $pdo->prepare("INSERT INTO comment_replies (comment_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$comment_id, $user_id, $reply]);
        $reply_id = $pdo->lastInsertId();

        // Notifikasi ke pemilik komentar
        $q = $pdo->prepare("SELECT c.user_id, p.baca, p.title FROM comments c JOIN posts p ON c.post_id = p.id WHERE c.id = ?");
        $q->execute([$comment_id]);
        $target = $q->fetch();

        if ($target && $target['user_id'] != $user_id) {
            $msg = "membalas komentarmu di: \"" . htmlspecialchars($target['title']) . "\"";
            $url = "read.php?baca=" . urlencode($target['baca']) . "#comment-$comment_id";
            sendNotification($pdo, $target['user_id'], $msg, $url);
        }

        // Ambil data user pengirim untuk render reply HTML
        $user = $pdo->prepare("SELECT username, profile_photo FROM users WHERE id = ?");
        $user->execute([$user_id]);
        $u = $user->fetch();

        $photo = $u['profile_photo']
            ? "<img src='".htmlspecialchars($u['profile_photo'])."' class='w-8 h-8 rounded-full object-cover'>"
            : "<div class='w-8 h-8 rounded-full bg-slate-400 flex items-center justify-center text-white font-bold'>".strtoupper(substr($u['username'],0,1))."</div>";

        $html = "
        <div class='flex items-start gap-3 ml-6 reply-item fade-out'>
            $photo
            <div class='flex-1'>
                <div class='text-sm font-semibold'>
                    <a href='user.php?user=".urlencode($u['username'])."' class='hover:underline'>".htmlspecialchars($u['username'])."</a>
                    <span class='text-xs text-gray-500 ml-2'>baru saja</span>
                </div>
                <div class='text-sm text-gray-800'>".nl2br(htmlspecialchars($reply))."</div>
                <div class='flex gap-3 mt-1'>
                    <button class='likeReplyBtn text-xs text-red-600 hover:underline' data-id='$reply_id'>❤️ Suka</button>
                    <button class='deleteReplyBtn text-xs text-black hover:underline' data-id='$reply_id'>Hapus</button>
                </div>
            </div>
        </div>
        ";

        echo json_encode(['status' => 'success', 'html' => $html]);
    } catch (Throwable $e) {
        echo json_encode(['status' => 'fail', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'fail']);
}
