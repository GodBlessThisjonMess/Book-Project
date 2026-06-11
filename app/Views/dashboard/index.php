    <div class="dashboard-header" style="margin-bottom: 30px;">
    <h2>Selamat Datang di Ruang Membaca Anda</h2>
    <p style="color: var(--text-secondary); margin-top: 5px;">Melacak progres, menjadwalkan pembacaan, dan menulis jurnal buku kesayangan Anda.</p>
</div>

<!-- Grid Statistik Dashboard (Kebutuhan Fitur 6) -->
<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon not-read">
            <i class="fa-solid fa-book"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Belum Dibaca</span>
            <span class="stat-value"><?= isset($stats['Not Read']) ? $stats['Not Read'] : 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon ongoing">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Sedang Dibaca</span>
            <span class="stat-value"><?= isset($stats['On Going']) ? $stats['On Going'] : 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon done">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Selesai Dibaca</span>
            <span class="stat-value"><?= isset($stats['Done']) ? $stats['Done'] : 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon unfinished">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <div class="stat-info">
            <span class="stat-label">Tidak Selesai</span>
            <span class="stat-value"><?= isset($stats['Unfinished']) ? $stats['Unfinished'] : 0 ?></span>
        </div>
    </div>
</div>

<!-- Panel Aksi Cepat (Call to Action) -->
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 30px; backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
    <div>
        <h3>Mulai Petualangan Membaca Baru</h3>
        <p style="color: var(--text-secondary); margin-top: 8px; font-size: 14px; max-width: 500px;">
            Tambahkan buku baru ke dalam koleksi personal Anda, buat jadwal membaca di kalender harian, dan catat setiap ulasan atau jurnal dari bab yang telah Anda lalui.
        </p>
    </div>
    <a href="<?= BASE_URL ?>/books/create" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Buku Baru
    </a>
</div>

