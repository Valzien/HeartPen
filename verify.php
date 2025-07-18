<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Jakarta');

include 'header.php';
include 'koneksi.php';

echo "<div class='flex-grow'>";  // <== TAMBAH INI

$email = $_GET['email'] ?? '';
$code  = $_GET['code'] ?? '';

if (!$email || !$code) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
            Link verifikasi tidak valid.
          </div>";
    echo "</div>"; // <== TAMBAH INI
    include 'footer.php';
    exit;
}

// Cek data user berdasarkan email dan token verifikasi
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_token = ?");
$stmt->execute([$email, $code]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
            Data tidak ditemukan atau kode salah.
          </div>";
} elseif ($user['is_verified'] == 1) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-green-100 text-green-700 rounded'>
            Akun kamu sudah diverifikasi sebelumnya. Silakan <a href='login.php' class='underline text-blue-600'>login</a>.
          </div>";
} elseif (strtotime($user['token_expiry']) < time()) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-yellow-100 text-yellow-800 rounded'>
            Link verifikasi sudah kadaluarsa. 
            <a href='resend.php?email=" . htmlspecialchars($email, ENT_QUOTES) . "' class='underline text-blue-600'>Kirim ulang verifikasi</a>.
          </div>";
} else {
    // Update status verifikasi sekaligus hapus token
    $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, token_expiry = NULL WHERE email = ?");
    $update->execute([$email]);

    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-green-100 text-green-700 rounded'>
            Selamat, akun kamu berhasil diverifikasi. Silakan <a href='login.php' class='underline text-blue-600'>login</a>.
          </div>";
}

echo "</div>"; // <== TAMBAH INI
include 'footer.php';
?>
