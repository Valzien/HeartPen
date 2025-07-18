<?php
include 'header.php';
include 'koneksi.php';

$user_id = $_SESSION['user_id'] ?? null;

$post = null;
$totalLikes = 0;
$totalViews = 0;
$isLiked = false;
$alreadyBookmarked = false;
$bookmark_lists = [];

// Redirect jika pakai ?id=...
if (isset($_GET['id']) && !isset($_GET['baca'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT baca FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    if ($result) {
        header("Location: read?baca=" . urlencode($result['baca']));
        exit;
    } else {
        echo "<div class='container mx-auto px-6 py-12'><p class='text-red-500'>Tulisan tidak ditemukan.</p></div>";
        include 'footer.php';
        exit;
    }
}

// Ambil data berdasarkan baca
if (isset($_GET['baca'])) {
    $baca = $_GET['baca'];
    $stmt = $pdo->prepare("SELECT p.*, u.username, u.profile_photo 
                           FROM posts p 
                           LEFT JOIN users u ON p.user_id = u.id 
                           WHERE p.baca = ?");
    $stmt->execute([$baca]);
    $post = $stmt->fetch();
}

// Jika tidak ditemukan
if (!$post) {
    echo "<div class='container mx-auto px-6 py-12'><p class='text-red-500'>Tulisan tidak ditemukan.</p></div>";
    include 'footer.php';
    exit;
}

// URL untuk dibagikan
$shareUrl = "https://heartpen.free.nf/read?baca=" . urlencode($post['baca']);

// Simpan view jika login
if ($user_id) {
    $insView = $pdo->prepare("INSERT INTO post_views (user_id, post_id) VALUES (?, ?)");
    $insView->execute([$user_id, $post['id']]);
}

// Total views
$viewsStmt = $pdo->prepare("SELECT COUNT(*) FROM post_views WHERE post_id = ?");
$viewsStmt->execute([$post['id']]);
$totalViews = $viewsStmt->fetchColumn();

// Total likes
$likeStmt = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ?");
$likeStmt->execute([$post['id']]);
$totalLikes = $likeStmt->fetchColumn();

// Cek like, bookmark, dan list bookmark jika user login
if ($user_id) {
    $likeCheck = $pdo->prepare("SELECT id FROM post_likes WHERE user_id = ? AND post_id = ?");
    $likeCheck->execute([$user_id, $post['id']]);
    $isLiked = $likeCheck->fetch() ? true : false;

    $bmCheck = $pdo->prepare("SELECT id FROM post_bookmarks WHERE user_id = ? AND post_id = ?");
    $bmCheck->execute([$user_id, $post['id']]);
    $alreadyBookmarked = $bmCheck->fetch() ? true : false;

    $list_stmt = $pdo->prepare("SELECT * FROM bookmark_lists WHERE user_id = ?");
    $list_stmt->execute([$user_id]);
    $bookmark_lists = $list_stmt->fetchAll();
}

// Ambil komentar
$comments = [];
$cmt_stmt = $pdo->prepare("SELECT c.*, u.username, u.profile_photo 
                            FROM comments c 
                            JOIN users u ON c.user_id = u.id
                            WHERE c.post_id = ?
                            ORDER BY c.created_at DESC");
$cmt_stmt->execute([$post['id']]);
$comments = $cmt_stmt->fetchAll();

// Ambil balasan komentar
$replies = [];
$r_stmt = $pdo->prepare("SELECT r.*, u.username, u.profile_photo 
                          FROM comment_replies r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE r.comment_id IN (SELECT id FROM comments WHERE post_id = ?) 
                          ORDER BY r.created_at ASC");
$r_stmt->execute([$post['id']]);
$replyData = $r_stmt->fetchAll();
foreach ($replyData as $reply) {
    $replies[$reply['comment_id']][] = $reply;
}
?>


<div class="container mx-auto px-6 py-12 max-w-4xl">
    <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($post['title']) ?></h1>
    <div class="mb-6 flex items-center gap-3">
        <?php if (!empty($post['profile_photo'])): ?>
            <img src="<?= htmlspecialchars($post['profile_photo']) ?>" class="w-10 h-10 rounded-full object-cover">
        <?php else: ?>
            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-bold text-white">
                <?= strtoupper(substr($post['username'],0,1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <a href="user?user=<?= urlencode($post['username']) ?>" class="font-semibold hover:underline">
                <?= htmlspecialchars($post['username']) ?>
            </a>
            <div class="text-xs text-gray-500"><?= date('d M Y H:i', strtotime($post['created_at'])) ?></div>
        </div>
    </div>

    <div class="flex items-center justify-between text-sm border-t pt-4">
<!-- KIRI: views, likes, bookmark -->
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
            👁️ <?= $totalViews ?> views
            </div>
            <div class="flex items-center gap-2 likeBtn cursor-pointer" data-id="<?= $post['id'] ?>" data-login="<?= $user_id ? '1' : '0' ?>">
            <span class="likeIcon"><?= $isLiked ? '❤️' : '🤍' ?></span> <?= $totalLikes ?> likes
            </div>
            <?php if ($user_id): ?>
            <button class="bookmarkBtn" data-post="<?= $post['id'] ?>" data-login="<?= $user_id ? '1' : '0' ?>">
                <span class="bookmarkIcon text-gray-400 transition-all">📑 Bookmark</span>
            </button>
            <?php endif; ?>
        </div>

        <!-- KANAN: tombol share -->
        <div class="relative">
            <button id="shareBtn"
            class="text-base hover:scale-110 transition"
            title="Bagikan">
            🔗
            </button>
            <!-- Dropdown share -->
            <div id="shareMenu" class="hidden absolute right-0 mt-2 bg-white border rounded shadow-md w-40 z-50">
            <a href="https://wa.me/?text=<?= urlencode($post['title'].' '.$shareUrl) ?>" target="_blank"
                class="block px-4 py-2 text-sm hover:bg-gray-100">📤 WhatsApp</a>
            <button onclick="copyLink()"
                class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">🔗 Salin Link</button>
            </div>
        </div>
    </div>

<div class="border-t my-4"></div>

    <?php if (!empty($post['cover'])): ?>
        <div class="w-full max-h-[400px] sm:max-h-[500px] md:max-h-[600px] lg:max-h-[700px] xl:max-h-[800px] flex justify-center items-center bg-gray-100 rounded shadow mb-4 overflow-hidden">
            <img src="<?= htmlspecialchars($post['cover']) ?>" alt="<?= htmlspecialchars($post['image_desc']) ?>"
                class="w-full h-auto max-h-full object-contain">
        </div>
        <?php if (!empty($post['image_desc'])): ?>
            <p class="text-sm text-gray-600 italic text-center font-light mb-8"><?= htmlspecialchars($post['image_desc']) ?></p>
        <?php endif; ?>
    <?php endif; ?>



<div class="prose max-w-none text-gray-800 mb-8"><?= nl2br($post['content']) ?></div>


    <div class="flex items-center justify-between text-sm border-t pt-4">
<!-- KIRI: views, likes, bookmark -->
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
            👁️ <?= $totalViews ?> views
            </div>
            <div class="flex items-center gap-2 likeBtn cursor-pointer" data-id="<?= $post['id'] ?>" data-login="<?= $user_id ? '1' : '0' ?>">
            <span class="likeIcon"><?= $isLiked ? '❤️' : '🤍' ?></span> <?= $totalLikes ?> likes
            </div>
            <?php if ($user_id): ?>
            <button class="bookmarkBtn" data-post="<?= $post['id'] ?>" data-login="<?= $user_id ? '1' : '0' ?>">
                <span class="bookmarkIcon text-gray-400 transition-all">📑 Bookmark</span>
            </button>
            <?php endif; ?>
        </div>

        <!-- KANAN: tombol share -->
        <div class="relative">
            <button id="shareBtn"
            class="text-base hover:scale-110 transition"
            title="Bagikan">
            🔗
            </button>
            <!-- Dropdown share -->
            <div id="shareMenu" class="hidden absolute right-0 mt-2 bg-white border rounded shadow-md w-40 z-50">
            <a href="https://wa.me/?text=<?= urlencode($post['title'].' '.$shareUrl) ?>" target="_blank"
                class="block px-4 py-2 text-sm hover:bg-gray-100">📤 WhatsApp</a>
            <button onclick="copyLink()"
                class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">🔗 Salin Link</button>
            </div>
        </div>
    </div>

    <!-- COMMENTS -->
    <div class="mt-8 border-t pt-6 space-y-6">
        <h4 class="text-lg font-bold mb-4 text-slate-700">Komentar</h4>
        <?php if ($user_id): ?>
            <form id="commentForm" class="space-y-4">
                <textarea name="comment" rows="3" placeholder="Tulis komentar..."
                    class="w-full p-3 border rounded focus:outline-none focus:ring"></textarea>
                <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800">Kirim Komentar</button>
            </form>
        <?php else: ?>
            <div class="text-sm text-slate-600">
                <a href="login" class="text-black hover:underline">Login</a> untuk menulis komentar.
            </div>
        <?php endif; ?>

        <div id="commentList">
            <?php foreach ($comments as $cmt): ?>
            <div class="flex items-start gap-4 border-b pb-4 comment-item">
                <?php if ($cmt['profile_photo']): ?>
                    <img src="<?= htmlspecialchars($cmt['profile_photo']) ?>" class="w-10 h-10 rounded-full object-cover">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-slate-400 flex items-center justify-center text-white font-bold">
                        <?= strtoupper(substr($cmt['username'],0,1)) ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1">
                    <div class="text-sm font-semibold">
                        <a href="user?user=<?= urlencode($cmt['username']) ?>" class="hover:underline">
                            <?= htmlspecialchars($cmt['username']) ?>
                        </a>
                        <span class="text-xs text-gray-500 ml-2"><?= date('d M Y H:i', strtotime($cmt['created_at'])) ?></span>
                    </div>
                    <div class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($cmt['content'])) ?></div>
                    <div class="flex gap-3 mt-1">
                        <button class="replyBtn text-xs text-blue-600 hover:underline" data-id="<?= $cmt['id'] ?>">Balas</button>
                        <button class="likeCommentBtn text-xs text-red-600 hover:underline" data-id="<?= $cmt['id'] ?>">❤️ Suka</button>
                        <?php if ($cmt['user_id'] == $user_id): ?>
                            <button class="deleteComment text-xs text-black hover:underline" data-id="<?= $cmt['id'] ?>">Hapus</button>
                        <?php endif; ?>
                    </div>
                    <div class="replyForm hidden mt-2" data-id="<?= $cmt['id'] ?>">
                        <textarea rows="2" class="w-full p-2 border rounded mb-2" placeholder="Tulis balasan..."></textarea>
                        <button class="sendReply bg-black text-white px-3 py-1 rounded" data-id="<?= $cmt['id'] ?>">Kirim</button>
                    </div>
                    <div class="replyContainer mt-3 space-y-3" data-id="<?= $cmt['id'] ?>"></div>
                </div>
            </div>
                        <?php if (!empty($replies[$cmt['id']])): ?>
                <div class="mt-3 space-y-3">
                    <?php foreach ($replies[$cmt['id']] as $rep): ?>
                        <div class="flex items-start gap-3 ml-6 reply-item">
                            <?php if ($rep['profile_photo']): ?>
                                <img src="<?= htmlspecialchars($rep['profile_photo']) ?>" class="w-8 h-8 rounded-full object-cover">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-slate-400 flex items-center justify-center text-white text-xs font-bold">
                                    <?= strtoupper(substr($rep['username'],0,1)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="bg-gray-100 px-3 py-2 rounded-md flex-1">
                                <div class="text-sm font-semibold">
                                    <a href="user?user=<?= urlencode($rep['username']) ?>" class="hover:underline">
                                        <?= htmlspecialchars($rep['username']) ?>
                                    </a>
                                    <span class="text-xs text-gray-500 ml-2"><?= date('d M Y H:i', strtotime($rep['created_at'])) ?></span>
                                </div>
                                <div class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($rep['content'])) ?></div>
                                <div class="flex gap-3 mt-1">
                                    <button class="likeReplyBtn text-xs text-red-600 hover:underline" data-id="<?= $rep['id'] ?>">❤️ Suka</button>
                                    <?php if ($rep['user_id'] == $user_id): ?>
                                        <button class="deleteReplyBtn text-xs text-black hover:underline" data-id="<?= $rep['id'] ?>">Hapus</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- MODAL BOOKMARK -->
<div id="bookmarkModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
  <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md space-y-6">

    <h3 class="text-lg font-bold text-gray-800">Simpan ke mana?</h3>

    <!-- List bookmark -->
    <div id="listContainer" class="space-y-3 max-h-60 overflow-y-auto pr-1">
      <?php if (!empty($bookmark_lists)): ?>
        <?php foreach ($bookmark_lists as $list): ?>
          <div class="group flex items-center justify-between border px-3 py-2 rounded hover:bg-gray-50 transition">
            <button type="button"
              class="listChoice w-full text-left font-medium text-sm py-1 px-2 rounded bg-black text-white hover:bg-gray-800 transition"
              data-list="<?= $list['id'] ?>">
              <?= htmlspecialchars($list['name']) ?>
              <?= $list['is_public'] ? '(Public)' : '(Private)' ?>
            </button>
            <button
              class="deleteList text-xs text-red-600 ml-2 hidden group-hover:inline transition"
              data-id="<?= $list['id'] ?>">Hapus</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-sm text-slate-500">Belum ada list bookmark.</div>
      <?php endif; ?>
    </div>

    <!-- Buat list baru -->
    <form id="newListForm" class="space-y-4 pt-4 border-t">
      <div>
        <label class="text-sm font-medium text-gray-700">Nama List</label>
        <input type="text" name="name" required
          class="w-full mt-1 px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500">
      </div>

      <div class="flex gap-6 text-sm">
        <label class="flex items-center space-x-2">
          <input type="radio" name="is_public" value="1" class="accent-black">
          <span>Public</span>
        </label>
        <label class="flex items-center space-x-2">
          <input type="radio" name="is_public" value="0" checked class="accent-black">
          <span>Private</span>
        </label>
      </div>

      <div class="flex justify-between items-center pt-2">
        <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition">Simpan</button>
        <button type="button" id="cancelBtn" class="text-gray-500 hover:underline text-sm">Batal</button>
      </div>
    </form>
  </div>
</div>


<div id="toast" class="fixed bottom-5 right-5 bg-green-600 text-white py-2 px-4 rounded shadow hidden"></div>

<script>
let selectedPostId = null;
let selectedBookmarkIcon = null;

document.querySelectorAll('.likeBtn').forEach(btn => {
    btn.addEventListener('click', function () {
        const isLoggedIn = this.dataset.login === '1';
        if (!isLoggedIn) {
            window.location.href = 'login';
            return;
        }

        const postId = this.dataset.id;
        const iconSpan = this.querySelector('.likeIcon');
        const text = this;

        fetch('like', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'post_id=' + postId
        })
        .then(res => res.json())
        .then(data => {
            iconSpan.textContent = data.status === 'liked' ? '❤️' : '🤍';
            text.childNodes[2].nodeValue = ` ${data.total} likes`;
        })
        .catch(console.error);
    });
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.bookmarkBtn')) {
        selectedPostId = e.target.closest('.bookmarkBtn').dataset.post;
        selectedBookmarkIcon = e.target.closest('.bookmarkBtn').querySelector('.bookmarkIcon');
        document.getElementById('bookmarkModal').classList.remove('hidden');
    }

    if (e.target.classList.contains('listChoice')) {
        const listId = e.target.dataset.list;
        fetch('save_bookmark', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'post_id=' + encodeURIComponent(selectedPostId) + '&list_id=' + encodeURIComponent(listId)
        })
        .then(res => res.json())
        .then(data => {
            closeModal();
            if (data.status === 'bookmarked') {
                selectedBookmarkIcon.innerHTML = '<span class="text-yellow-400 transition">🔖 Disimpan</span>';
            } else if (data.status === 'unbookmarked') {
                selectedBookmarkIcon.innerHTML = '<span class="text-gray-400 transition">📑 Bookmark</span>';
            }
        })

        .catch(console.error);
    }

    if (e.target.id === 'cancelBtn') closeModal();
});

// reply komentar
document.addEventListener('click', function(e){
    if(e.target.classList.contains('replyBtn')){
        const id = e.target.dataset.id;
        document.querySelector(`.replyForm[data-id="${id}"]`).classList.toggle('hidden');
    }
    if(e.target.classList.contains('sendReply')){
        const id = e.target.dataset.id;
        const textarea = document.querySelector(`.replyForm[data-id="${id}"] textarea`);
        fetch('reply_comment',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'id='+encodeURIComponent(id)+'&reply='+encodeURIComponent(textarea.value)
        })
        .then(res=>res.json())
        .then(data=>{
    if(data.status==='success'){
        textarea.value = '';
        // Tutup form balasan
        document.querySelector(`.replyForm[data-id="${id}"]`).classList.add('hidden');

        // Tambahkan HTML balasan ke replyContainer
        const replyBox = document.createElement('div');
        replyBox.innerHTML = data.html;
        const replyTarget = document.querySelector(`.replyContainer[data-id="${id}"]`);
        replyTarget?.appendChild(replyBox.firstElementChild);

        showToast("Balasan dikirim!");
    }

        });
    }
    if(e.target.classList.contains('likeCommentBtn')){
        const id = e.target.dataset.id;
        fetch('like_comment',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'id='+encodeURIComponent(id)
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==='liked'){
                e.target.textContent = "❤️ Disukai";
            }
        });
    }
});

