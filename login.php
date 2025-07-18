<?php 
include 'header.php'; 
include 'koneksi.php';

$msg = '';

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $login = $_POST['login'];
    $pass  = $_POST['password'];

    // Ambil user berdasarkan email atau username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$login, $login]);
    $u = $stmt->fetch();

    if ($u) {
        // Verifikasi password
        if (password_verify($pass, $u['password'])) {
            if ($u['is_verified'] == 0) {
                $msg = "<div class='p-4 bg-yellow-100 text-yellow-800 rounded'>Akun kamu belum diverifikasi. Silakan cek email untuk aktivasi.</div>";
            } else {
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['role']    = $u['role'];
            echo "<script>location='blog.php'</script>";
            echo "<noscript><meta http-equiv='refresh' content='0; url=index.php'></noscript>";
            exit;
                        }
        } else {
            $msg = "<div class='p-4 bg-red-100 text-red-700 rounded'>Password salah. Coba lagi.</div>";
        }
    } else {
        $msg = "<div class='p-4 bg-red-100 text-red-700 rounded'>User tidak ditemukan. Cek email/username.</div>";
    }
}
?>

<div class="container mx-auto px-6 py-12 max-w-md">
    <div class="bg-white p-8 rounded-lg shadow-lg border">
        <h2 class="text-3xl font-bold mb-6 text-center">Login</h2>
        <?= $msg ?>
        <form method="post" class="space-y-4 mt-4">
            <input type="text" name="login" placeholder="Email atau Username" required 
                   class="w-full p-3 border rounded" />

            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Password" 
                       class="w-full p-3 border rounded pr-10" required />
                <button type="button" id="togglePassword" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 focus:outline-none">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <button class="bg-black text-white px-6 py-3 rounded hover:bg-gray-800 transition w-full">
                Login
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="forgot.php" class="text-blue-600 hover:underline">
                Lupa password?
            </a>
        </div>

        <p class="mt-4 text-gray-600 text-center">
            Belum punya akun? 
            <a href="register.php" class="text-blue-600 hover:underline">Daftar di sini</a>.
        </p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", function () {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
        
        this.innerHTML = type === "password" 
            ? '<i class="fas fa-eye"></i>' 
            : '<i class="fas fa-eye-slash"></i>';
    });
</script>

<?php include 'footer.php'; ?>
