<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Koleksi Buku Anda</h2>
        <p style="color: var(--text-secondary); margin-top: 5px;">Daftar lengkap semua buku yang Anda miliki di Book Journal.</p>
    </div>
    <a href="<?= BASE_URL ?>/books/create" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Buku Baru
    </a>
</div>

<!-- Notifikasi Flash Message Sukses -->
<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--success); color: var(--success); padding: 12px 20px; border-radius: var(--radius); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></span>
    </div>
<?php endif; ?>

<!-- Tampilan Kosong Jika Koleksi Belum Ada -->
<?php if (empty($books)): ?>
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 60px; text-align: center; backdrop-filter: blur(8px); box-shadow: var(--shadow);">
        <i class="fa-solid fa-book-open" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 20px; opacity: 0.5;"></i>
        <h3>Belum Ada Koleksi Buku</h3>
        <p style="color: var(--text-secondary); margin-top: 8px; font-size: 14px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.6;">
            Anda belum menambahkan buku apa pun ke dalam koleksi Anda. Mulailah petualangan membaca dengan menekan tombol tambah di atas!
        </p>
    </div>
<?php else: ?>

    <!-- Grid Koleksi Buku -->
    <div id="booksGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
        <?php 
        $index = 0;
        foreach ($books as $book): 
        ?>
            <div class="book-card" data-index="<?= $index ?>" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 24px; display: flex; flex-direction: column; height: 100%; transition: var(--transition); box-shadow: var(--shadow);">
                
                <!-- Visualisasi 3D Book Cover Otomatis / Cover Gambar Asli -->
                <a href="<?= BASE_URL ?>/books/<?= $book->id ?>" style="text-decoration: none;">
                    <div class="book-cover-wrapper" style="background: <?= $book->coverUrl ? 'url(' . htmlspecialchars($book->coverUrl) . ') center/cover no-repeat' : $book->getCoverGradient() ?>;">
                        <div class="book-spine"></div>
                        <!-- Hanya tampilkan judul/penulis teks jika cover asli tidak dapat dimuat -->
                        <?php if (!$book->coverUrl): ?>
                            <div class="book-cover-content">
                                <h4 class="book-cover-title"><?= htmlspecialchars($book->title); ?></h4>
                                <span class="book-cover-author"><?= htmlspecialchars($book->author); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; margin-top: 8px;">
                    <!-- Lencana Pewarnaan Status Dinamis -->
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; padding: 4px 8px; border-radius: 6px; 
                        <?php 
                        if ($book->status === 'On Going') echo 'background: rgba(6, 182, 212, 0.15); color: #06b6d4;';
                        elseif ($book->status === 'Done') echo 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
                        elseif ($book->status === 'Unfinished') echo 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
                        else echo 'background: rgba(156, 163, 175, 0.15); color: #9ca3af;';
                        ?>">
                        <?= $book->getStatusLabel(); ?>
                    </span>
                </div>
                <h3 style="font-size: 18px; font-weight: 600; line-height: 1.4; margin-bottom: 6px; color: var(--text-primary);"><?= htmlspecialchars($book->title); ?></h3>
                <span style="color: var(--text-secondary); font-size: 13px; font-weight: 500; display: block; margin-bottom: 16px;">Oleh: <?= htmlspecialchars($book->author); ?></span>
                
                <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                    <?= $book->description ? htmlspecialchars(substr($book->description, 0, 100)) . '...' : 'Tidak ada sinopsis/deskripsi yang ditambahkan.' ?>
                </p>

                <!-- Progress Bar Membaca (Halaman Terbaca) -->
                <div style="margin-bottom: 20px;">
                    <?php 
                    $progress = $book->getProgressPercent(); 
                    $pagesText = $book->totalPages > 0 ? "Hal. " . $book->lastPageRead . " / " . $book->totalPages : "Progres Membaca";
                    ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 12px; font-weight: 600;">
                        <span style="color: var(--text-secondary);"><?= $pagesText ?></span>
                        <span style="color: var(--primary);"><?= $progress ?>%</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: rgba(0, 121, 121, 0.08); border-radius: 10px; overflow: hidden;">
                        <div style="width: <?= $progress ?>%; height: 100%; background: var(--primary); border-radius: 10px; transition: width 0.5s ease;"></div>
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 16px; display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                    <a href="<?= BASE_URL ?>/books/<?= $book->id ?>" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                        Detail & Jurnal <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    
                    <!-- Tombol Hapus Sementara (Soft Delete) -->
                    <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/delete" method="POST" onsubmit="return confirm('Buang buku ini ke Tong Sampah?');" style="display: inline;">
                        <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 13px; transition: var(--transition);" title="Buang ke Tong Sampah">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php 
        $index++;
        endforeach; 
        ?>
    </div>
<?php endif; ?>