// new list
document.getElementById('newListForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('create_list', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const container = document.getElementById('listContainer');
            const wrapper = document.createElement('div');
            wrapper.className = 'group flex items-center justify-between border px-3 py-2 rounded hover:bg-gray-50 transition';

            const listBtn = document.createElement('button');
            listBtn.type = 'button';
            listBtn.className = 'listChoice w-full text-left font-medium text-sm py-1 px-2 rounded bg-black text-white hover:bg-gray-800 transition';
            listBtn.dataset.list = data.list_id;
            listBtn.textContent = `${data.name} ${data.is_public == 1 ? '(Public)' : '(Private)'}`;

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'deleteList text-xs text-red-600 ml-2 hidden group-hover:inline transition';
            deleteBtn.dataset.id = data.list_id;
            deleteBtn.textContent = 'Hapus';

            wrapper.appendChild(listBtn);
            wrapper.appendChild(deleteBtn);
            container.appendChild(wrapper);


            // Tambahkan event click langsung ke tombol baru
            newBtn.addEventListener('click', function() {
                fetch('save_bookmark', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'post_id=' + encodeURIComponent(selectedPostId) + '&list_id=' + encodeURIComponent(data.list_id)
                })
                .then(res => res.json())
                .then(data => {
                    closeModal();
                    if (data.status === 'bookmarked') {
                        selectedBookmarkIcon.textContent = '🔖 Berhasil!';
                    } else {
                        selectedBookmarkIcon.textContent = '📑 Bookmark';
                    }
                });
            });

            container.appendChild(newBtn);
            this.reset();
        }
    })
    .catch(console.error);
});

