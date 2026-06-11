<div style="margin-bottom: 30px;">
    <h2>Tambah Buku ke Koleksi</h2>
    <p style="color: var(--text-secondary); margin-top: 5px;">Masukkan informasi buku untuk mulai melacak perkembangan membaca Anda.</p>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 35px; max-width: 600px; box-shadow: var(--shadow);">
    <form action="<?= BASE_URL ?>/books" method="POST">
        <!-- Input Judul Buku -->
        <div style="margin-bottom: 20px;">
            <label for="title" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Judul Buku <span style="color: var(--danger);">*</span></label>
            <input type="text" id="title" name="title" required placeholder="Contoh: Bumi Manusia" style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
        </div>

        <!-- Input Penulis Buku -->
        <div style="margin-bottom: 20px;">
            <label for="author" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Penulis Buku <span style="color: var(--danger);">*</span></label>
            <input type="text" id="author" name="author" required placeholder="Contoh: Pramoedya Ananta Toer" style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
        </div>

        <!-- Seleksi Status Awal -->
        <div style="margin-bottom: 20px;">
            <label for="status" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Status Awal <span style="color: var(--danger);">*</span></label>
            <select id="status" name="status" style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
                <option value="Not Read" selected>Belum Dibaca (Not Read)</option>
                <option value="On Going">Sedang Dibaca (On Going)</option>
                <option value="Done">Selesai (Done)</option>
                <option value="Unfinished">Tidak Selesai (Unfinished)</option>
            </select>
        </div>

        <!-- Input Jumlah Halaman -->
        <div style="margin-bottom: 20px;">
            <label for="total_pages" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Jumlah Halaman <span style="color: var(--danger);">*</span></label>
            <input type="number" id="total_pages" name="total_pages" required min="1" placeholder="Contoh: 350" style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
        </div>

        <!-- Input Cover URL (Dinamis terisi oleh Open Library API) -->
        <div style="margin-bottom: 20px; position: relative;">
            <label for="cover_url" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Cover Buku URL (Opsional)</label>
            <input type="url" id="cover_url" name="cover_url" placeholder="Tempel URL gambar cover atau kosongkan untuk auto-pencarian..." style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
            <div id="cover_suggestion_info" style="font-size: 11px; color: var(--primary); margin-top: 6px; display: none; align-items: center; gap: 6px; font-weight: 600;">
                <i class="fa-solid fa-spinner fa-spin"></i> Mencari cover asli di Open Library...
            </div>
            <!-- Kontainer Pratinjau Cover -->
            <div id="cover_preview_wrapper" style="margin-top: 15px; display: none; align-items: center; gap: 15px;">
                <div id="cover_preview_img" style="width: 60px; height: 85px; border-radius: 4px; background: rgba(0,0,0,0.05); border: 1px solid var(--border-color); background-size: cover; background-position: center;"></div>
                <span style="font-size: 12px; color: var(--text-secondary);">Pratinjau Cover Buku Terdeteksi</span>
            </div>
        </div>

        <!-- Area Sinopsis -->
        <div style="margin-bottom: 30px;">
            <label for="description" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Sinopsis / Catatan Deskripsi</label>
            <textarea id="description" name="description" rows="5" placeholder="Masukkan sinopsis singkat atau alasan Anda ingin membaca buku ini..." style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; resize: vertical; outline: none; transition: var(--transition);"></textarea>
        </div>

        <!-- Tombol Aksi -->
        <div style="display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Buku
            </button>
            <a href="<?= BASE_URL ?>/books" class="btn" style="background: #ffffff; color: var(--text-primary); padding: 12px 28px; border: 1px solid var(--border-color);">
                Batal
            </a>
        </div>
    </form>
</div>
