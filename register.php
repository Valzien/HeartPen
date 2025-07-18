<?php
include 'header.php';
include 'koneksi.php';

require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = '';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $full_name = trim($_POST['fullname']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    if ($password != $confirm) {
        $msg = "<div class='p-4 bg-red-100 text-red-700 rounded'>Password tidak cocok.</div>";
    } else {
        // cek unik
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $msg = "<div class='p-4 bg-yellow-100 text-yellow-800 rounded'>Username atau email sudah digunakan.</div>";
        } else {
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $token = substr(md5(uniqid(rand(), true)), 0, 10);
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, verification_token, token_expiry) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $username, $email, $passHash, $token, $token_expiry])) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'heartpen17@gmail.com';
                    $mail->Password   = 'abju bsey wkdw vvvn';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('heartpen17@gmail.com', 'HeartPen');
                    $mail->addAddress($email, $full_name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Verifikasi akun kamu';

                    $email_enc = urlencode($email);
                    $token_enc = urlencode($token);

                    $mail->Body = "
                    <div style='font-family: Arial, sans-serif; color: #333; line-height:1.6; max-width:600px; margin:auto;'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <img src='https://heartpen.free.nf/img/icon.png' alt='HeartPen' style='width: 120px;'>
                        </div>
                        <h2 style='color: #2c5282;'>Hi $full_name,</h2>
                        <p>Terima kasih sudah mendaftar di <strong>HeartPen</strong>!<br>
                        Klik tombol di bawah ini untuk verifikasi akun kamu:</p>
                        
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
                    $msg = "<div class='p-4 bg-green-100 text-green-700 rounded'>Registrasi berhasil. Cek email untuk verifikasi.</div>";
                } catch (Exception $e) {
                    $msg = "<div class='p-4 bg-red-100 text-red-700 rounded'>Gagal kirim email: {$mail->ErrorInfo}</div>";
                }
            } else {
                $msg = "<div class='p-4 bg-red-100 text-red-700 rounded'>Terjadi kesalahan. Coba lagi.</div>";
            }
        }
    }
}
?>
<div class="container mx-auto px-6 py-12 max-w-md">
    <div class="bg-white p-8 rounded-lg shadow-lg border">
        <h2 class="text-3xl font-bold mb-6 text-center text-slate-800">Register</h2>
        <?= $msg ?>
        <form method="post" class="space-y-4 mt-4">
            <input type="text" name="fullname" placeholder="Nama Lengkap" required 
                   class="w-full p-3 border rounded focus:border-blue-600 focus:ring focus:ring-blue-100" />
            <input type="text" name="username" placeholder="Username unik" required 
                   class="w-full p-3 border rounded focus:border-blue-600 focus:ring focus:ring-blue-100" />
            <input type="email" name="email" placeholder="Email aktif" required 
                   class="w-full p-3 border rounded focus:border-blue-600 focus:ring focus:ring-blue-100" />

            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Password" 
                       class="w-full p-3 border rounded pr-10 focus:border-blue-600 focus:ring focus:ring-blue-100" required />
                <button type="button" id="togglePassword" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-600">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <div class="relative">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" 
                       class="w-full p-3 border rounded pr-10 focus:border-blue-600 focus:ring focus:ring-blue-100" required />
                <button type="button" id="toggleConfirm" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-600">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <button class="bg-black text-white px-6 py-3 rounded hover:bg-gray-800 transition w-full">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-slate-600">
            Sudah punya akun? 
            <a href="login.php" class="text-blue-600 hover:underline">Login di sini</a>.
        </p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    togglePassword.addEventListener("click", () => {
        const type = passwordInput.type === "password" ? "text" : "password";
        passwordInput.type = type;
        togglePassword.innerHTML = type === "password" ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    const confirmInput = document.getElementById("confirm_password");
    const toggleConfirm = document.getElementById("toggleConfirm");
    toggleConfirm.addEventListener("click", () => {
        const type = confirmInput.type === "password" ? "text" : "password";
        confirmInput.type = type;
        toggleConfirm.innerHTML = type === "password" ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
</script>
<?php include 'footer.php'; ?>