function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (m) {
        return ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[m];
    });
}


//close bookmark
function closeModal() {
    document.getElementById('bookmarkModal').classList.add('hidden');
    selectedPostId = null;
    selectedBookmarkIcon = null;
}

// copy link
function copyLink(){
    navigator.clipboard.writeText("<?= $shareUrl ?>").then(() => {
        showToast("Link berhasil disalin!");
    });
}

// comment & delete
document.getElementById('commentForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const data = new FormData(form);
    data.append('post_id', <?= $post['id'] ?>);

    fetch('add_comment', { method: 'POST', body: data })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Masukkan komentar baru dengan animasi
            const commentList = document.getElementById('commentList');
            const temp = document.createElement('div');
            temp.innerHTML = data.html.trim();
            const newComment = temp.firstElementChild;

            // Tambahkan animasi masuk
            newComment.classList.add('opacity-0', 'transition-opacity');
            commentList.insertBefore(newComment, commentList.firstChild);
            setTimeout(() => newComment.classList.remove('opacity-0'), 10);

            form.reset();
            showToast("Komentar berhasil ditambahkan!");
        }
    })
    .catch(console.error);
});


document.addEventListener('click', function(e){
    if(e.target.classList.contains('deleteComment')){
        const id = e.target.dataset.id;
        fetch('delete_comment',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'id='+encodeURIComponent(id)
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==='deleted'){
            const el = e.target.closest('.comment-item');
            el.classList.add('opacity-50', 'transition-opacity');
            setTimeout(() => el.remove(), 300);
                showToast("Komentar dihapus.");
            }
        });
    }
});

