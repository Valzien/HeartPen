<?php
// INI HARUS DI PALING ATAS
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'sql111.infinityfree.com';
$db   = 'if0_39433211_heartpen';
$user = 'if0_39433211';
$pass = 'Nugraha1013';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit("Koneksi database gagal: " . $e->getMessage());
}

date_default_timezone_set('Asia/Jakarta');
$pdo->exec("SET time_zone = '+07:00'");

// Fungsi notifikasi
if (!function_exists('sendNotification')) {
    function sendNotification($pdo, $target_user_id, $message, $link = null) {
        $sender_id = $_SESSION['user_id'] ?? null;

        if (!$sender_id || $sender_id == $target_user_id) return;

        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$sender_id]);
        $sender = $stmt->fetchColumn();

        $finalMessage = "<b>@$sender</b> $message";

        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$target_user_id, $finalMessage, $link]);
    }
}
?>