<!-- CTA Bar Melanjutkan Baca Buku Aktif Terakhir -->
<?php if (!empty($latestBook)): ?>
<div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 20px 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; box-shadow: var(--shadow);">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(0, 121, 121, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 18px;">
            <i class="fa-solid fa-bookmark"></i>
        </div>
        <div>
            <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--primary); letter-spacing: 0.5px;">Buku Aktif Terakhir</span>
            <h4 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin-top: 2px;">
                Ingin melanjutkan membaca buku <strong style="color: var(--primary);">"<?= htmlspecialchars($latestBook->title) ?>"</strong>?
            </h4>
        </div>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="<?= BASE_URL ?>/books/<?= $latestBook->id ?>" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-book-open"></i> Ya, Lanjutkan
        </a>
        <form action="<?= BASE_URL ?>/confirm-reading" method="POST" style="display: inline;">
            <input type="hidden" name="book_id" value="<?= $latestBook->id ?>">
            <input type="hidden" name="status" value="Unfinished">
            <button type="submit" class="btn" style="padding: 8px 16px; font-size: 13px; border-radius: 8px; background: #fff; border: 1px solid var(--border-color); color: var(--danger); font-weight: 600; cursor: pointer; transition: var(--transition); font-family: inherit;">
                <i class="fa-solid fa-circle-xmark"></i> Tidak / Tunda
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Bagian Ulasan & Jurnal Pilihan (Preview Tanpa Aksi) -->
<div style="margin-top: 40px; margin-bottom: 40px;">
    <h3 style="font-size: 18px; font-weight: 600; color: var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-star" style="color: var(--warning);"></i> Catatan & Ulasan Pilihan
    </h3>
    
    <?php if (empty($reviewedBooks)): ?>
        <div style="background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: var(--radius); padding: 30px; text-align: center; color: var(--text-secondary); font-size: 14px;">
            <i class="fa-regular fa-folder-open" style="font-size: 32px; color: var(--primary); opacity: 0.5; margin-bottom: 10px; display: block;"></i>
            Belum ada buku dengan ulasan bintang dan jurnal catatan untuk ditampilkan.
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            <?php foreach ($reviewedBooks as $book): ?>
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); position: relative; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <!-- Cover & Deskripsi Header -->
                        <div style="display: flex; gap: 15px; align-items: flex-start; margin-bottom: 15px;">
                            <?php 
                            $gradients = [
                                'linear-gradient(135deg, #007979, #004d4d)',
                                'linear-gradient(135deg, #2b4c7e, #1a3050)',
                                'linear-gradient(135deg, #7c3aed, #5b21b6)',
                                'linear-gradient(135deg, #b45309, #78350f)',
                                'linear-gradient(135deg, #0f766e, #115e59)',
                                'linear-gradient(135deg, #9f1239, #4c0519)',
                                'linear-gradient(135deg, #1e293b, #0f172a)',
                                'linear-gradient(135deg, #d97706, #92400e)'
                            ];
                            $gradIndex = $book['id'] ? ($book['id'] % count($gradients)) : 0;
                            $bgCover = $book['cover_url'] ? 'url(' . htmlspecialchars($book['cover_url']) . ') center/cover no-repeat' : $gradients[$gradIndex];
                            ?>
                            <div class="book-cover-wrapper" style="background: <?= $bgCover ?>; width: 60px; height: 90px; flex-shrink: 0; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.15); position: relative; margin-bottom: 0;">
                                <div class="book-spine" style="width: 3px;"></div>
                                <?php if (!$book['cover_url']): ?>
                                    <div style="padding: 6px; color: #fff; display: flex; flex-direction: column; justify-content: center; height: 100%; text-align: center;">
                                        <div style="font-size: 8px; font-weight: 700; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.1;"><?= htmlspecialchars($book['title']) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="flex: 1;">
                                <h4 style="font-size: 15px; font-weight: 600; color: var(--text-primary); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.3; margin: 0;"><?= htmlspecialchars($book['title']) ?></h4>
                                <span style="font-size: 12px; color: var(--text-secondary); display: block; margin-top: 2px;">Oleh: <?= htmlspecialchars($book['author']) ?></span>
                                
                                <!-- Rating Bintang -->
                                <div style="display: flex; gap: 2px; color: var(--warning); margin-top: 8px; font-size: 13px;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?= $i <= $book['rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ulasan Buku Singkat -->
                        <?php if (!empty($book['review_text'])): ?>
                            <div style="background: var(--bg-secondary); border-left: 3px solid var(--primary); padding: 10px 12px; border-radius: 4px; margin-bottom: 12px; font-size: 13px; color: var(--text-primary); font-style: italic; line-height: 1.4;">
                                "<?= htmlspecialchars(mb_strimwidth($book['review_text'], 0, 120, '...')) ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Catatan Jurnal Terakhir -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 12px; margin-top: 5px;">
                        <span style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">Catatan Jurnal Terbaru</span>
                        <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.4; margin: 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($book['latest_note']) ?>
                        </p>
                        <span style="font-size: 11px; color: var(--text-secondary); opacity: 0.7; display: block; margin-top: 6px;">
                            <i class="fa-regular fa-clock" style="margin-right: 3px;"></i> 
                            <?= date('d M Y H:i', strtotime($book['latest_note_date'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>


<!-- Modal Dialog Prompt untuk Melanjutkan Membaca (Saat Membuka Website / Sekali Sesi) -->
<?php if (!empty($scheduledBook)): ?>
    <div class="modal-overlay" id="confirmModal" style="display: none; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(8px); z-index: 9999;">
        <div class="modal" style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 30px; border-radius: var(--radius); max-width: 450px; width: 90%; text-align: center; box-shadow: var(--shadow); position: relative; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
            <!-- Tombol Tutup Silang di Sudut Kanan Atas -->
            <button type="button" id="closeConfirmModal" style="position: absolute; right: 15px; top: 15px; background: transparent; border: none; font-size: 16px; color: var(--text-secondary); cursor: pointer; outline: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="modal-icon" style="font-size: 44px; color: var(--primary); margin-bottom: 15px;">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Melanjutkan Membaca</h3>
            <p style="color: var(--text-secondary); line-height: 1.6; margin-top: 10px; font-size: 14px;">
                Selamat datang kembali! Ingin melanjutkan membaca buku aktif terakhir Anda?<br>
                <strong style="color: var(--primary); font-size: 16px; display: block; margin-top: 8px;">"<?= htmlspecialchars($scheduledBook->title) ?>"</strong>
            </p>
            <div class="modal-actions" style="margin-top: 25px; display: flex; gap: 12px; justify-content: center;">
                <a href="<?= BASE_URL ?>/books/<?= $scheduledBook->id ?>" class="btn btn-yes" id="btnYesConfirm" style="background: var(--primary); color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-check"></i> Ya, Lanjutkan
                </a>
                <form action="<?= BASE_URL ?>/confirm-reading" method="POST" style="display: inline-block; margin: 0;">
                    <input type="hidden" name="book_id" value="<?= $scheduledBook->id ?>">
                    <input type="hidden" name="status" value="Unfinished">
                    <button type="submit" class="btn btn-no" style="background: #fff; border: 1px solid var(--border-color); color: var(--danger); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;">
                        Tidak / Tunda
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('confirmModal');
            const closeBtn = document.getElementById('closeConfirmModal');
            const btnYes = document.getElementById('btnYesConfirm');
            
            // Tampilkan modal hanya jika belum di-dismiss pada sesi browser ini
            if (modal && !sessionStorage.getItem('dismissed_continue_reading_modal')) {
                modal.style.display = 'flex';
            }

            if (closeBtn && modal) {
                closeBtn.addEventListener('click', () => {
                    modal.style.display = 'none';
                    sessionStorage.setItem('dismissed_continue_reading_modal', 'true');
                });
            }

            if (btnYes && modal) {
                btnYes.addEventListener('click', () => {
                    sessionStorage.setItem('dismissed_continue_reading_modal', 'true');
                });
            }
        });
    </script>
<?php endif; ?>
