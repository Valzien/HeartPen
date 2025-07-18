<?php
include 'header.php';
include 'koneksi.php';

$user_id = $_SESSION['user_id'] ?? null;

// Ambil daftar list bookmark milik user
$bookmark_lists = [];
if ($user_id) {
    $list_stmt = $pdo->prepare("SELECT * FROM bookmark_lists WHERE user_id = ?");
    $list_stmt->execute([$user_id]);
    $bookmark_lists = $list_stmt->fetchAll();
}

// Ambil semua bookmark post_id milik user
$user_bookmarks = [];
if ($user_id) {
    $bm_stmt = $pdo->prepare("SELECT post_id FROM post_bookmarks WHERE user_id = ?");
    $bm_stmt->execute([$user_id]);
    $user_bookmarks = $bm_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Pencarian dan sorting
$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'latest';
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

$orderBy = match($sort) {
    'views' => 'total_views DESC',
    'likes' => 'total_likes DESC',
    default => 'p.created_at DESC'
};

// Hitung total post untuk pagination
$countQuery = "SELECT COUNT(*) FROM posts p JOIN users u ON p.user_id = u.id WHERE p.status = 'publish'";
$params = [];
if ($q) {
    $countQuery .= " AND (p.title LIKE ? OR u.username LIKE ?)";
    $params = ["%$q%", "%$q%"];
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);

// Ambil data post
$query = "SELECT p.*, p.baca, u.username, u.profile_photo,
    (SELECT COUNT(*) FROM post_views WHERE post_id = p.id) AS total_views,
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) AS total_likes,
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS total_comments
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.status = 'publish'";

if ($q) {
    $query .= " AND (p.title LIKE ? OR u.username LIKE ?)";
}
$query .= " ORDER BY $orderBy LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<div class="container mx-auto px-6 py-12 max-w-5xl">
    <h2 class="text-3xl font-bold mb-8 text-slate-800" data-aos="fade-down">Karya Orang Hebat!</h2>

    <form method="get" class="mb-10 flex flex-col md:flex-row gap-4 md:items-center justify-between" data-aos="fade-up">
        <input type="text" name="q" placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($q) ?>"
            class="w-full md:w-2/3 p-3 border border-slate-300 rounded-lg focus:outline-none focus:ring focus:border-blue-600" />
        <select name="sort" onchange="this.form.submit()" class="p-3 border rounded text-sm">
            <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Terbaru</option>
            <option value="views" <?= $sort === 'views' ? 'selected' : '' ?>>Paling Dilihat</option>
            <option value="likes" <?= $sort === 'likes' ? 'selected' : '' ?>>Paling Disukai</option>
        </select>
    </form>

    <div class="space-y-10">
    <?php
    if (!$posts) {
        echo "<div class='p-6 bg-yellow-100 text-yellow-800 rounded'>Yahh, tulisan yang kamu cari tidak ditemukan.</div>";
    }

    $delay = 0;
    foreach ($posts as $p):
        $delay += 100;
        $initial = strtoupper($p['username'][0]);
        $alreadyBookmarked = in_array($p['id'], $user_bookmarks);
    ?>
    <div class="md:flex md:items-start gap-6 border-b pb-8" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <div class="flex-1">
            <div class="flex items-center gap-2 text-sm text-slate-600 mb-1 font-medium">
                <a href="user?user=<?= urlencode($p['username']) ?>" class="flex items-center gap-2 hover:underline">
                    <?php if ($p['profile_photo'] && file_exists($p['profile_photo'])): ?>
                        <img src="<?= htmlspecialchars($p['profile_photo']) ?>" class="w-6 h-6 rounded-full object-cover" />
                    <?php else: ?>
                        <div class="w-6 h-6 rounded-full bg-slate-400 flex items-center justify-center text-xs font-semibold text-white"><?= $initial ?></div>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($p['username']) ?></span>
                </a>
            </div>

            <h3 class="text-2xl font-semibold mb-2 hover:underline">
                <a href="read?baca=<?= urlencode($p['baca']) ?>"><?= htmlspecialchars($p['title']) ?></a>
            </h3>
            <p class="text-slate-700 mb-3 text-sm"><?= substr(strip_tags($p['content']), 0, 140) ?>...</p>

            <div class="flex items-center text-xs text-slate-500 gap-6">
                <div class="flex items-center gap-2">👁️ <?= $p['total_views'] ?> views</div>
                <div class="flex items-center gap-2"><?= $p['total_likes'] > 0 ? '❤️' : '🤍' ?> <?= $p['total_likes'] ?> likes</div>
                <div class="flex items-center gap-2">💬 <?= $p['total_comments'] ?> komentar</div>
                <?php if ($user_id): ?>
                <button class="bookmarkBtn flex items-center gap-2 ml-auto focus:outline-none"
                    data-post="<?= $p['id'] ?>">
                    <span class="bookmarkIcon"><?= $alreadyBookmarked ? '🔖 Berhasil!' : '📑 Bookmark' ?></span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full md:w-48 mt-4 md:mt-0 flex-shrink-0">
            <div class="aspect-[4/3] overflow-hidden rounded">
                <img src="<?= htmlspecialchars($p['cover']) ?>" alt="<?= htmlspecialchars($p['image_desc']) ?>" class="w-full h-full object-cover" />
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex justify-center mt-12 space-x-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&sort=<?= $sort ?>&q=<?= urlencode($q) ?>" class="px-3 py-1 rounded <?= $i == $page ? 'bg-black text-white' : 'bg-gray-200 text-black hover:bg-gray-300' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
