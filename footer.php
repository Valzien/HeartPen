<!-- Footer Utama -->
<footer class="bg-white border-t text-sm text-gray-600">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Branding & Sosial -->
        <div>
            <h3 class="text-lg font-semibold mb-4">HeartPen</h3>
            <p class="mb-4 text-gray-500">Tempat di mana imajinasi tumbuh menjadi karya nyata.</p>
            <div class="flex space-x-4">
                <a href="#"><i class="fab fa-facebook text-xl hover:text-black"></i></a>
                <a href="#"><i class="fab fa-twitter text-xl hover:text-black"></i></a>
                <a href="https://www.instagram.com/_valzien/" target="_blank"><i class="fab fa-instagram text-xl hover:text-black"></i></a>
            </div>
        </div>

        <!-- Navigasi -->
        <div>
            <h4 class="font-semibold mb-2">Eksplor</h4>
            <ul class="space-y-1">
                <li><a href="blog.php" class="hover:text-black">Blog</a></li>
                <li><a href="books.php" class="hover:text-black">E-Book</a></li>
                <li><a href="write.php" class="hover:text-black">Tulis Karya</a></li>
            </ul>
        </div>

        <!-- Tentang -->
        <div>
            <h4 class="font-semibold mb-2">Tentang</h4>
            <ul class="space-y-1">
                <li><a href="#" class="hover:text-black">Tentang Kami</a></li>
                <li><a href="#" class="hover:text-black">Kebijakan Privasi</a></li>
                <li><a href="#" class="hover:text-black">Syarat & Ketentuan</a></li>
            </ul>
        </div>

        <!-- Kontak -->
        <div>
            <h4 class="font-semibold mb-2">Hubungi Kami</h4>
            <ul class="space-y-1">
                <li><span class="text-gray-700">Email:</span> <a href="mailto:heartpen17@gmail.com" class="hover:text-black">heartpen17@gmail.com</a></li>
                <li><span class="text-gray-700">WhatsApp:</span> <a href="https://wa.me/6285183221736" target="_blank" class="hover:text-black">+62 851-8322-1736</a></li>
                <li><span class="text-gray-700">Instagram:</span> <a href="https://instagram.com/_valzien" class="hover:text-black" target="_blank">@_valzien</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t text-center py-6">
        <p>&copy; <?= date('Y') ?> <strong>HeartPen</strong>. All rights reserved.</p>
    </div>
</footer>


<!-- Script Tambahan -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });
</script>
</body>
</html>
