<?php
/** @var \App\Models\Book|null $book */
/** @var \App\Models\Review|null $review */
/** @var array<\App\Models\Journal> $journals */
?>
<div style="margin-bottom: 25px;">
    <a href="<?= BASE_URL ?>/books" style="color: var(--primary); text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Koleksi
    </a>
</div>

<!-- Flash Message Notifications -->
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

<?php if (!$book): ?>
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 40px; text-align: center; box-shadow: var(--shadow);">
        <h3>Buku Tidak Ditemukan</h3>
        <p style="color: var(--text-secondary); margin-top: 8px;">Maaf, data buku dengan ID tersebut tidak terdaftar.</p>
    </div>
<?php else: ?>
    <!-- Kontainer Detail Buku -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 35px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
            
            <!-- Cover Buku 3D Otomatis / Cover Gambar Asli -->
            <div class="book-cover-wrapper" style="background: <?= $book->coverUrl ? 'url(' . htmlspecialchars($book->coverUrl) . ') center/cover no-repeat' : $book->getCoverGradient() ?>; width: 130px; height: 190px; flex-shrink: 0; margin-bottom: 0;">
                <div class="book-spine"></div>
                <?php if (!$book->coverUrl): ?>
                    <div class="book-cover-content">
                        <h4 class="book-cover-title" style="font-size: 13px;"><?= htmlspecialchars($book->title); ?></h4>
                        <span class="book-cover-author" style="font-size: 9px;"><?= htmlspecialchars($book->author); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div style="flex: 1; min-width: 250px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div>
                    <!-- Label Status Buku -->
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; padding: 4px 8px; border-radius: 6px; margin-bottom: 12px; display: inline-block;
                        <?php 
                        if ($book->status === 'On Going') echo 'background: rgba(6, 182, 212, 0.15); color: #06b6d4;';
                        elseif ($book->status === 'Done') echo 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
                        elseif ($book->status === 'Unfinished') echo 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
                        else echo 'background: rgba(156, 163, 175, 0.15); color: #9ca3af;';
                        ?>">
                        <?= $book->getStatusLabel(); ?>
                    </span>
                    <h2 style="font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1.3;"><?= htmlspecialchars($book->title); ?></h2>
                    <span style="color: var(--text-secondary); font-size: 15px; font-weight: 500; display: block; margin-top: 5px;">Karya: <?= htmlspecialchars($book->author); ?></span>
                    
                    <!-- Progress Bar Membaca (Halaman Terbaca) -->
                    <div style="margin-top: 20px; max-width: 350px;">
                        <?php 
                        $progress = $book->getProgressPercent(); 
                        $pagesText = $book->totalPages > 0 ? "Halaman " . $book->lastPageRead . " dari " . $book->totalPages : "Progres belum ditentukan";
                        ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 12px; font-weight: 600;">
                            <span style="color: var(--text-secondary);"><?= $pagesText ?></span>
                            <span style="color: var(--primary);"><?= $progress ?>%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: rgba(0, 121, 121, 0.08); border-radius: 10px; overflow: hidden;">
                            <div style="width: <?= $progress ?>%; height: 100%; background: var(--primary); border-radius: 10px; transition: width 0.5s ease;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Aksi Ubah Status Cepat (Fitur 1 - Pengguna dapat mengubah status buku secara manual) -->
                <div style="display: flex; flex-direction: column; gap: 10px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; padding: 15px;">
                    <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/status" method="POST" style="display: flex; align-items: center; gap: 10px;">
                        <label for="status" style="font-size: 13px; font-weight: 600;">Ubah Status:</label>
                        <select id="status" name="status" style="padding: 6px 12px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; font-size: 13px; color: var(--text-primary); font-family: inherit;">
                            <option value="Not Read" <?= $book->status === 'Not Read' ? 'selected' : '' ?>>Belum Dibaca</option>
                            <option value="On Going" <?= $book->status === 'On Going' ? 'selected' : '' ?>>Sedang Dibaca</option>
                            <option value="Done" <?= $book->status === 'Done' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Unfinished" <?= $book->status === 'Unfinished' ? 'selected' : '' ?>>Tidak Selesai</option>
                        </select>
                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 6px;">Simpan</button>
                    </form>
                    
                    <button type="button" id="btnEditBookDetails" class="btn" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-secondary); padding: 6px 12px; font-size: 12px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; font-weight: 600; font-family: inherit; width: 100%;">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Informasi Buku
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Edit Detail Buku (Collapsible) -->
        <div id="editBookDetailsForm" style="display: none; margin-top: 25px; border-top: 1px dashed var(--border-color); padding-top: 20px;">
            <h4 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Edit Informasi Buku</h4>
            <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/update" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label for="edit_title" style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Judul Buku *</label>
                        <input type="text" id="edit_title" name="title" required value="<?= htmlspecialchars($book->title) ?>" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; color: var(--text-primary); font-family: inherit; font-size: 13px; outline: none;">
                    </div>
                    <div>
                        <label for="edit_author" style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Penulis *</label>
                        <input type="text" id="edit_author" name="author" required value="<?= htmlspecialchars($book->author) ?>" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; color: var(--text-primary); font-family: inherit; font-size: 13px; outline: none;">
                    </div>
                    <div>
                        <label for="edit_total_pages" style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Jumlah Halaman *</label>
                        <input type="number" id="edit_total_pages" name="total_pages" required min="1" value="<?= htmlspecialchars($book->totalPages) ?>" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; color: var(--text-primary); font-family: inherit; font-size: 13px; outline: none;">
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="edit_cover_url" style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Cover URL (Opsional)</label>
                    <input type="url" id="edit_cover_url" name="cover_url" value="<?= htmlspecialchars($book->coverUrl ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; color: var(--text-primary); font-family: inherit; font-size: 13px; outline: none;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="edit_description" style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Sinopsis / Deskripsi</label>
                    <textarea id="edit_description" name="description" rows="5" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; color: var(--text-primary); font-family: inherit; font-size: 13px; resize: vertical; outline: none;"><?= htmlspecialchars($book->description ?? '') ?></textarea>
                </div>
                
                <!-- Keep same status -->
                <input type="hidden" name="status" value="<?= htmlspecialchars($book->status) ?>">
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; border-radius: 6px;"><i class="fa-solid fa-check"></i> Simpan Perubahan</button>
                    <button type="button" id="btnCancelEditDetails" class="btn" style="padding: 8px 20px; font-size: 13px; border-radius: 6px; background: #fff; border: 1px solid var(--border-color); color: var(--text-primary);">Batal</button>
                </div>
            </form>
        </div>

        <div style="margin-top: 25px; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <h4 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary); margin-bottom: 10px;">Sinopsis / Deskripsi Buku</h4>
            <p style="color: var(--text-secondary); line-height: 1.7; font-size: 14px;">
                <?= $book->description ? nl2br(htmlspecialchars($book->description)) : '<em>Tidak ada deskripsi untuk buku ini.</em>' ?>
            </p>
        </div>
    </div>

    <!-- Layout Dua Kolom: Ulasan (Kiri) & Jurnal (Kanan) -->
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 30px; align-items: start; flex-wrap: wrap;">
        
        <!-- KOLOM 1: REVIEW & RATING (Fitur 4 - Ulasan & Rating Buku) -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow);">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: var(--primary);">
                <i class="fa-solid fa-star"></i> Ulasan Pribadi (Privat)
            </h3>

            <!-- Tampilan Ulasan yang Sudah Ada -->
            <?php if ($review): ?>
                <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; gap: 4px; color: var(--warning); margin-bottom: 10px; font-size: 16px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $review->rating ? 'fa-solid fa-star' : 'fa-regular fa-star' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p style="color: var(--text-primary); font-size: 14px; line-height: 1.6; font-style: italic;">
                        "<?= nl2br(htmlspecialchars($review->reviewText)) ?>"
                    </p>
                    <span style="display: block; font-size: 11px; color: var(--text-secondary); margin-top: 10px; text-align: right;">
                        Ditinjau pada: <?= date('d M Y', strtotime($review->createdAt)) ?>
                    </span>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 20px 0; color: var(--text-secondary); font-size: 13px; margin-bottom: 20px; border: 1px dashed var(--border-color); border-radius: 8px;">
                    <i class="fa-regular fa-face-smile" style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                    Belum ada ulasan. Bagikan pendapat Anda setelah membaca buku ini!
                </div>
            <?php endif; ?>

            <!-- Form Simpan / Edit Review -->
            <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/review" method="POST" style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);"><?= $review ? 'Perbarui Ulasan Anda' : 'Tulis Ulasan Baru' ?></h4>
                
                <!-- Rating Selector -->
                <div style="margin-bottom: 15px;">
                    <label for="rating" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Rating Bintang:</label>
                    <select id="rating" name="rating" required style="padding: 8px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; width: 120px; font-family: inherit; font-size: 13px; color: var(--text-primary);">
                        <option value="5" <?= $review && $review->rating == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" <?= $review && $review->rating == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ (4)</option>
                        <option value="3" <?= $review && $review->rating == 3 ? 'selected' : '' ?>>⭐⭐⭐ (3)</option>
                        <option value="2" <?= $review && $review->rating == 2 ? 'selected' : '' ?>>⭐⭐ (2)</option>
                        <option value="1" <?= $review && $review->rating == 1 ? 'selected' : '' ?>>⭐ (1)</option>
                    </select>
                </div>

                <!-- Ulasan Teks -->
                <div style="margin-bottom: 20px;">
                    <label for="review_text" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Catatan Ulasan:</label>
                    <textarea id="review_text" name="review_text" rows="4" placeholder="Tulis ulasan Anda mengenai karakter, isi cerita, kesan setelah membaca..." required style="width: 100%; padding: 10px; background: #fff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 13px; resize: vertical;"><?= $review ? htmlspecialchars($review->reviewText) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-pen-to-square"></i> Simpan Ulasan</button>
            </form>
        </div>

        <!-- KOLOM 2: JURNAL MEMBACA (Fitur 5 - Jurnal Membaca Pribadi) -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow);">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: var(--primary);">
                <i class="fa-solid fa-clock-rotate-left"></i> Jurnal Aktivitas & Catatan
            </h3>

            <!-- Form Tulis Jurnal Baru -->
            <form action="<?= BASE_URL ?>/books/<?= $book->id ?>/journal" method="POST" style="margin-bottom: 30px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px;">
                <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 10px; text-transform: uppercase; color: var(--primary);">Tulis Jurnal Baru</h4>
                <div style="margin-bottom: 12px;">
                    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 12px; margin-bottom: 12px;">
                        <div>
                            <label for="logged_at" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Tanggal Baca / Aktivitas:</label>
                            <input type="date" id="logged_at" name="logged_at" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit; font-size: 13px; color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="read_to_page" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Sampai Halaman:</label>
                            <input type="number" id="read_to_page" name="read_to_page" required min="1" <?= $book->totalPages > 0 ? 'max="' . $book->totalPages . '"' : '' ?> placeholder="<?= $book->totalPages > 0 ? 'Maks: ' . $book->totalPages : 'Contoh: 10' ?>" style="width: 100%; padding: 8px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; font-family: inherit; font-size: 13px; color: var(--text-primary);">
                        </div>
                    </div>
                    
                    <label for="notes" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary);">Catatan Pemikiran:</label>
                    <textarea id="notes" name="notes" rows="3" required placeholder="Catat pemikiran Anda, progres halaman, kutipan menarik, atau kesan membaca saat ini..." style="width: 100%; padding: 10px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-family: inherit; font-size: 13px; resize: vertical;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 12px; border-radius: 6px;"><i class="fa-solid fa-paper-plane"></i> Simpan ke Jurnal</button>
            </form>

            <!-- Lini Masa Catatan Jurnal -->
            <div style="display: flex; flex-direction: column; gap: 15px; border-left: 2px solid var(--border-color); padding-left: 20px; margin-left: 10px;">
                <?php if (empty($journals)): ?>
                    <div style="color: var(--text-secondary); font-size: 13px; padding: 15px 0; font-style: italic; margin-left: -20px; text-align: center; border: 1px dashed var(--border-color); border-radius: 8px; background: var(--bg-secondary);">
                        Belum ada catatan jurnal. Mulailah menulis untuk merekam setiap momen perjalanan membaca Anda!
                    </div>
                <?php else: ?>
                    <?php foreach ($journals as $journal): ?>
                        <div style="position: relative; background: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                            <!-- Titik Lini Masa -->
                            <div style="position: absolute; width: 12px; height: 12px; background: var(--primary); border: 2px solid var(--bg-card); border-radius: 50%; left: -27px; top: 18px;"></div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; border-bottom: 1px solid rgba(0,0,0,0.04); padding-bottom: 6px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 11px; color: var(--text-secondary); font-weight: 600;">
                                        <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($journal->loggedAt)) ?>
                                    </span>
                                    <?php if ($journal->readToPage > 0): ?>
                                        <span style="font-size: 10px; background: rgba(0, 121, 121, 0.08); color: var(--primary); font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                                            Hal. <?= $journal->readToPage ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <!-- Aksi Edit & Hapus Jurnal -->
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <button type="button" class="btn-edit-journal" data-id="<?= $journal->id ?>" style="background: none; border: none; color: var(--primary); cursor: pointer; font-size: 12px; transition: var(--transition);" title="Edit catatan">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <form action="<?= BASE_URL ?>/journal/<?= $journal->id ?>/delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan jurnal ini?');" style="display: inline;">
                                        <!-- Kirim ID buku untuk redirect kembali -->
                                        <input type="hidden" name="book_id" value="<?= $book->id ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 12px; transition: var(--transition);" title="Hapus catatan">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div id="journal-display-<?= $journal->id ?>">
                                <p style="color: var(--text-primary); font-size: 13px; line-height: 1.6; white-space: pre-wrap; margin: 0;"><?= htmlspecialchars($journal->notes) ?></p>
                            </div>
                            <div id="journal-edit-<?= $journal->id ?>" style="display: none; margin-top: 10px;">
                                <form action="<?= BASE_URL ?>/journal/<?= $journal->id ?>/update" method="POST">
                                    <input type="hidden" name="book_id" value="<?= $book->id ?>">
                                    
                                    <div style="margin-bottom: 8px;">
                                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">Sampai Halaman:</label>
                                        <input type="number" name="read_to_page" required min="1" <?= $book->totalPages > 0 ? 'max="' . $book->totalPages . '"' : '' ?> value="<?= $journal->readToPage ?>" style="width: 120px; padding: 6px; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-family: inherit; font-size: 12px; outline: none; background: #fff;">
                                    </div>
                                    
                                    <div style="margin-bottom: 8px;">
                                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">Catatan Pemikiran:</label>
                                        <textarea name="notes" rows="3" required style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-family: inherit; font-size: 13px; resize: vertical; margin-bottom: 8px; outline: none; background: #fff;"><?= htmlspecialchars($journal->notes) ?></textarea>
                                    </div>
                                    
                                    <div style="display: flex; gap: 8px;">
                                        <button type="submit" class="btn btn-primary" style="padding: 4px 12px; font-size: 11px; border-radius: 4px;">Simpan</button>
                                        <button type="button" class="btn btn-cancel-edit-journal" data-id="<?= $journal->id ?>" style="padding: 4px 12px; font-size: 11px; border-radius: 4px; background: #fff; border: 1px solid var(--border-color); color: var(--text-primary);">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Script JavaScript Halaman Detail -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Toggle Form Edit Detail Buku
            const btnEditBookDetails = document.getElementById('btnEditBookDetails');
            const editBookDetailsForm = document.getElementById('editBookDetailsForm');
            const btnCancelEditDetails = document.getElementById('btnCancelEditDetails');

            if (btnEditBookDetails && editBookDetailsForm) {
                btnEditBookDetails.addEventListener('click', () => {
                    const isHidden = editBookDetailsForm.style.display === 'none';
                    editBookDetailsForm.style.display = isHidden ? 'block' : 'none';
                    if (isHidden) {
                        editBookDetailsForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }

            if (btnCancelEditDetails && editBookDetailsForm) {
                btnCancelEditDetails.addEventListener('click', () => {
                    editBookDetailsForm.style.display = 'none';
                });
            }

            // Toggle Form Edit Jurnal
            document.querySelectorAll('.btn-edit-journal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const displayDiv = document.getElementById(`journal-display-${id}`);
                    const editDiv = document.getElementById(`journal-edit-${id}`);
                    
                    if (displayDiv && editDiv) {
                        displayDiv.style.display = 'none';
                        editDiv.style.display = 'block';
                    }
                });
            });

            document.querySelectorAll('.btn-cancel-edit-journal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const displayDiv = document.getElementById(`journal-display-${id}`);
                    const editDiv = document.getElementById(`journal-edit-${id}`);
                    
                    if (displayDiv && editDiv) {
                        displayDiv.style.display = 'block';
                        editDiv.style.display = 'none';
                    }
                });
            });
        });
    </script>
<?php endif; ?>
