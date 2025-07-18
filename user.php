<?php
include 'header.php';
include 'koneksi.php';

$username = $_GET['user'] ?? '';
if (!$username) die('<div class="p-6 text-red-600 text-center">Pengguna tidak ditemukan.</div>');

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) die('<div class="p-6 text-red-600 text-center">Pengguna tidak ditemukan.</div>');

$session_id = $_SESSION['user_id'] ?? null;
$view_id = $user['id'];
$can_edit = ($session_id && $session_id == $view_id);

$is_following = false;
if (!$can_edit && $session_id) {
    $stmt_following = $pdo->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt_following->execute([$session_id, $view_id]);
    $is_following = (bool) $stmt_following->fetchColumn();
}

// followers/following
$fol_stmt = $pdo->prepare("SELECT 
    (SELECT COUNT(*) FROM follows WHERE following_id = ?) AS followers,
    (SELECT COUNT(*) FROM follows WHERE follower_id = ?) AS following");
$fol_stmt->execute([$view_id, $view_id]);
$fol = $fol_stmt->fetch();

// bookmarks
$bookmark_ids = [];
if ($can_edit) {
    $bm_stmt = $pdo->prepare("SELECT post_id FROM post_bookmarks WHERE user_id = ?");
    $bm_stmt->execute([$view_id]);
    $bookmark_ids = $bm_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// arsip
$arsip_posts = [];
if ($can_edit) {
    $stmt_arsip = $pdo->prepare("SELECT p.*,
        (SELECT COUNT(*) FROM post_views WHERE post_id = p.id) AS views,
        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) AS likes,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments
        FROM posts p WHERE p.user_id = ? AND p.status = 'archived'
        ORDER BY p.created_at DESC");
    $stmt_arsip->execute([$view_id]);
    $arsip_posts = $stmt_arsip->fetchAll();
}

// bookmark detail
$bookmark_posts = [];
if ($can_edit && !empty($bookmark_ids)) {
    $in = str_repeat('?,', count($bookmark_ids) - 1) . '?';
    $stmt_bm = $pdo->prepare("SELECT p.*,
        (SELECT COUNT(*) FROM post_views WHERE post_id = p.id) AS views,
        (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) AS likes,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments
        FROM posts p WHERE p.id IN ($in)
        ORDER BY p.created_at DESC");
    $stmt_bm->execute($bookmark_ids);
    $bookmark_posts = $stmt_bm->fetchAll();
}

// top followers

$top_followers = [];
$stmt_top = $pdo->prepare("SELECT u.username, u.profile_photo, u.bio
    FROM follows f
    JOIN users u ON f.follower_id = u.id
    WHERE f.following_id = ?
    ORDER BY f.id ASC LIMIT 3");
$stmt_top->execute([$view_id]);
$top_followers = $stmt_top->fetchAll();

// sort & paging
$sort = $_GET['sort'] ?? 'latest';
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

$orderBy = match($sort) {
    'views' => '(SELECT COUNT(*) FROM post_views WHERE post_id = p.id) DESC',
    'likes' => '(SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) DESC',
    default => 'p.created_at DESC'
};

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status != 'archived'");
$countStmt->execute([$view_id]);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);

$stmt_posts = $pdo->prepare("SELECT p.*,
    (SELECT COUNT(*) FROM post_views WHERE post_id = p.id) AS views,
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) AS likes,
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments
    FROM posts p
    WHERE p.user_id = ? AND p.status != 'archived'
    ORDER BY $orderBy
    LIMIT $perPage OFFSET $offset");
$stmt_posts->execute([$view_id]);
$posts = $stmt_posts->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $bio = trim($_POST['bio']);
    $instagram = trim($_POST['instagram']);
    $twitter = trim($_POST['twitter']);
    $linkedin = trim($_POST['linkedin']);

    // kalau user cuma template atau kosong -> simpan kosong
    if ($instagram == 'https://instagram.com/' || $instagram == '') $instagram = '';
    if ($twitter == 'https://twitter.com/' || $twitter == '') $twitter = '';
    if ($linkedin == 'https://linkedin.com/in/' || $linkedin == '') $linkedin = '';

    // update foto jika ada
    if (!empty($_FILES['profile_photo']['name'])) {
        $foto = 'uploads/' . uniqid() . '_' . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $foto);

        $stmt = $pdo->prepare("UPDATE users SET profile_photo=?, bio=?, instagram=?, twitter=?, linkedin=? WHERE id=?");
        $stmt->execute([$foto, $bio, $instagram, $twitter, $linkedin, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET bio=?, instagram=?, twitter=?, linkedin=? WHERE id=?");
        $stmt->execute([$bio, $instagram, $twitter, $linkedin, $_SESSION['user_id']]);
    }

    echo "<script>location.href='user?user=".urlencode($user['username'])."';</script>";
    exit;
}

?>

<div class="max-w-7xl mx-auto p-6">
    <!-- PROFILE -->
    <div class="flex flex-col md:flex-row gap-8 items-center md:items-start mb-10" data-aos="fade-down">
        <div class="flex-shrink-0">
            <?php if ($user['profile_photo'] && file_exists($user['profile_photo'])): ?>
                <img src="<?= htmlspecialchars($user['profile_photo']) ?>" class="w-36 h-36 rounded-full object-cover border-4 border-gray-200 shadow-md">
            <?php else: ?>
                <div class="w-36 h-36 rounded-full bg-gray-400 flex items-center justify-center text-3xl text-white font-bold shadow-md"><?= strtoupper($user['username'][0]) ?></div>
            <?php endif; ?>
        </div>
        <div class="flex-1 space-y-2">
            <h2 class="text-2xl font-bold">@<?= htmlspecialchars($user['username']) ?></h2>
            <?php if (!empty($user['bio'])): ?>
                <p class="text-gray-600"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
        <div class="flex gap-6 mt-2 text-sm text-gray-600 items-center">
            <a href="followers?user=<?= urlencode($user['username']) ?>" class="hover:underline">
                <i class="ph-users"></i> <?= $fol['followers'] ?> Followers
            </a>
            <a href="following?user=<?= urlencode($user['username']) ?>" class="hover:underline">
                <i class="ph-users-three"></i> <?= $fol['following'] ?> Following
            </a>

            <?php if (!empty($user['instagram'])): ?>
                <a href="<?= htmlspecialchars($user['instagram']) ?>" target="_blank" class="text-pink-500 text-xl hover:scale-110 transition">
                    <i class="fab fa-instagram"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($user['twitter'])): ?>
                <a href="<?= htmlspecialchars($user['twitter']) ?>" target="_blank" class="text-blue-400 text-xl hover:scale-110 transition">
                    <i class="fab fa-twitter"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($user['linkedin'])): ?>
                <a href="<?= htmlspecialchars($user['linkedin']) ?>" target="_blank" class="text-blue-700 text-xl hover:scale-110 transition">
                    <i class="fab fa-linkedin"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php if ($can_edit): ?>
            <div>
                <button onclick="openEditForm()"
                    class="mt-3 inline-block bg-black text-white px-5 py-2 rounded hover:bg-gray-800 transition">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </button>
            </div>
        <?php elseif ($session_id): ?>
            <button id="followBtn"
                class="mt-3 inline-block <?= $is_following ? 'bg-gray-600 hover:bg-gray-700' : 'bg-black hover:bg-gray-800' ?> text-white px-5 py-2 rounded transition"
                data-following="<?= $is_following ? '1' : '0' ?>">
                <?= $is_following ? '<i class="fas fa-user-minus"></i> Unfollow' : '<i class="fas fa-user-plus"></i> Follow' ?>
            </button>
        <?php endif; ?>
        </div>
    <div class="flex justify-between mb-4" data-aos="fade-down">
        <div></div> <!-- dummy kiri biar dorong ke kanan -->
        <?php if (!empty($top_followers)): ?>
            <div class="text-sm mt-4 ml-10 w-fit">
                <div class="font-semibold mb-2">TOP FOLLOWERS</div>
                <?php foreach ($top_followers as $f): ?>
                    <div class="flex items-start gap-2 mb-3">
                        <?php if ($f['profile_photo'] && file_exists($f['profile_photo'])): ?>
                            <a href="user?user=<?= urlencode($f['username']) ?>">
                                <img src="<?= htmlspecialchars($f['profile_photo']) ?>" class="w-8 h-8 rounded-full object-cover" alt="<?= htmlspecialchars($f['username']) ?>">
                            </a>
                        <?php else: ?>
                            <a href="user?user=<?= urlencode($f['username']) ?>">
                                <div class="w-8 h-8 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs">
                                    <?= strtoupper($f['username'][0]) ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        <div>
                            <a href="user?user=<?= urlencode($f['username']) ?>" class="hover:underline text-gray-700">@<?= htmlspecialchars($f['username']) ?></a>
                            <?php if (!empty($f['bio'])): ?>
                                <div class="text-xs text-gray-500"><?= nl2br(htmlspecialchars($f['bio'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <!-- TABS + SEARCH -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4" data-aos="fade-right">
        <div class="flex space-x-2">
            <button class="tab-btn bg-black text-white px-4 py-2 rounded" data-tab="karya"><i class="ph-pencil-simple"></i> Publish</button>
            <button class="tab-btn bg-gray-200 px-4 py-2 rounded" data-tab="arsip"><i class="ph-archive"></i> Arsip</button>
            <button class="tab-btn bg-gray-200 px-4 py-2 rounded" data-tab="bookmark"><i class="ph-bookmark"></i> Bookmark</button>
        </div>
        <input type="text" id="searchInput" placeholder="Cari karya..." class="p-2 border rounded w-full md:w-1/3">
    </div>



    <!-- TAB CONTENT -->
    <div id="karya" class="tab-section" data-aos="fade-up">
        <?php if (empty($posts)): ?>
            <p class="text-center text-gray-500">Belum ada karya.</p>
            <p class="text-center text-gray-500"> Yu mulai tulis karyamu <a href="write" class="text-blue-500 hover:underline hover:text-blue-700">disini!</a></p>
            <div class="h-64"></div> <!-- dummy spacing -->
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ($posts as $p): ?>
                <div class="post-card border rounded-xl shadow hover:shadow-lg transition" data-title="<?= strtolower($p['title']) ?>">
                    <div class="aspect-video bg-gray-100 overflow-hidden">
                        <?php if ($p['cover'] && file_exists($p['cover'])): ?>
                            <img src="<?= htmlspecialchars($p['cover']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex justify-center items-center text-2xl font-bold text-gray-400"><?= strtoupper($p['title'][0]) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-md"><a href="read?baca=<?= urlencode($p['baca']) ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
                        <div class="text-xs text-gray-500 mt-1 flex gap-2 flex-wrap">
                            <span><?= $p['status'] == 'publish' ? '✔️ Publish' : '📝 Draft' ?></span>
                            <span>👁️ <?= $p['views'] ?></span>
                            <span><?= $p['likes'] > 0 ? '❤️' : '🤍' ?> <?= $p['likes'] ?></span>
                            <span>💬 <?= $p['comments'] ?></span>
                            <?php if (in_array($p['id'], $bookmark_ids)) echo '<span>🔖</span>'; ?>
                        </div>
                        <?php if ($can_edit): ?>
                        <div class="flex gap-2 text-xs mt-2">
                            <a href="write?baca=<?= urlencode($p['baca']) ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete?baca=<?= urlencode($p['baca']) ?>" onclick="return confirm('Hapus karya ini?')" class="text-red-500 hover:underline">Hapus</a>
                            <a href="ubah_status?user=<?= urlencode($user['username']) ?>&id=<?= $p['id'] ?>&to=archived" class="text-gray-600 hover:underline">Arsipkan</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-center mt-8 space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?user=<?= urlencode($user['username']) ?>&sort=<?= $sort ?>&page=<?= $i ?>"
                        class="px-3 py-1 rounded <?= $i == $page ? 'bg-black text-white' : 'bg-gray-200 text-black hover:bg-gray-300' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <!-- TAB ARSIP -->
    <div id="arsip" class="tab-section hidden" data-aos="fade-up">
        <?php if (empty($arsip_posts)): ?>
            <p class="text-center text-gray-500">Tidak ada karya yang diarsipkan.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($arsip_posts as $p): ?>
                <div class="post-card border rounded-xl shadow hover:shadow-lg transition" data-title="<?= strtolower($p['title']) ?>">
                    <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                        <?php if ($p['cover'] && file_exists($p['cover'])): ?>
                            <img src="<?= htmlspecialchars($p['cover']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex justify-center items-center text-2xl font-bold text-gray-400"><?= strtoupper($p['title'][0]) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg"><a href="read?baca=<?= urlencode($p['baca']) ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
                        <div class="text-xs text-gray-500 mt-1 flex gap-3 flex-wrap">
                            <span>📦 Arsip</span>
                            <span>👁️ <?= $p['views'] ?></span>
                            <span><?= $p['likes'] > 0 ? '❤️' : '🤍' ?> <?= $p['likes'] ?></span>
                            <span>💬 <?= $p['comments'] ?></span>
                        </div>
                        <?php if ($can_edit): ?>
                        <div class="flex gap-3 text-sm mt-2">
                            <a href="write?baca=<?= urlencode($p['baca']) ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete?baca=<?= urlencode($p['baca']) ?>" onclick="return confirm('Hapus karya ini?')" class="text-red-500 hover:underline">Hapus</a>
                            <a href="ubah_status?user=<?= urlencode($user['username']) ?>&id=<?= $p['id'] ?>&to=draft" class="text-green-600 hover:underline">Kembalikan ke Draft</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB BOOKMARK -->
    <div id="bookmark" class="tab-section hidden" data-aos="fade-up">
        <?php if (empty($bookmark_posts)): ?>
            <p class="text-center text-gray-500">Belum ada karya yang dibookmark.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($bookmark_posts as $p): ?>
                <div class="post-card border rounded-xl shadow hover:shadow-lg transition" data-title="<?= strtolower($p['title']) ?>">
                    <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                        <?php if ($p['cover'] && file_exists($p['cover'])): ?>
                            <img src="<?= htmlspecialchars($p['cover']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex justify-center items-center text-2xl font-bold text-gray-400"><?= strtoupper($p['title'][0]) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg"><a href="read?baca=<?= urlencode($p['baca']) ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
                        <div class="text-xs text-gray-500 mt-1 flex gap-3 flex-wrap">
                            <span>🔖 Bookmark</span>
                            <span>👁️ <?= $p['views'] ?></span>
                            <span><?= $p['likes'] > 0 ? '❤️' : '🤍' ?> <?= $p['likes'] ?></span>
                            <span>💬 <?= $p['comments'] ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- EDIT PROFILE FORM -->
<?php if ($can_edit): ?>
<div id="editForm" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-lg relative">
        <button onclick="closeEditForm()" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">&times;</button>
        <form method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Foto Profil</label>
                <input type="file" name="profile_photo" accept="image/*" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block font-semibold mb-1">Bio</label>
                <textarea name="bio" rows="3" class="w-full p-2 border rounded" placeholder="Tentang dirimu..."><?= htmlspecialchars($user['bio']) ?></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-1">Instagram</label>
                <input type="text" name="instagram" class="w-full p-2 border rounded" 
                    value="<?= htmlspecialchars($user['instagram'] ?: 'https://instagram.com/') ?>">
                <small class="text-gray-500">Kosongkan untuk menghapus Instagram.</small>
            </div>
            <div>
                <label class="block font-semibold mb-1">Twitter</label>
                <input type="text" name="twitter" class="w-full p-2 border rounded" 
                    value="<?= htmlspecialchars($user['twitter'] ?: 'https://twitter.com/') ?>">
                <small class="text-gray-500">Kosongkan untuk menghapus Twitter.</small>
            </div>
            <div>
                <label class="block font-semibold mb-1">LinkedIn</label>
                <input type="text" name="linkedin" class="w-full p-2 border rounded" 
                    value="<?= htmlspecialchars($user['linkedin'] ?: 'https://linkedin.com/in/') ?>">
                <small class="text-gray-500">Kosongkan untuk menghapus LinkedIn.</small>
            </div>
            <button type="submit" name="update_profile" class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800 transition">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
<?php endif; ?>


<!-- SCRIPT -->
<script>
    // TAB SWITCH
    const tabs = document.querySelectorAll('.tab-btn');
    const sections = document.querySelectorAll('.tab-section');
    function activateTab(tabId) {
        tabs.forEach(btn => {
            btn.classList.toggle('bg-black', btn.dataset.tab === tabId);
            btn.classList.toggle('text-white', btn.dataset.tab === tabId);
            btn.classList.toggle('bg-gray-200', btn.dataset.tab !== tabId);
        });
        sections.forEach(sec => sec.classList.add('hidden'));
        document.getElementById(tabId)?.classList.remove('hidden');
    }
    tabs.forEach(btn => btn.addEventListener('click', () => {
        activateTab(btn.dataset.tab);
        history.replaceState(null, '', `?user=<?= urlencode($user['username']) ?>&tab=${btn.dataset.tab}`);
    }));
    const urlParams = new URLSearchParams(window.location.search);
    activateTab(urlParams.get('tab') || 'karya');

    // SEARCH
    document.getElementById('searchInput')?.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.post-card').forEach(card => {
            card.style.display = card.dataset.title.includes(val) ? 'block' : 'none';
        });
    });

function openEditForm() {
    document.getElementById('editForm').classList.remove('hidden');
}
function closeEditForm() {
    document.getElementById('editForm').classList.add('hidden');
}

// ajax follow

document.getElementById('followBtn')?.addEventListener('click', function() {
    const btn = this;
    const following = btn.dataset.following === "1";
    const userId = <?= (int)$view_id ?>;

    btn.disabled = true;
    fetch('toggle_follow', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + encodeURIComponent(userId)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.dataset.following = data.following ? "1" : "0";
            btn.innerHTML = data.following 
                ? '<i class="fas fa-user-minus"></i> Unfollow' 
                : '<i class="fas fa-user-plus"></i> Follow';
            btn.className = 'mt-3 inline-block ' + 
                (data.following ? 'bg-gray-600 hover:bg-gray-700' : 'bg-black hover:bg-gray-800') +
                ' text-white px-5 py-2 rounded transition';
        }
    })
    .finally(() => btn.disabled = false);
});
</script>

<div class="h-32"></div> <!-- dummy spacing -->

<?php include 'footer.php'; ?>
