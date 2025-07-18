<?php
include 'header.php';
include 'koneksi.php';

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

echo "<div class='flex-grow'>";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
                Email tidak ditemukan.
              </div>";
    } else {
        $token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->execute([$token, $expiry, $email]);

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
            $mail->Subject = 'Reset Password Anda';
            $link = "https://heartpen.free.nf/reset.php?email=".urlencode($email)."&token=".urlencode($token);
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; line-height:1.6; max-width:600px; margin:auto;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <img src='https://heartpen.free.nf/img/icon.png' alt='HeartPen' style='width: 120px;'>
                </div>
                <h2 style='color: #2c5282;'>Hi {$user['full_name']},</h2>
                <p>Kami menerima permintaan untuk <strong>reset password</strong> akun kamu di <strong>HeartPen</strong>.<br>
                Klik tombol di bawah ini untuk mengatur ulang password kamu:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$link' 
                    style='background: #3182ce; color: #fff; text-decoration: none; padding: 12px 25px; 
                            border-radius: 5px; display: inline-block; font-size: 16px;'>
                        Reset Password
                    </a>
                </div>

                <p style='font-size:14px;color:#555;'>Link ini hanya berlaku selama <strong>1 jam</strong>.
                Jika kamu tidak merasa meminta reset password, abaikan saja email ini.</p>

                <p style='margin-top:30px;font-size:13px;color:#999;'>Salam hangat,<br>Tim HeartPen</p>
            </div>
            ";


            $mail->send();
            echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-green-100 text-green-700 rounded'>
                    Link reset password telah dikirim ke email kamu.
                  </div>";
        } catch (Exception $e) {
            echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
                    Gagal mengirim email: {$mail->ErrorInfo}
                  </div>";
        }
    }
}

?>

<div class="container mx-auto max-w-md p-6 mt-10 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Reset Password</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Masukkan email" 
               class="w-full px-4 py-2 mb-4 border rounded" required>
        <button type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition w-full">
            Kirim Link Reset
        </button>
    </form>
</div>

<?php
echo "</div>";
include 'footer.php';
?>
