<?php
include 'header.php';
include 'koneksi.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

echo "<div class='flex-grow'>";

if (!$email || !$token) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
            Link tidak valid.
          </div>";
    echo "</div>"; include 'footer.php'; exit;
}

// cek token & expiry
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND reset_token = ?");
$stmt->execute([$email, $token]);
$user = $stmt->fetch();

if (!$user || strtotime($user['reset_expiry']) < time()) {
    echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
            Link tidak valid atau sudah kadaluarsa.
          </div>";
    echo "</div>"; include 'footer.php'; exit;
}

// handle form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pass = $_POST['password'] ?? '';
    if (strlen($pass) < 6) {
        echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-red-100 text-red-700 rounded'>
                Password minimal 6 karakter.
              </div>";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?");
        $update->execute([$hash, $email]);

        echo "<div class='container mx-auto max-w-md p-6 mt-10 bg-green-100 text-green-700 rounded'>
                Password berhasil direset. <a href='login.php' class='underline text-blue-600'>Login</a>.
              </div>";
    }
}
?>

<div class="container mx-auto max-w-md p-6 mt-10 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Masukkan Password Baru</h2>
    <form method="post">
        <div class="relative mb-4">
            <input type="password" id="password" name="password" placeholder="Password baru" 
                   class="w-full px-4 py-2 border rounded pr-10" required>
            <button type="button" id="togglePassword" 
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 focus:outline-none">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <button type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition w-full">
            Reset Password
        </button>
    </form>
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


<?php
echo "</div>";
include 'footer.php';
?>
