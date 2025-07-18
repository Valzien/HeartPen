<?php
include 'header.php';
include 'koneksi.php';

$session_id = $_SESSION['user_id'] ?? null;
if (!$session_id) {
    header('Location: login.php');
    exit;
}

// ambil notifikasi
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$session_id]);
$notifications = $stmt->fetchAll();

// tandai semua sebagai read
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$session_id]);
?>

<div class="container mx-auto px-6 py-12 max-w-3xl">
    <h2 class="text-2xl font-bold mb-4 text-center">🔔 Notifikasi</h2>

    <?php if (!empty($notifications)): ?>
        <div class="flex justify-end mb-4">
            <form method="post" action="delete_all_notifications.php" onsubmit="return confirm('Hapus semua notifikasi?')">
                <button type="submit" class="text-sm text-red-600 hover:underline">🗑 Hapus Semua</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (empty($notifications)): ?>
        <div class="p-4 bg-gray-100 rounded text-gray-600 text-center">Tidak ada notifikasi baru.</div>
        <div class="h-64"></div> <!-- dummy spacing -->
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($notifications as $notif): ?>
                <?php
                    $icon = '🔔';
                    if (str_contains($notif['message'], 'menyukai')) $icon = '❤️';
                    elseif (str_contains($notif['message'], 'mengomentari')) $icon = '💬';
                    elseif (str_contains($notif['message'], 'menyimpan')) $icon = '📌';

                    $link = $notif['link'] ?? null;
                    $wrapperStart = $wrapperEnd = '';
                    if ($link) {
                        $wrapperStart = "<a href=\"" . htmlspecialchars($link) . "\" class=\"block hover:bg-blue-50 transition rounded\">";
                        $wrapperEnd = "</a>";
                    }
                ?>
                <?= $wrapperStart ?>
                <div class="p-4 border rounded bg-white shadow-sm flex items-start gap-3">
                    <div class="text-xl"><?= $icon ?></div>
                    <div class="flex-1">
                        <p class="text-gray-800"><?= $notif['message'] ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></p>
                    </div>
                    <?php if (!$notif['is_read']): ?>
                        <span class="mt-1 w-2 h-2 rounded-full bg-blue-500"></span>
                    <?php endif; ?>
                </div>
                <?= $wrapperEnd ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<div class="h-32"></div> <!-- dummy spacing -->
<?php include 'footer.php'; ?>
