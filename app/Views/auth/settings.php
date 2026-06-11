<?php
/** @var \App\Models\User $user */
?>
<div style="margin-bottom: 30px;">
    <h2>Pengaturan Akun</h2>
    <p style="color: var(--text-secondary); margin-top: 5px;">Kelola informasi profil personal Anda di Book Journal.</p>
</div>

<!-- Flash Message Notifications -->
<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--success); color: var(--success); padding: 12px 20px; border-radius: var(--radius); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid var(--danger); color: var(--danger); padding: 12px 20px; border-radius: var(--radius); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-xmark"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 35px; max-width: 650px; box-shadow: var(--shadow);">
    <form action="<?= BASE_URL ?>/settings" method="POST" enctype="multipart/form-data">
        
        <!-- Foto Profil / Avatar -->
        <div style="margin-bottom: 30px; display: flex; align-items: center; gap: 25px; flex-wrap: wrap;">
            <div style="position: relative; width: 100px; height: 100px; border-radius: 50%; overflow: hidden; background: #f1f5f9; border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <?php if ($user->avatarUrl): ?>
                    <img id="avatar-preview" src="<?= BASE_URL . '/' . $user->avatarUrl ?>" alt="Pratinjau Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <img id="avatar-preview" src="" alt="Pratinjau Avatar" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <i id="avatar-icon-placeholder" class="fa-solid fa-circle-user" style="font-size: 100px; color: var(--primary);"></i>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Foto Profil</label>
                <div style="position: relative; display: inline-block;">
                    <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(event)" style="position: absolute; left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 10;">
                    <button type="button" class="btn" style="background: #ffffff; color: var(--text-primary); border: 1px solid var(--border-color); padding: 8px 16px; font-size: 13px; pointer-events: none;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Pilih Foto Baru
                    </button>
                </div>
                <span style="display: block; font-size: 11px; color: var(--text-secondary); margin-top: 6px;">Mendukung format JPG, JPEG, PNG, GIF, WEBP. Maks 2MB.</span>
            </div>
        </div>

        <!-- Input Username -->
        <div style="margin-bottom: 20px;">
            <label for="username" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Username <span style="color: var(--danger);">*</span></label>
            <input type="text" id="username" name="username" required value="<?= htmlspecialchars($user->username) ?>" style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
        </div>

        <!-- Input Deskripsi User (Bio) -->
        <div style="margin-bottom: 20px;">
            <label for="bio" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Deskripsi User / Bio</label>
            <input type="text" id="bio" name="bio" value="<?= htmlspecialchars($user->bio) ?>" placeholder="Contoh: Pembaca setenang angin senja..." style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
        </div>

        <!-- Optional: Ganti Password -->
        <div style="margin-bottom: 30px; border-top: 1px dashed var(--border-color); padding-top: 20px;">
            <h4 style="font-size: 13px; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">Ubah Kata Sandi (Opsional)</h4>
            <label for="password" style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">Kata Sandi Baru</label>
            <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah..." style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: inherit; font-size: 14px; outline: none; transition: var(--transition);">
        </div>

        <!-- Tombol Aksi -->
        <div style="display: flex; gap: 15px; border-top: 1px solid var(--border-color); padding-top: 25px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
            <a href="<?= BASE_URL ?>/" class="btn" style="background: #ffffff; color: var(--text-primary); padding: 12px 28px; border: 1px solid var(--border-color);">
                Kembali ke Dashboard
            </a>
        </div>
    </form>
</div>

<script>
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const placeholder = document.getElementById('avatar-icon-placeholder');
                
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
