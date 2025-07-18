-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql111.infinityfree.com
-- Generation Time: Jul 17, 2025 at 07:17 PM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39433211_heartpen`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_lists`
--

CREATE TABLE `bookmark_lists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookmark_lists`
--

INSERT INTO `bookmark_lists` (`id`, `user_id`, `name`, `is_public`, `created_at`) VALUES
(17, 1, 'sakit', 1, '2025-07-13 09:24:50'),
(6, 1, 'dodo', 0, '2025-07-11 00:41:26'),
(8, 14, 'tes', 0, '2025-07-11 19:37:20'),
(15, 21, 'Rival Nugraha', 1, '2025-07-11 21:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `rating` float DEFAULT 4.5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `image`, `rating`) VALUES
(14, 'To Kill a Mockingbird', 'Harper Lee', '1.jpeg', 4.8),
(15, '1984', 'George Orwell', '2.jpeg', 4.7),
(16, 'Pride and Prejudice', 'Jane Austen', '3.jpeg', 4.6),
(17, 'The Great Gatsby', 'F. Scott Fitzgerald', '4.jpeg', 4.5),
(18, 'Moby Dick', 'Herman Melville', '5.jpeg', 4.4),
(19, 'Jane Eyre', 'Charlotte Brontë', '6.jpeg', 4.7),
(20, 'Wuthering Heights', 'Emily Brontë', '7.jpeg', 4.6),
(21, 'Little Women', 'Louisa May Alcott', '8.jpeg', 4.5),
(22, 'Brave New World', 'Aldous Huxley', '9.jpeg', 4.4),
(23, 'Frankenstein', 'Mary Shelley', '10.jpeg', 4.6);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `content`, `created_at`) VALUES
(4, 1, 8, 'Ini komentar ya ges', '2025-07-11 01:37:39'),
(5, 18, 10, 'INI KOMENTAR', '2025-07-11 04:05:28'),
(8, 18, 8, 'AWKOKOW', '2025-07-11 04:28:37'),
(7, 18, 10, 'aduh aduh aduh', '2025-07-11 04:27:50'),
(9, 14, 11, 'tes', '2025-07-11 19:36:26'),
(10, 14, 12, 'gg mas', '2025-07-11 19:37:07'),
(18, 21, 10, 're', '2025-07-13 09:59:50'),
(19, 21, 10, 'sa', '2025-07-13 10:02:00'),
(20, 21, 10, 'asda', '2025-07-13 10:22:49'),
(21, 21, 23, 'Gua sangat mengapresiasi atas bantuan & dukungan kalian semua selama proses development HeartPen. Seneng rasanya bisa bareng-bareng ngerjain ini, ngerasain tiap progressnya, sampe akhirnya jadi makin solid kayak sekarang. Makasih banget ya ??', '2025-07-14 00:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `user_id`, `comment_id`, `created_at`) VALUES
(7, 21, 10, '2025-07-11 13:23:09'),
(6, 21, 11, '2025-07-11 13:23:06'),
(8, 1, 10, '2025-07-13 02:25:16'),
(9, 1, 16, '2025-07-13 02:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `comment_replies`
--

CREATE TABLE `comment_replies` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comment_replies`
--

INSERT INTO `comment_replies` (`id`, `comment_id`, `user_id`, `content`, `created_at`) VALUES
(14, 5, 21, 'tau', '2025-07-13 09:58:01'),
(13, 10, 21, 'jelas', '2025-07-11 22:45:02'),
(15, 19, 1, 'as', '2025-07-13 10:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`id`, `follower_id`, `following_id`, `created_at`) VALUES
(5, 1, 10, '2025-07-10 20:49:33'),
(6, 18, 1, '2025-07-10 21:05:16'),
(4, 1, 18, '2025-07-10 20:13:28'),
(7, 14, 18, '2025-07-11 12:35:51'),
(8, 14, 21, '2025-07-11 12:37:32'),
(9, 21, 14, '2025-07-11 12:38:21'),
(17, 21, 1, '2025-07-13 03:38:03'),
(18, 1, 21, '2025-07-13 03:38:11'),
(19, 1, 22, '2025-07-13 04:30:46'),
(22, 21, 22, '2025-07-13 18:12:13'),
(21, 22, 21, '2025-07-13 04:38:47');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `url`, `created_at`, `is_read`, `link`) VALUES
(2, 18, '@admin mulai mengikuti kamu.', NULL, '2025-07-10 20:09:46', 1, NULL),
(3, 18, '@admin mulai mengikuti kamu.', NULL, '2025-07-10 20:13:28', 0, NULL),
(4, 10, '@admin mulai mengikuti kamu.', NULL, '2025-07-10 20:49:33', 0, NULL),
(6, 18, '@dabonn mulai mengikuti kamu.', NULL, '2025-07-11 12:35:51', 0, NULL),
(8, 14, '@Valzien mulai mengikuti kamu.', NULL, '2025-07-11 12:38:21', 0, NULL),
(11, 14, '?? menyukai komentarmu di: <b>Saat Kebaikan Disalahartikan Jadi Cinta</b>', 'read.php?baca=saat-kebaikan-disalahartikan-jadi-cinta#comment-10', '2025-07-13 02:25:16', 0, NULL),
(50, 21, '<b>@admin</b> menyukai postinganmu: \"Senyum Kupu-Kupu Biru\"</a>', NULL, '2025-07-14 15:39:11', 0, 'read.php?baca=senyum-kupu-kupu-biru'),
(49, 21, '<b>@admin</b> menyukai postinganmu: \"Senyum Kupu-Kupu Biru\"</a>', NULL, '2025-07-14 14:32:03', 0, 'read.php?baca=senyum-kupu-kupu-biru'),
(19, 18, '? membalas komentarmu di: <b>ADUHHH</b>', 'read.php?baca=aduhhh#comment-5', '2025-07-13 02:58:01', 0, NULL),
(48, 22, '<b>@admin</b> menyukai postinganmu: \"Serunya Proses Development\"</a>', NULL, '2025-07-13 18:12:57', 0, 'read.php?baca=serunya-proses-development'),
(46, 22, '<b>@admin</b> menyukai postinganmu: \"Serunya Proses Development\"</a>', NULL, '2025-07-13 18:01:58', 0, 'read.php?baca=serunya-proses-development'),
(47, 22, '<b>@Valzien</b> mulai mengikuti kamu.', NULL, '2025-07-13 18:12:13', 0, 'user.php?user=Valzien'),
(45, 22, '<b>@admin</b> menyukai postinganmu: \"Serunya Proses Development\"</a>', NULL, '2025-07-13 18:01:34', 0, 'read.php?baca=serunya-proses-development'),
(44, 22, '<b>@Valzien</b> mengomentari postinganmu: \"Serunya Proses Development\"', NULL, '2025-07-13 17:00:08', 0, 'read.php?baca=serunya-proses-development#comment-21'),
(43, 22, '<b>@Valzien</b> menyukai postinganmu: \"Serunya Proses Development\"</a>', NULL, '2025-07-13 16:57:54', 0, 'read.php?baca=serunya-proses-development'),
(42, 21, '<b>@AlfredChandra</b> menyukai postinganmu: \"Saat Kebaikan Disalahartikan Jadi Cinta\"</a>', NULL, '2025-07-13 05:20:01', 1, 'read.php?baca=saat-kebaikan-disalahartikan-jadi-cinta'),
(41, 21, '<b>@AlfredChandra</b> menyukai postinganmu: \"Saat Kebaikan Disalahartikan Jadi Cinta\"</a>', NULL, '2025-07-13 05:19:57', 1, 'read.php?baca=saat-kebaikan-disalahartikan-jadi-cinta'),
(40, 21, '<b>@AlfredChandra</b> mulai mengikuti kamu.', NULL, '2025-07-13 04:38:47', 1, 'user.php?user=AlfredChandra'),
(38, 22, '<b>@admin</b> mulai mengikuti kamu.', NULL, '2025-07-13 04:30:46', 0, 'user.php?user=admin'),
(39, 22, '<b>@Valzien</b> mulai mengikuti kamu.', NULL, '2025-07-13 04:38:23', 0, 'user.php?user=Valzien');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `baca` varchar(255) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `image_desc` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('draft','pending','publish','archived') DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `baca`, `cover`, `image_desc`, `content`, `created_at`, `status`, `views`, `likes_count`) VALUES
(12, 21, 'Saat Kebaikan Disalahartikan Jadi Cinta', 'saat-kebaikan-disalahartikan-jadi-cinta', 'uploads/1752237385_68710549dd597.jpg', 'Dua orang di tempat yang sama, tapi hatinya jauh. Senyum yang pernah menyelamatkan… kini jadi tempat paling sulit untuk pulang.', '<div>Ada satu fase dalam hidup, di mana lu nggak nyari siapa-siapa. Lu cuma butuh seseorang buat duduk di samping lu, tanpa banyak tanya, tanpa harus ngerti semua. Dan saat itu datang... dia ada.</div><div><br>Senyumnya, suaranya, bahkan cara dia dengerin lu cerita — semua ngebuat lu ngerasa: <em>“Mungkin kali ini… gua nggak sendirian.”</em> Lu mulai bangkit pelan-pelan. Dari titik paling gelap, lu belajar berdiri lagi. Karena ada dia. Karena lu ngerasa ditemenin. Dan saat itu, dia pernah bilang ke lu, <strong><em>“Gua gamau kehilangan sesuatu yang berharga lagi di hidup gua.”</em></strong> Kalimat itu… jadi titik nyala yang sempat hidupin lu kembali.</div><div><br>Tapi ternyata... dia nggak pernah nunggu. Dia nggak pernah punya maksud lain selain jadi orang baik. Dia cuma hadir — sesederhana itu. Sedangkan lu? Lu udah nulis semua kemungkinan indah di kepala lu sendiri. Lu jatuh cinta, pelan-pelan, tanpa sadar, tanpa validasi.</div><div><br>Dan itu sakit. Bukan karena dia jahat. Tapi karena lu kecewa sama ekspektasi lu sendiri. Lu ngerasa dikhianati — bukan sama dia, tapi sama harapan lu sendiri yang terlalu liar. Lu nyangka senyumnya punya makna, padahal cuma ramah. Lu pikir dia peduli lebih, padahal cuma simpati. Tapi terlepas dari itu semua, senyumnya memang sangat indah. Bahkan lu sering menatapnya diam-diam. Lu nggak mau senyumnya itu hilang…</div><div><br>Akhirnya lu sadar... ini bukan tentang dia. Ini tentang lu, yang pernah hancur dan pengen percaya lagi. Lu yang nyari pegangan dalam bentuk manusia, dan akhirnya... jatuh ke dalam jurang harapan palsu yang lu bangun sendiri.</div><div><br>Ada satu bagian dari lagu Snuff dari band Slipknot yang selalu berhasil nyeret perasaan lu waktu inget semua ini:</div><blockquote><strong><em><br>\"If you love me, let me go...\"</em></strong></blockquote><div><br>Lu cinta, tapi lu tau nggak bisa. Lu pengen deket, tapi lu tau dia nggak pernah ngarah ke sana. Jadi lu mundur. Bukan karena udah nggak sayang. Tapi karena akhirnya lu sadar — cinta yang sepihak cuma bikin lu lebih patah.</div><div><br>Tapi nggak apa-apa. Karena dari semua luka itu, lu jadi tau rasanya bertahan. Lu belajar, kalau nggak semua kehadiran harus lu artiin lebih. Kadang... orang cuma mampir, dan itu nggak apa-apa.</div><div><br>Dan lu? Lu tetep hidup. Masih dengan luka yang sama, tapi kali ini lebih bijak. Karena lu pernah dikhianati... oleh harapan lu sendiri.</div><div><br>Tapi ya, walaupun semua itu nyakitin, lu tetap harus bersyukur karena pernah ketemu dia.</div><div><br>Karena di tengah jatuhnya lu, dia dateng. Karena dia, lu pernah ngerasain hidup yang lebih berwarna. Ada tawa yang nggak lu sangka bakal lu punya di masa-masa itu. Mungkin dia emang nggak pernah punya rasa yang sama. Tapi buat lu... senyum dia masih jadi tempat lu tersesat sampai sekarang. Dan itu nggak mudah buat dilupain.</div><div><br>Dia juga yang pertama ngajarin lu buat nulis. Buat menuangin perasaan, rasa sesak, dan semua ekspresi yang nggak bisa lu keluarin lewat omongan. Dia yang nunjukin bahwa kata-kata bisa jadi pelarian, bisa jadi penyembuh.</div><div><br>Senyum itu... indah banget. Ada sesuatu di baliknya yang bikin lu susah berpaling. Tapi nggak apa-apa. Semoga, di mana pun dia sekarang, dia terus senyum... karena senyumnya itu, secara nggak langsung, nyelametin lu juga.</div><div><br>Kalau suatu hari nanti lu bisa nemuin pintu keluar, lu harus tetap inget… pernah ada seseorang, yang cuma lewat kebaikannya, bisa bikin lu jatuh sedalam ini.</div><div><br>Tapi sejujurnya… lu masih pengen deket sama dia. Bukan buat maksa rasa yang sama, tapi karena lu cuma pengen deket — kayak dulu. Lu tau sekarang, kebaikannya bukan cinta. Dan itu nggak papa. Mungkin kalau kalian bisa balik temenan lagi, semuanya bakal baik-baik aja. Tanpa salah arti, tanpa luka yang sama.</div>', '2025-07-11 12:36:25', 'publish', 0, 4),
(23, 22, 'Serunya Proses Development', 'serunya-proses-development', 'uploads/1752382609_68733c91a0e7f.jpg', 'sumber: kamera handphone gua sendiri', '<div>Suatu malam teman gua, Rival, ngabarin kalo dia baru bikin website menulis. Senang rasanya bisa punya medium baru untuk menulis. Apalagi yang dibikin sama teman sendiri.<br><br>Sejauh ini, gua menulis di banyak platform seperti: <strong>Medium</strong>, untuk menulis cerita pendek. <strong>Wattpad</strong>, untuk tulisan senandika yang gua buat. Lalu yang terakhir website pribadi yang gua bikin pake <strong>Wordpress</strong>, atau simpelnya blog pribadi.<br><br>Doa baiknya, semoga website Heartpen ini bisa berkembang lebih baik kedepannya. Mungkin bisa jadi opsi buat gua migrasi dari Wordpress ke sini. Rival, juga kirim pesan WA ke gua, minta cek website ini pas baru jadi. Gua dikasih tugas buat cek apa yang salah atau bisa ditambahin apa biar lebih enak websitenya.<br><br>Jujur gua suka ngelewatin fase <em>development,&nbsp;</em>kita menciptakan, cari kekutu yang ada, diperbaiki, dan siap release.<br><br>Yang biasa gua lakukan ketika baru selesai menulis cerita pendek adalah mengirim draft 1 dua-tiga orang teman, minta mereka baca, lalu minta untuk dikomentari cerita yang gua tulis jelek dan kurangnya di mana.<br><br>Buat gua diskusi masalah dan solusi itu yang seru ditahap penciptaan sebuah karya. Gua suka ketika tulisan gua (cerita pendek) dikeroyok banyak orang, gua jadi punya peluang untuk bikin cerita gua lebih bagus dari draft 1-nya.<br><br>Saat tulisan ini gua tulis, Rival masih terus revisi, dan cari kekutu yang ada di website Heartpen ini. Mantap Pal, semoga Heartpen bisa jadi website yang besar di masa depan. 👍🏻🚀</div>', '2025-07-13 04:56:49', 'publish', 0, 3),
(24, 21, 'Senyum Kupu-Kupu Biru', 'senyum-kupu-kupu-biru', 'uploads/1752426341_6873e765ab1e9.jpg', 'Photo by Fernanda Kelly on Pinterest', '<div><br>Pernah ada satu sosok yang hinggap di hidup gua.<br>Gua sebut dia kupu-kupu.<br>Bukan tanpa alasan — dia seindah itu.<br>Kayak kupu-kupu biru yang terbang bebas, warnanya mencuri perhatian tanpa harus berusaha.</div><div><br>Dia…<br>dengan senyum yang selalu ceria, dengan tawa yang bisa nular ke hati siapa aja, bikin sekitar dia ikut berwarna, termasuk dunia kecil gua yang dulu kelam.</div><div><br>Saat orang-orang pergi, saat sepi jadi teman, dia tetap ada.<br>Duduk diam di samping gua, ngedengerin semua cerita gua tanpa menghakimi, ngingetin gua buat jaga diri — hal-hal kecil yang ternyata berarti segalanya.</div><div><br>Gua nggak tau berapa banyak rahasia yang dia simpan di balik senyumnya.<br>Yang gua tau, dia pernah jadi alasan kenapa gua bisa berdiri lagi.</div><div><br>Mungkin dia nggak pernah tau, mungkin juga nggak pernah sadar. Tapi setiap senyumnya, setiap tatapannya, itu nyelametin gua dari gelap yang hampir menelan.</div><div><br>Gua nggak tau dia bakal baca ini lagi atau nggak.<br>Tapi kalau iya… terima kasih.<br>Terima kasih udah bikin hidup gua lebih berwarna, lebih berarti, lebih hidup.</div><div><br>Dia ngajarin gua beberapa hal yaitu, buat selalu jadi diri sendiri, buat mencintai semua sisi yang pernah gua benci dari diri gua sendiri.</div><div><br>Gua doain, semoga semua harapan kecil yang pernah dia bisikin ke langit,<br>pelan-pelan jatuh satu per satu ke tangannya.</div><div><br>Tetaplah jadi kupu-kupu biru yang gua kenal, yang terbang bebas, yang tanpa sadar pernah nyelamatin seseorang yang diam-diam hampir runtuh.</div><div><br>Kalau suatu hari nanti kita cuma saling tau dari kejauhan, nggak apa-apa. Karena sekali lu hadir, lu udah jadi bagian kecil dari perjalanan hidup gua… yang nggak akan pernah gua lupain.</div><div><br>Stay beautiful, a little blue butterfly. 🦋</div><div><br>Cyph3Rus (｡•̀ᴗ-)✧</div>', '2025-07-13 17:05:41', 'publish', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `post_bookmarks`
--

CREATE TABLE `post_bookmarks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `list_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `post_bookmarks`
--

INSERT INTO `post_bookmarks` (`id`, `user_id`, `post_id`, `created_at`, `list_id`) VALUES
(14, 14, 11, '2025-07-11 12:36:21', 0),
(13, 1, 8, '2025-07-10 18:37:29', 6),
(15, 14, 12, '2025-07-11 12:37:22', 7),
(16, 21, 12, '2025-07-11 13:04:14', 0),
(17, 1, 12, '2025-07-13 02:24:52', 17),
(18, 1, 11, '2025-07-13 03:41:56', 6);

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(16, 1, 8, '2025-07-11 01:25:19'),
(17, 1, 10, '2025-07-11 02:42:05'),
(18, 18, 11, '2025-07-11 02:48:07'),
(20, 14, 11, '2025-07-11 19:35:44'),
(38, 21, 12, '2025-07-13 11:14:35'),
(22, 14, 12, '2025-07-11 19:37:00'),
(29, 1, 12, '2025-07-13 09:17:33'),
(37, 21, 10, '2025-07-13 10:23:02'),
(40, 22, 12, '2025-07-13 12:20:01'),
(41, 22, 23, '2025-07-13 12:20:14'),
(42, 21, 23, '2025-07-13 23:57:54'),
(43, 21, 24, '2025-07-14 00:05:49'),
(46, 1, 23, '2025-07-14 01:12:57'),
(48, 1, 24, '2025-07-14 22:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `post_views`
--

CREATE TABLE `post_views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `post_views`
--

INSERT INTO `post_views` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(1, 1, 8, '2025-07-11 01:04:20'),
(2, 1, 8, '2025-07-11 01:08:21'),
(3, 1, 8, '2025-07-11 01:10:17'),
(4, 1, 8, '2025-07-11 01:11:31'),
(5, 1, 8, '2025-07-11 01:11:58'),
(6, 1, 8, '2025-07-11 01:12:15'),
(7, 1, 8, '2025-07-11 01:14:37'),
(8, 1, 8, '2025-07-11 01:17:55'),
(9, 1, 8, '2025-07-11 01:20:28'),
(10, 1, 8, '2025-07-11 01:24:23'),
(11, 1, 8, '2025-07-11 01:32:26'),
(12, 1, 8, '2025-07-11 01:32:41'),
(13, 1, 8, '2025-07-11 01:35:26'),
(14, 1, 8, '2025-07-11 01:35:35'),
(15, 1, 8, '2025-07-11 01:35:40'),
(16, 1, 8, '2025-07-11 01:36:25'),
(17, 1, 8, '2025-07-11 01:37:25'),
(18, 1, 9, '2025-07-11 01:40:44'),
(19, 1, 8, '2025-07-11 02:21:45'),
(20, 1, 8, '2025-07-11 02:34:47'),
(21, 1, 8, '2025-07-11 02:39:19'),
(22, 1, 10, '2025-07-11 02:42:02'),
(23, 18, 11, '2025-07-11 02:48:03'),
(24, 18, 11, '2025-07-11 03:31:54'),
(25, 18, 10, '2025-07-11 04:05:21'),
(26, 18, 8, '2025-07-11 04:06:12'),
(27, 18, 10, '2025-07-11 04:27:45'),
(28, 18, 8, '2025-07-11 04:28:30'),
(29, 1, 11, '2025-07-11 19:18:17'),
(30, 1, 11, '2025-07-11 19:22:10'),
(31, 1, 10, '2025-07-11 19:22:16'),
(32, 1, 8, '2025-07-11 19:22:21'),
(33, 14, 11, '2025-07-11 19:33:51'),
(34, 14, 11, '2025-07-11 19:35:41'),
(35, 14, 11, '2025-07-11 19:36:05'),
(36, 21, 12, '2025-07-11 19:36:33'),
(37, 14, 12, '2025-07-11 19:36:54'),
(38, 21, 12, '2025-07-11 19:37:28'),
(39, 14, 12, '2025-07-11 19:37:37'),
(40, 14, 12, '2025-07-11 19:38:45'),
(41, 21, 12, '2025-07-11 19:39:54'),
(42, 21, 12, '2025-07-11 19:40:20'),
(43, 21, 12, '2025-07-11 19:49:36'),
(44, 21, 12, '2025-07-11 19:49:51'),
(45, 21, 12, '2025-07-11 19:49:55'),
(46, 21, 12, '2025-07-11 19:52:15'),
(47, 21, 12, '2025-07-11 19:52:23'),
(48, 21, 12, '2025-07-11 19:59:43'),
(49, 21, 12, '2025-07-11 19:59:58'),
(50, 21, 12, '2025-07-11 20:01:04'),
(51, 21, 12, '2025-07-11 20:04:01'),
(52, 21, 12, '2025-07-11 20:04:50'),
(53, 21, 12, '2025-07-11 20:09:10'),
(54, 21, 11, '2025-07-11 20:12:49'),
(55, 21, 12, '2025-07-11 20:13:29'),
(56, 21, 12, '2025-07-11 20:17:51'),
(57, 21, 12, '2025-07-11 20:18:02'),
(58, 21, 12, '2025-07-11 20:20:57'),
(59, 21, 12, '2025-07-11 20:21:23'),
(60, 21, 12, '2025-07-11 20:21:30'),
(61, 21, 12, '2025-07-11 20:21:44'),
(62, 21, 12, '2025-07-11 20:21:54'),
(63, 21, 12, '2025-07-11 20:22:06'),
(64, 21, 12, '2025-07-11 20:22:29'),
(65, 21, 12, '2025-07-11 20:22:45'),
(66, 21, 12, '2025-07-11 20:23:04'),
(67, 21, 12, '2025-07-11 20:27:23'),
(68, 21, 12, '2025-07-11 20:27:37'),
(69, 21, 12, '2025-07-11 20:29:20'),
(70, 21, 12, '2025-07-11 20:29:26'),
(71, 21, 12, '2025-07-11 20:30:48'),
(72, 21, 12, '2025-07-11 20:35:11'),
(73, 21, 12, '2025-07-11 20:35:18'),
(74, 21, 12, '2025-07-11 20:37:13'),
(75, 21, 12, '2025-07-11 20:40:14'),
(76, 21, 12, '2025-07-11 20:42:52'),
(77, 21, 12, '2025-07-11 20:43:07'),
(78, 21, 12, '2025-07-11 20:43:15'),
(79, 21, 12, '2025-07-11 20:46:13'),
(80, 21, 12, '2025-07-11 20:46:25'),
(81, 21, 12, '2025-07-11 20:49:45'),
(82, 21, 12, '2025-07-11 20:49:55'),
(83, 21, 12, '2025-07-11 20:51:46'),
(84, 21, 12, '2025-07-11 20:51:54'),
(85, 21, 12, '2025-07-11 20:52:43'),
(86, 21, 12, '2025-07-11 20:52:50'),
(87, 21, 12, '2025-07-11 20:53:34'),
(88, 21, 12, '2025-07-11 20:53:39'),
(89, 21, 12, '2025-07-11 20:56:03'),
(90, 21, 12, '2025-07-11 20:56:11'),
(91, 21, 12, '2025-07-11 20:56:57'),
(92, 21, 12, '2025-07-11 21:00:18'),
(93, 21, 12, '2025-07-11 21:00:29'),
(94, 21, 12, '2025-07-11 21:11:30'),
(95, 21, 12, '2025-07-11 21:11:53'),
(96, 21, 12, '2025-07-11 21:11:57'),
(97, 21, 12, '2025-07-11 21:12:01'),
(98, 21, 12, '2025-07-11 21:12:08'),
(99, 21, 12, '2025-07-11 21:12:15'),
(100, 21, 12, '2025-07-11 21:20:23'),
(101, 21, 12, '2025-07-11 21:20:37'),
(102, 21, 12, '2025-07-11 21:20:55'),
(103, 21, 12, '2025-07-11 21:26:15'),
(104, 21, 12, '2025-07-11 21:26:51'),
(105, 21, 12, '2025-07-11 21:26:58'),
(106, 21, 12, '2025-07-11 21:29:29'),
(107, 21, 12, '2025-07-11 21:34:33'),
(108, 21, 12, '2025-07-11 21:36:47'),
(109, 21, 12, '2025-07-11 21:38:01'),
(110, 21, 12, '2025-07-11 21:41:00'),
(111, 21, 12, '2025-07-11 21:41:28'),
(112, 21, 12, '2025-07-11 21:42:19'),
(113, 21, 12, '2025-07-11 21:43:12'),
(114, 21, 12, '2025-07-11 21:44:05'),
(115, 21, 12, '2025-07-11 21:44:27'),
(116, 21, 12, '2025-07-11 21:44:43'),
(117, 21, 12, '2025-07-11 21:45:02'),
(118, 21, 12, '2025-07-11 21:45:09'),
(119, 21, 12, '2025-07-11 21:45:17'),
(120, 21, 12, '2025-07-11 21:46:38'),
(121, 21, 12, '2025-07-11 21:47:31'),
(122, 21, 12, '2025-07-11 21:48:15'),
(123, 21, 12, '2025-07-11 21:49:00'),
(124, 21, 12, '2025-07-11 21:49:23'),
(125, 21, 12, '2025-07-11 21:49:36'),
(126, 21, 12, '2025-07-11 21:51:41'),
(127, 21, 12, '2025-07-11 21:54:11'),
(128, 21, 12, '2025-07-11 21:58:55'),
(129, 21, 12, '2025-07-11 22:03:49'),
(130, 21, 12, '2025-07-11 22:08:35'),
(131, 21, 12, '2025-07-11 22:10:40'),
(132, 21, 12, '2025-07-11 22:10:46'),
(133, 21, 12, '2025-07-11 22:13:28'),
(134, 21, 11, '2025-07-11 22:13:31'),
(135, 21, 8, '2025-07-11 22:13:34'),
(136, 21, 13, '2025-07-11 22:14:20'),
(137, 21, 21, '2025-07-11 22:39:56'),
(138, 21, 22, '2025-07-11 22:41:21'),
(139, 21, 12, '2025-07-11 22:43:03'),
(140, 21, 12, '2025-07-11 22:43:20'),
(141, 21, 12, '2025-07-11 22:44:00'),
(142, 21, 12, '2025-07-11 22:44:31'),
(143, 21, 12, '2025-07-11 22:48:40'),
(144, 1, 12, '2025-07-13 09:17:29'),
(145, 1, 12, '2025-07-13 09:24:34'),
(146, 1, 12, '2025-07-13 09:34:08'),
(147, 1, 12, '2025-07-13 09:43:52'),
(148, 21, 10, '2025-07-13 09:50:32'),
(149, 1, 10, '2025-07-13 09:53:21'),
(150, 1, 10, '2025-07-13 09:53:28'),
(151, 1, 10, '2025-07-13 09:55:57'),
(152, 21, 10, '2025-07-13 10:01:04'),
(153, 1, 10, '2025-07-13 10:04:30'),
(154, 21, 10, '2025-07-13 10:06:25'),
(155, 21, 10, '2025-07-13 10:17:21'),
(156, 1, 10, '2025-07-13 10:17:50'),
(157, 1, 11, '2025-07-13 10:41:51'),
(158, 21, 11, '2025-07-13 11:02:12'),
(159, 21, 10, '2025-07-13 11:02:38'),
(160, 21, 10, '2025-07-13 11:05:11'),
(161, 21, 10, '2025-07-13 11:05:46'),
(162, 21, 12, '2025-07-13 11:05:53'),
(163, 21, 12, '2025-07-13 11:07:56'),
(164, 21, 12, '2025-07-13 11:08:50'),
(165, 21, 11, '2025-07-13 11:09:00'),
(166, 21, 10, '2025-07-13 11:09:05'),
(167, 21, 12, '2025-07-13 11:09:10'),
(168, 21, 12, '2025-07-13 11:14:13'),
(169, 1, 12, '2025-07-13 11:32:09'),
(170, 22, 23, '2025-07-13 11:56:50'),
(171, 22, 12, '2025-07-13 12:19:52'),
(172, 22, 23, '2025-07-13 12:20:09'),
(173, 21, 23, '2025-07-13 23:56:42'),
(174, 21, 24, '2025-07-14 00:05:42'),
(175, 1, 12, '2025-07-14 00:09:55'),
(176, 1, 24, '2025-07-14 01:00:21'),
(177, 1, 23, '2025-07-14 01:01:13'),
(178, 1, 24, '2025-07-14 01:01:25'),
(179, 1, 23, '2025-07-14 01:01:31'),
(180, 1, 23, '2025-07-14 01:01:54'),
(181, 1, 23, '2025-07-14 01:02:06'),
(182, 1, 23, '2025-07-14 01:12:50'),
(183, 1, 24, '2025-07-14 21:31:53'),
(184, 1, 24, '2025-07-14 21:35:30'),
(185, 1, 24, '2025-07-14 22:13:03'),
(186, 1, 24, '2025-07-14 22:16:45'),
(187, 1, 24, '2025-07-14 22:17:38'),
(188, 1, 24, '2025-07-14 22:17:53'),
(189, 1, 24, '2025-07-14 22:18:09'),
(190, 1, 24, '2025-07-14 22:18:22'),
(191, 1, 24, '2025-07-14 22:20:21'),
(192, 1, 24, '2025-07-14 22:20:24'),
(193, 1, 24, '2025-07-14 22:20:29'),
(194, 1, 24, '2025-07-14 22:39:08'),
(195, 1, 24, '2025-07-14 22:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `reply_likes`
--

CREATE TABLE `reply_likes` (
  `id` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reply_likes`
--

INSERT INTO `reply_likes` (`id`, `reply_id`, `user_id`, `created_at`) VALUES
(1, 1, 21, '2025-07-11 13:35:14'),
(2, 13, 1, '2025-07-13 02:25:18'),
(4, 15, 21, '2025-07-13 03:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `profile_photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `linkedin` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `is_verified`, `verification_token`, `token_expiry`, `role`, `profile_photo`, `bio`, `instagram`, `twitter`, `linkedin`, `reset_token`, `reset_expiry`) VALUES
(1, NULL, 'admin', 'heartpen17@gmail.com', '$2y$10$kTFt9LTg6DsXq9mDd2JZveRg3dqZqrglf8KKwTuRN48bVkbmmHjFq', 1, NULL, NULL, 'admin', 'uploads/profile_1_1752099851.png', 'Admin HeartPen! Love you all!', 'https://www.instagram.com/_valzien/', '', '', NULL, NULL),
(10, 'Cahya Amaylia', 'mayrmaid', 'amaylia105@gmail.com', '$2y$10$KcPp14zi6jkbVy6kpgKtgOzSEvf4J.oaZUSjX48u36CV08mSmBTWO', 1, NULL, NULL, 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'gattan', 'dabonn', 'dabonnsihh@gmail.com', '$2y$10$R7xmpJHfX1jVsymiA9JYMe3kPnOMDENlXe9CdByziIzsNsoGpjf6y', 1, NULL, NULL, 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Budi', 'Budi', 'pbgame30k@gmail.com', '$2y$10$vPT7F5xTnJ.hrf812ogBheyOO8EJLiNrtaIqr6aHXH8HqMUozRuYi', 1, NULL, NULL, 'user', 'uploads/profile_18_1752177628.jpg', 'asdasdasdas', '', '', '', NULL, NULL),
(21, 'Rival Adistia N', 'Valzien', 'nugraharival1736@gmail.com', '$2y$10$sdVzfFHch7PUcmifN.4ok.l6yJehK.4iU53cG6IOBOqPCs7ZaYyCS', 1, NULL, NULL, 'user', 'uploads/profile_21_1752248863.jpg', 'Seseorang yang sedang mencari dirinya sendiri.', 'https://www.instagram.com/_valzien/', '', '', NULL, NULL),
(22, 'Alfred Chandra', 'AlfredChandra', 'alfredchdr@gmail.com', '$2y$10$EV6O77sOACpj/UA406DHUO6aEQhigiSJa7fuiTRuRGLaSl.Of5V5a', 1, NULL, NULL, 'user', 'uploads/profile_22_1752381151.jpg', 'Pencinta kopi Dolce yang suka nulis cerita.', 'https://instagram.com/red_chdr', '', '', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmark_lists`
--
ALTER TABLE `bookmark_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `list_id` (`list_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`);

--
-- Indexes for table `post_views`
--
ALTER TABLE `post_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `reply_likes`
--
ALTER TABLE `reply_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reply_id` (`reply_id`,`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmark_lists`
--
ALTER TABLE `bookmark_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `comment_replies`
--
ALTER TABLE `comment_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `post_views`
--
ALTER TABLE `post_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `reply_likes`
--
ALTER TABLE `reply_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
