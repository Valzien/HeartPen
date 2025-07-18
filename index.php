<?php
include 'header.php';
include 'koneksi.php';
?>

<!-- Hero Section -->
<section class="bg-white py-20 px-6">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-10">
        <!-- Teks -->
        <div class="flex-1 space-y-6 text-left" data-aos="fade-right">
            <h1 class="text-4xl md:text-5xl font-bold leading-tight">
                Biarkan imajinasimu mekar,<br>lahirkan sebuah karya.
            </h1>
            <p class="text-gray-600 text-lg">
                Imajinasi kamu terlalu keren untuk disimpan sendiri.<br>Yuk bawa ke dunia, biar semua ikut kagum.
            </p>
            <a href="write.php" class="inline-block bg-black text-white px-6 py-3 rounded hover:bg-gray-900 transition">
                Mulai berkarya!
            </a>
        </div>

        <!-- Gambar -->
        <div class="flex-1" data-aos="fade-left">
            <img src="img/mockup-hero.jpg" alt="Hero Mockup" class="rounded-lg shadow-md w-full object-cover">
        </div>
    </div>
</section>

<!-- Jelajahi Section -->
<section class="py-20 px-6 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold mb-12 text-center">Jelajahi & Ekspresikan Dirimu</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            <a href="blog.php" class="block bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-newspaper text-4xl text-blue-600 mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Blog</h3>
                <p class="text-gray-600 text-sm">Baca karya penuh inspirasi, tips & cerita menarik untuk menemani harimu.</p>
            </a>

            <a href="books.php" class="block bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-book text-4xl text-green-600 mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Book (E-Book)</h3>
                <p class="text-gray-600 text-sm">Dapatkan e-book gratis atau premium, perluas wawasan kapan saja.</p>
            </a>

            <a href="write.php" class="block bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-pen-fancy text-4xl text-orange-600 mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Tulis</h3>
                <p class="text-gray-600 text-sm">Tuangkan pikiranmu, biarkan dunia membaca & merasakan karyamu.</p>
            </a>

        </div>
    </div>
</section>

<!-- Kenapa Menulis -->
<section class="py-20 px-6 bg-white">
    <div class="max-w-3xl mx-auto text-center space-y-10">
        <h2 class="text-3xl font-bold" data-aos="fade-down">Kenapa Menulis di Sini?</h2>

        <div class="space-y-6 text-left" data-aos="fade-up">
            <div class="border-b group pb-4 cursor-pointer accordion-header flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-800">Buat blog yang memukau</h3>
                <i class="fas fa-chevron-right text-sm transition-transform duration-300 group-hover:translate-x-1"></i>
            </div>
            <div class="accordion-content text-gray-600 text-sm hidden mb-4">
                Kami menyediakan platform yang dirancang untuk memudahkan kamu menulis dengan bebas, tanpa batasan. Kembangkan ide dan ceritamu ke dalam blog yang menarik dan personal.
            </div>

            <div class="border-b group pb-4 cursor-pointer accordion-header flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-800">Sunting dengan mudah</h3>
                <i class="fas fa-chevron-right text-sm transition-transform duration-300 group-hover:translate-x-1"></i>
            </div>
            <div class="accordion-content text-gray-600 text-sm hidden mb-4">
                Alat penyuntingan kami intuitif dan ramah pengguna, memungkinkan kamu untuk memperindah tulisanmu dengan cepat tanpa perlu skill teknis yang rumit.
            </div>

            <div class="border-b group pb-4 cursor-pointer accordion-header flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-800">Dan, tunjukkan segalanya</h3>
                <i class="fas fa-chevron-right text-sm transition-transform duration-300 group-hover:translate-x-1"></i>
            </div>
            <div class="accordion-content text-gray-600 text-sm hidden mb-4">
                Setelah menulis, kamu bisa langsung membagikan karyamu ke komunitas luas. Jadikan tulisanmu dikenal, dibaca, dan menginspirasi banyak orang.
            </div>
        </div>

        <div class="pt-6 flex justify-center gap-4">
            <a href="write.php" class="bg-black text-white px-5 py-2 rounded hover:bg-gray-900">Mulai</a>
            <a href="register.php" class="bg-gray-100 text-black px-5 py-2 rounded border hover:bg-gray-200">Gabung sekarang!</a>
        </div>
    </div>
</section>

