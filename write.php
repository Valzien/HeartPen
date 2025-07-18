<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');
$user_id = $_SESSION['user_id'];

date_default_timezone_set('Asia/Jakarta');
$msg = '';
$edit = false;
$baca = $_GET['baca'] ?? null;

// === CEK MODE EDIT
if ($baca) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE baca = ? AND user_id = ?");
    $stmt->execute([$baca, $user_id]);
    $post = $stmt->fetch();
    if (!$post) {
        die("<div class='p-6 text-center text-red-600'>Karya tidak ditemukan atau bukan milikmu.</div>");
    }
    $edit = true;
}

// === JIKA FORM DISUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    $content = $_POST['content'];
    $desc = $_POST['image_desc'];
    $status = $_POST['status'] ?? 'draft';
    $now = date('Y-m-d H:i:s');

    $cover = $edit ? $post['cover'] : '';
    if (!empty($_FILES['cover']['name'])) {
        $target_dir = "uploads/";
        $ext = pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION);
        $new_name = time() . '_' . uniqid() . '.' . $ext;
        $cover = $target_dir . $new_name;
        move_uploaded_file($_FILES["cover"]["tmp_name"], $cover);
    }

    if ($edit) {
        $stmt = $pdo->prepare("UPDATE posts SET title=?, baca=?, content=?, cover=?, image_desc=?, status=? WHERE baca=? AND user_id=?");
        $stmt->execute([$title, $slug, $content, $cover, $desc, $status, $baca, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, baca, content, cover, image_desc, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $slug, $content, $cover, $desc, $status, $now]);
    }

    header("Location: read.php?baca=" . urlencode($slug));
    exit;
}
?>

<?php include 'header.php'; ?>


<div class="container mx-auto px-6 py-12 max-w-3xl">
    <h2 class="text-3xl font-bold mb-8 text-center" data-aos="fade-down">
        <?= $edit ? 'Edit Karyamu' : 'Tulis Karyamu!' ?>
    </h2>

    <?= $msg ?>

    <form method="post" enctype="multipart/form-data" class="space-y-6">
        <!-- Judul -->
        <div data-aos="fade-up">
            <input type="text" name="title" placeholder="Judul Karya" required class="w-full p-3 border rounded"
                value="<?= $edit ? htmlspecialchars($post['title']) : '' ?>">
        </div>

        <!-- Upload Cover -->
        <div data-aos="fade-up" data-aos-delay="100">
            <label class="block mb-2 font-semibold">Upload Cover (4:3)</label>
            <input type="file" name="cover" accept="image/*" class="w-full p-3 border rounded">
            <?php if ($edit && $post['cover']): ?>
                <p class="text-sm text-gray-500 mt-1">Cover saat ini:
                    <a href="<?= $post['cover'] ?>" target="_blank" class="text-blue-600 hover:underline">
                        <?= basename($post['cover']) ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>

        <!-- Deskripsi Gambar -->
        <div data-aos="fade-up" data-aos-delay="200">
            <input type="text" name="image_desc" placeholder="Deskripsi Gambar" required class="w-full p-3 border rounded"
                value="<?= $edit ? htmlspecialchars($post['image_desc']) : '' ?>">
        </div>

        <!-- Editor (TANPA AOS) -->
        <div>
            <label class="block mb-2 font-semibold">Isi Karya</label>
            <input id="x" type="hidden" name="content" value="<?= $edit ? htmlspecialchars($post['content']) : '' ?>">
            <trix-editor input="x" class="bg-white p-3 border rounded"></trix-editor>
        </div>

        <!-- Status (TANPA AOS) -->
        <div>
            <label class="block mb-2 font-semibold">Status</label>
            <select name="status" class="w-full p-3 border rounded">
                <option value="publish" <?= $edit && $post['status'] == 'publish' ? 'selected' : '' ?>>Publish</option>
                <option value="draft" <?= $edit && $post['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
            </select>
        </div>

        <!-- Tombol (TANPA AOS) -->
        <button type="submit" class="bg-black text-white px-6 py-3 rounded hover:bg-gray-800 transition">
            <?= $edit ? 'Simpan Perubahan' : 'Kirim Karya' ?>
        </button>
    </form>
</div>

<!-- TRIX Editor -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

<!-- AOS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>

<?php include 'footer.php'; ?>