function showToast(msg){
    const t=document.getElementById('toast');
    t.textContent=msg;
    t.classList.remove('hidden');
    setTimeout(()=>t.classList.add('hidden'),3000);
}

document.addEventListener('click', function(e){
    // like reply
    if (e.target.classList.contains('likeReplyBtn')) {
        const id = e.target.dataset.id;
        fetch('like_comment', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'reply_id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'liked') {
                e.target.textContent = "❤️ Disukai";
            }
        });
    }

    // delete reply
    if (e.target.classList.contains('deleteReplyBtn')) {
        const id = e.target.dataset.id;
        fetch('delete_comment', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'reply_id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'deleted') {
            const el = e.target.closest('.reply-item');
            el.classList.add('opacity-50', 'transition-opacity');
            setTimeout(() => el.remove(), 300);
                showToast("Balasan dihapus.");
            }
        });
    }
});

// Delete list
document.addEventListener('click', function(e){
    if(e.target.classList.contains('deleteList')){
        const listId = e.target.dataset.id;
        if(confirm("Yakin ingin hapus list ini?")){
            fetch('delete_list', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(listId)
            })
            .then(res => res.json())
            .then(data => {
            if (data.status === 'deleted') {
                const listItem = e.target.closest('.flex');
                listItem.remove();
                showToast("List dihapus.");

                // Reset icon bookmark kalau list ini sedang aktif
                if (selectedBookmarkIcon) {
                    selectedBookmarkIcon.textContent = '📑 Bookmark';
                    selectedBookmarkIcon = null;
                    selectedPostId = null;
                }
            }
            })
            .catch(console.error);
        }
    }
});

// Toggle menu share
document.getElementById('shareBtn')?.addEventListener('click', function() {
    const menu = document.getElementById('shareMenu');
    menu.classList.toggle('hidden');
});

// Klik di luar menu share untuk nutup
document.addEventListener('click', function(e) {
    const btn = document.getElementById('shareBtn');
    const menu = document.getElementById('shareMenu');
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.add('hidden');
    }
});


    // <div class="flex gap-3 mb-6">
    //     <a href="https://wa.me/?text=<?= urlencode($post['title'].' '.$shareUrl) ?>" target="_blank"
    //        class="px-3 py-1 bg-green-500 text-white rounded">Share WA</a>
    //     <button onclick="copyLink()" class="px-3 py-1 bg-blue-500 text-white rounded">Copy Link</button>
    // </div>

</script>

<style>
.fade-out {
    opacity: 0;
    transform: translateY(6px);
    animation: fadeInReply 0.3s ease-out forwards;
}
@keyframes fadeInReply {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-button {
    background-color: #000;
    color: #fff;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    transition: background 0.3s;
}
.modal-button:hover {
    background-color: #1f1f1f;
}

.bookmarkIcon {
    transition: all 0.3s ease;
}
</style>


<?php include 'footer'; ?>