<!-- Karya Populer -->
<section class="py-20 px-6 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold mb-12 text-center">Karya Populer!</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $delay = 0;
            $query = "
                SELECT posts.*, users.username, (posts.views + posts.likes_count) AS popularity 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE posts.status = 'publish' 
                ORDER BY popularity DESC 
                LIMIT 3
            ";
            $stmt = $pdo->query($query);
            $posts = $stmt->fetchAll();

            if ($posts && count($posts) > 0):
                foreach ($posts as $p):
                    $delay += 100;
                    $created_at = date('d M Y H:i', strtotime($p['created_at']));
                    echo '
                    <div class="bg-white p-4 rounded shadow hover:shadow-lg transition" 
                         data-aos="fade-up" data-aos-delay="'.$delay.'">
                        <img src="'.htmlspecialchars($p['cover']).'" 
                             alt="'.htmlspecialchars($p['image_desc']).'" 
                             class="w-full h-40 object-cover rounded mb-3" />
                        <h3 class="text-lg font-bold mb-1">'.htmlspecialchars($p['title']).'</h3>
                        <p class="text-gray-700 text-sm mb-2">'.substr(strip_tags($p['content']), 0, 80).'...</p>
                        <p class="text-xs text-gray-500">By '.htmlspecialchars($p['username']).' • '.$created_at.'</p>
                        <a href="read.php?baca=' . urlencode($p['baca']) . '" class="text-blue-600 hover:underline mt-2 inline-block text-sm">Baca Lengkap</a>
                    </div>';
                endforeach;
            else:
                echo "<p class='text-center text-gray-500'>Belum ada karya populer.</p>";
            endif;
            ?>
        </div>
    </div>
</section>


<!-- Orang Populer -->
<section class="py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold mb-12 text-center">Orang Populer!</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <?php
            $query = "
                SELECT 
                    u.full_name, u.username, u.profile_photo, u.bio,
                    SUM(p.views + p.likes_count) AS total_popularity
                FROM users u
                JOIN posts p ON u.id = p.user_id
                WHERE p.status = 'publish'
                    AND u.is_verified = 1
                    AND u.role != 'admin'
                    AND u.profile_photo IS NOT NULL
                    AND u.bio IS NOT NULL
                GROUP BY u.id
                ORDER BY total_popularity DESC
                LIMIT 3
            ";
            $stmt = $pdo->query($query);
            $users = $stmt->fetchAll();
            $delay = 0;

            if ($users && count($users) > 0):
                foreach ($users as $user):
                    $delay += 100;
                    echo '
                    <div class="bg-white p-6 rounded shadow" data-aos="fade-up" data-aos-delay="'.$delay.'">
                        <img src="' .htmlspecialchars($user['profile_photo']).'" alt="'.htmlspecialchars($user['full_name']).'" class="w-20 h-20 mx-auto rounded-full object-cover mb-4">
                        <p class="italic text-sm">"'.htmlspecialchars(substr($user['bio'], 0, 100)).'"</p>
                        <div class="mt-4">
                            <p class="font-bold">'.htmlspecialchars($user['full_name']).'</p>
                            <p class="text-xs text-gray-500">@'.htmlspecialchars($user['username']).'</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Popularitas: '.$user['total_popularity'].' poin</p>
                    </div>';
                endforeach;
            else:
                echo "<p class='text-center text-gray-500'>Belum ada orang populer saat ini.</p>";
            endif;
            ?>
        </div>
    </div>
</section>


<!-- Footer CTA -->
<section class="bg-gray-100 px-6 py-16">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between text-center md:text-left gap-6">
        <h2 class="text-2xl font-bold">Jangan ragu tuangkan imajinasimu!</h2>
        <div class="flex gap-4 justify-center">
            <a href="write.php" class="bg-black text-white px-6 py-3 rounded hover:bg-gray-900">Mulai</a>
            <a href="register.php" class="bg-gray-200 text-black px-6 py-3 rounded hover:bg-gray-300">Gabung sekarang!</a>
        </div>
    </div>
</section>

<!-- Accordion Script -->
<script>
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;
            const icon = header.querySelector('i');

            // Close all others
            document.querySelectorAll('.accordion-content').forEach(c => {
                if (c !== content) c.classList.add('hidden');
            });
            document.querySelectorAll('.accordion-header i').forEach(i => {
                if (i !== icon) i.classList.remove('rotate-90');
            });

            // Toggle current
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-90');
        });
    });
</script>

<style>
    .rotate-90 {
        transform: rotate(90deg);
    }
</style>

<?php include 'footer.php'; ?>
