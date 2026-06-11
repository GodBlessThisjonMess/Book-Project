<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2>Tong Sampah (Trash)</h2>
        <p style="color: var(--text-secondary); margin-top: 5px;">Daftar buku yang dihapus sementara. Anda dapat memulihkan atau menghapusnya secara permanen.</p>
    </div>
</div>

<!-- Notifikasi Flash Message -->
<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--success); color: var(--success); padding: 12px 20px; border-radius: var(--radius); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></span>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: var(--danger); padding: 12px 20px; border-radius: var(--radius); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-xmark"></i>
        <span><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></span>
    </div>
<?php endif; ?>

<!-- Tampilan Kosong Jika Tong Sampah Kosong -->
<?php if (empty($books)): ?>
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 60px; text-align: center; backdrop-filter: blur(8px); box-shadow: var(--shadow);">
        <i class="fa-solid fa-trash-arrow-up" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 20px; opacity: 0.5;"></i>
        <h3>Tong Sampah Kosong</h3>
        <p style="color: var(--text-secondary); margin-top: 8px; font-size: 14px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.6;">
            Tidak ada buku di dalam tong sampah saat ini.
        </p>
    </div>
<?php else: ?>
    <!-- Grid Koleksi Buku di Tong Sampah -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
        <?php foreach ($books as $book): ?>
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 24px; display: flex; flex-direction: column; height: 100%; transition: var(--transition); box-shadow: var(--shadow);">
                <!-- Visualisasi 3D Book Cover Buram (Menandakan terhapus) -->
                <div class="book-cover-wrapper" style="background: <?= $book->coverUrl ? 'url(' . htmlspecialchars($book->coverUrl) . ') center/cover no-repeat' : $book->getCoverGradient() ?>; opacity: 0.65;">
                    <div class="book-spine"></div>
                    <?php if (!$book->coverUrl): ?>
                        <div class="book-cover-content">
                            <h4 class="book-cover-title"><?= htmlspecialchars($book->title); ?></h4>
                            <span class="book-cover-author"><?= htmlspecialchars($book->author); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <h3 style="font-size: 18px; font-weight: 600; line-height: 1.4; margin-bottom: 6px; color: var(--text-primary);"><?= htmlspecialchars($book->title); ?></h3>
                <span style="color: var(--text-secondary); font-size: 13px; font-weight: 500; display: block; margin-bottom: 20px;">Oleh: <?= htmlspecialchars($book->author); ?></span>
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 16px; display: flex; gap: 10px; margin-top: auto;">
                    <!-- Form Pulihkan Buku -->
                    <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/restore" method="POST" style="flex: 1;">
                        <button type="submit" class="btn" style="width: 100%; background: rgba(0, 121, 121, 0.1); color: var(--primary); padding: 8px 12px; font-size: 13px; border-radius: 8px; font-weight: 600;">
                            <i class="fa-solid fa-trash-can-arrow-up"></i> Pulihkan
                        </button>
                    </form>
                    <!-- Form Hapus Permanen -->
                    <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/force-delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini secara PERMANEN? Semua data review dan jurnal terkait juga akan terhapus.');" style="flex: 1;">
                        <button type="submit" class="btn" style="width: 100%; background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 8px 12px; font-size: 13px; border-radius: 8px; font-weight: 600;">
                            <i class="fa-solid fa-circle-xmark"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
