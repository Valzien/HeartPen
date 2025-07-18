<?php
include 'header.php';
include 'koneksi.php';

$username = $_GET['user'] ?? '';

if (!$username) {
    echo "<div class='container mx-auto px-6 py-12'><p class='text-red-500'>Pengguna tidak ditemukan.</p></div>";
    include 'footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container mx-auto px-6 py-12'><p class='text-red-500'>Pengguna tidak ditemukan.</p></div>";
    include 'footer.php';
    exit;
}

$view_id = $user['id'];

$stmt = $pdo->prepare("SELECT u.* FROM follows f JOIN users u ON f.following_id = u.id WHERE f.follower_id = ?");
$stmt->execute([$view_id]);
$following = $stmt->fetchAll();
?>

<div class="container mx-auto px-6 py-12 max-w-3xl">
    <h2 class="text-2xl font-bold mb-8">Mengikuti @<?= htmlspecialchars($user['username']) ?></h2>
    <?php if (!$following): ?>
        <div class="p-4 bg-gray-100 rounded">Belum mengikuti siapa pun.</div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($following as $f): ?>
                <a href="user.php?user=<?= htmlspecialchars($f['username']) ?>" class="flex items-center space-x-3 p-3 border rounded hover:bg-gray-50">
                    <?php if ($f['profile_photo'] && file_exists($f['profile_photo'])): ?>
                        <img src="<?= htmlspecialchars($f['profile_photo']) ?>" class="w-10 h-10 rounded-full object-cover" alt="<?= htmlspecialchars($f['username']) ?>">
                    <?php else: ?>
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-sm text-white font-bold">
                            <?= strtoupper($f['username'][0]) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <p class="font-semibold">@<?= htmlspecialchars($f['username']) ?></p>
                        <p class="text-xs text-gray-600"><?= htmlspecialchars($f['email']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
