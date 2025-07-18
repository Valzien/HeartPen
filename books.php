<?php include 'header.php'; include 'koneksi.php'; ?>

<div class="container mx-auto px-6 py-12">
    <h2 class="text-3xl font-bold mb-8" data-aos="fade-down">Koleksi Buku</h2>

    <form method="get" class="mb-8 flex justify-center" data-aos="fade-up">
        <input type="text" name="q" placeholder="Cari judul atau penulis..." 
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
               class="w-full md:w-1/2 p-3 border rounded"/>
    </form>

    <?php
    // Pagination setup
    $perPage = 8;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $perPage;

    $q = $_GET['q'] ?? '';
    $params = [];
    if ($q) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM books WHERE title LIKE ? OR author LIKE ?");
        $params = ["%$q%", "%$q%"];
        $stmt->execute($params);
        $totalBooks = $stmt->fetchColumn();
    } else {
        $totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
    }

    $totalPages = ceil($totalBooks / $perPage);

    if ($q) {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM books LIMIT $perPage OFFSET $offset");
        $stmt->execute();
    }

    $books = $stmt->fetchAll();
    ?>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-8" data-aos="fade-up" data-aos-delay="200">
        <?php
        if ($books) {
            $delay = 0;
            foreach ($books as $book) {
                $delay += 100; // biar muncul stagger
                $imagePath = $book['image'];
                if (substr($imagePath, 0, 4) !== 'img/') {
                    $imagePath = 'img/' . $imagePath;
                }
                echo '
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden"
                     data-aos="fade-up" data-aos-delay="'.$delay.'">
                    <img src="'.htmlspecialchars($imagePath).'" alt="'.htmlspecialchars($book['title']).'"
                         class="w-full h-64 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-blue-600 mb-1">'.htmlspecialchars($book['title']).'</h3>
                        <p class="text-gray-600 mb-1">By '.htmlspecialchars($book['author']).'</p>
                        <p class="text-yellow-500 font-semibold">★ '.number_format($book['rating'],1).'</p>
                    </div>
                </div>';
            }
        } else {
            echo "<p class='col-span-full text-center text-gray-500 text-lg' data-aos='fade-up'>Buku tidak ditemukan.</p>";
        }
        ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex justify-center items-center mt-12 space-x-4" data-aos="fade-up">
        <?php if ($page > 1): ?>
            <a href="?q=<?= urlencode($q) ?>&page=<?= $page - 1 ?>"
               class="px-4 py-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition">Previous</a>
        <?php endif; ?>
        <span class="text-gray-700">Page <?= $page ?> of <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="?q=<?= urlencode($q) ?>&page=<?= $page + 1 ?>"
               class="px-4 py-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>