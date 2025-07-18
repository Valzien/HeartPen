<?php 
if (session_status() === PHP_SESSION_NONE) session_start(); 
require_once 'koneksi.php';

$userPhoto = 'img/default-profile.png';
$username = '';
$notifList = [];

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, profile_photo FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        $username = htmlspecialchars($user['username']);
        if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])) {
            $userPhoto = htmlspecialchars($user['profile_photo']);
        }
    }

    // ambil 3 notif terbaru
    $stmtNotif = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
    $stmtNotif->execute([$_SESSION['user_id']]);
    $notifList = $stmtNotif->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>HeartPen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="img/icon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        html, body { overflow-x: hidden; max-width: 100%; }
    </style>
</head>
<body class="bg-white text-gray-800">

<!-- Header -->
<header class="w-full border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="index" class="text-xl font-bold tracking-tight">HeartPen</a>
        <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
            <a href="blog" class="hover:text-black transition">Blog</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="write" class="hover:text-black transition">Tulis</a>

                <!-- Dropdown notif -->
                <div class="relative">
                    <button id="notifBtn" class="relative text-lg focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white border shadow-lg rounded-lg z-50">
                        <div class="p-4 font-bold border-b">Notifikasi</div>
                        <ul class="divide-y max-h-64 overflow-y-auto">
                            <?php if ($notifList): ?>
                                <?php foreach ($notifList as $n): ?>
                                    <li class="px-4 py-3 text-sm hover:bg-gray-50 transition">
                                        <p><?= $n['message'] ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?= date('d M Y H:i', strtotime($n['created_at'])) ?></p>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="px-4 py-3 text-gray-500 text-sm">Tidak ada notifikasi baru</li>
                            <?php endif; ?>
                        </ul>
                        <div class="text-center border-t">
                            <a href="notifications" class="block py-2 text-blue-600 text-sm hover:underline">Lihat Semua</a>
                        </div>
                    </div>
                </div>

<!-- DROPDOWN PROFILE -->
            <div class="relative">
                <button id="profileBtn" class="focus:outline-none">
                    <?php if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])): ?>
                        <img src="<?= htmlspecialchars($user['profile_photo']) ?>" 
                            alt="Profile" 
                            class="w-9 h-9 rounded-full border object-cover" />
                    <?php else: ?>
                        <div class="w-9 h-9 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold border">
                            <?= strtoupper(substr($username, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </button>

                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg z-50">
                    <a href="user?user=<?= htmlspecialchars($username) ?>" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                    <a href="logout" class="block px-4 py-2 hover:bg-gray-100 text-red-600">Logout</a>
                </div>
            </div>

            <?php else: ?>
                <a href="login" class="hover:text-black transition">Masuk</a>
                <a href="register" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition">Daftar</a>
            <?php endif; ?>
        </nav>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="md:hidden text-2xl">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<!-- Mobile Menu -->

    <div id="mobile-menu" class="fixed inset-0 hidden z-50 backdrop-blur bg-white/80 flex flex-col items-center justify-start pt-24 pb-10 w-full text-base font-medium shadow-2xl rounded-t-3xl md:hidden transition-all">
        <a href="blog.php" class="w-full text-center py-3 hover:bg-gray-100 border-b">Blog</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="write" class="w-full text-center py-3 hover:bg-gray-100 border-b">Tulis</a>
            <a href="notifications" class="w-full text-center py-3 hover:bg-gray-100 border-b">Notifikasi</a>
            <a href="user?user=<?= $username ?>" class="w-full flex justify-center items-center py-4 border-b">
            <img src="<?= $userPhoto ?>" alt="Profile" class="w-12 h-12 rounded-full border object-cover" /> <span class="ml-3">Profile</span></a>
        <?php else: ?>
            <a href="login" class="w-full text-center py-3 hover:bg-gray-100 border-b">Masuk</a>
            <a href="register" class="w-full text-center py-3 hover:bg-gray-900 bg-black text-white">Daftar</a>
        <?php endif; ?>
        <button id="close-menu" class="absolute top-5 right-5 text-2xl hover:text-red-500 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>



<style>
#mobile-menu.show {
    animation: slideDown 0.3s ease forwards;
}
#mobile-menu.hide {
    animation: slideUp 0.3s ease forwards;
}

@keyframes slideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(-100%); opacity: 0; }
}

</style>

<script>
    const menuBtn = document.getElementById('mobile-menu-button');
    const closeBtn = document.getElementById('close-menu');
    const mobileMenu = document.getElementById('mobile-menu');

    menuBtn.onclick = () => {
        mobileMenu.classList.remove('hidden');
        mobileMenu.classList.add('show');
    };

    closeBtn.onclick = () => {
        mobileMenu.classList.remove('show');
        setTimeout(() => mobileMenu.classList.add('hidden'), 300);
    };
    // notif dropdown
    const notifBtn = document.getElementById("notifBtn");
    const notifDropdown = document.getElementById("notifDropdown");
    notifBtn?.addEventListener("click", function(e) {
        notifDropdown.classList.toggle("hidden");
        e.stopPropagation();
    });
    document.addEventListener("click", function(e) {
        if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
            notifDropdown.classList.add("hidden");
        }
    });

// profile dropdown
const profileBtn = document.getElementById("profileBtn");
const profileDropdown = document.getElementById("profileDropdown");

profileBtn?.addEventListener("click", function(e) {
    profileDropdown.classList.toggle("hidden");
    e.stopPropagation();
});

document.addEventListener("click", function(e) {
    if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
        profileDropdown.classList.add("hidden");
    }
});
</script>

