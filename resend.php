<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'header.php';
include 'koneksi.php';

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<div class='flex-grow'>"; // TAMBAH INI

$email = $_GET['email'] ?? '';

if (!$email) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
            Email tidak valid.
          </div>";
    echo "</div>"; // TAMBAH INI
    include 'footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
            Email tidak ditemukan.
          </div>";
} else {
    $verification_token = substr(md5(uniqid(rand(), true)), 0, 10);
    $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $update = $pdo->prepare("UPDATE users SET verification_token = ?, token_expiry = ? WHERE email = ?");
    $update->execute([$verification_token, $token_expiry, $email]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'heartpen17@gmail.com';
        $mail->Password = 'abju bsey wkdw vvvn';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('heartpen17@gmail.com', 'HeartPen');
        $mail->addAddress($email, $user['full_name']);
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi ulang akun kamu';

        $email_enc = urlencode($email);
        $token_enc = urlencode($verification_token);

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; line-height:1.6; max-width:600px; margin:auto;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <img src='https://heartpen.free.nf/img/icon.png' alt='HeartPen' style='width: 120px;'>
                </div>
                <h2 style='color: #2c5282;'>Hi $full_name,</h2>
                <p>Terima kasih sudah mendaftar di <strong>HeartPen</strong>!<br>
                Klik tombol di bawah ini untuk verifikasi ulang akun kamu:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://heartpen.free.nf/verify.php?email=$email_enc&code=$token_enc' 
                    style='background: #3182ce; color: #fff; text-decoration: none; padding: 12px 25px; 
                            border-radius: 5px; display: inline-block; font-size: 16px;'>
                        Verifikasi Sekarang
                    </a>
                </div>

                <p style='font-size:14px;color:#555;'>Link ini berlaku selama <strong>1 jam</strong>. 
                Kalau kamu tidak merasa membuat akun ini, abaikan saja email ini.</p>

                <p style='margin-top:30px;font-size:13px;color:#999;'>Salam hangat,<br>Tim HeartPen</p>
            </div>
            ";

        $mail->send();
        echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-green-100 text-green-700 rounded'>
                Email verifikasi ulang berhasil dikirim. Silakan cek inbox.
              </div>";
    } catch (Exception $e) {
        echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
                Email gagal dikirim: {$mail->ErrorInfo}
              </div>";
    }
}

echo "</div>"; // TAMBAH INI
include 'footer.php';
?>
