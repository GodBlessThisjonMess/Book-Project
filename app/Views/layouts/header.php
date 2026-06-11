<?php
use App\Config\Database;
try {
    $db = Database::getConnection();
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $db->prepare("SELECT id, title FROM books WHERE is_deleted = 0 AND user_id = :user_id ORDER BY id DESC LIMIT 6");
    $stmt->execute(['user_id' => $userId]);
    $sidebarBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    $sidebarBooks = [];
}

// Ambil info user yang sedang login
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    try {
        $userStmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $userStmt->execute(['id' => $_SESSION['user_id']]);
        $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}
$avatar = ($currentUser && !empty($currentUser['avatar_url'])) ? BASE_URL . '/' . $currentUser['avatar_url'] : null;
$username = $currentUser ? htmlspecialchars($currentUser['username']) : 'Demo User';
$bio = ($currentUser && !empty($currentUser['bio'])) ? htmlspecialchars($currentUser['bio']) : 'Single Account';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Book Reading Tracker</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        // Extract path component from BASE_URL for API calls
        // BASE_URL format: http://localhost or http://localhost/subfolder
        const urlObj = new URL(window.BASE_URL, 'http://dummy');
        window.BASE_PATH = urlObj.pathname === '/' ? '' : urlObj.pathname;
    </script>
</head>
<body>
    <!-- Notion-style Search Overlay -->
    <div class="search-overlay" id="searchOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(8px); z-index: 10000; justify-content: center; padding-top: 10vh;">
        <div class="search-modal" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 90%; max-width: 550px; height: fit-content; max-height: 450px; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0, 121, 121, 0.15); overflow: hidden; animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
            <!-- Search Input Bar -->
            <div style="display: flex; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--border-color); gap: 12px;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 18px; color: var(--primary);"></i>
                <input type="text" id="searchInput" placeholder="Cari judul buku atau penulis... (Tekan Esc untuk menutup)" style="flex: 1; border: none; outline: none; background: transparent; font-size: 15px; font-family: inherit; color: var(--text-primary);">
            </div>
            <!-- Container Hasil Pencarian -->
            <div id="searchResults" style="flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 4px;">
                <div style="color: var(--text-secondary); text-align: center; font-size: 13px; padding: 30px 0;">
                    Ketik sesuatu untuk mulai mencari...
                </div>
            </div>
        </div>
    </div>

    <div class="app-container">
        <!-- Sidebar Utama -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-book-bookmark brand-icon"></i>
                <span class="brand-name">Book Journey</span>
            </div>
            <nav class="sidebar-menu" style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                <a href="<?= BASE_URL ?>/" class="menu-item">
                    <i class="fa-solid fa-house-chimney menu-icon"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Koleksi Buku dengan Dropdown Toggle -->
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <a href="<?= BASE_URL ?>/books" class="menu-item" style="flex: 1; border-top-right-radius: 0; border-bottom-right-radius: 0; margin-right: 0;">
                            <i class="fa-solid fa-book-open menu-icon"></i>
                            <span>Koleksi Buku</span>
                        </a>
                        <button id="btnToggleSidebarBooks" style="background: transparent; border: none; outline: none; padding: 12px 16px; color: var(--text-secondary); cursor: pointer; border-top-right-radius: var(--radius); border-bottom-right-radius: var(--radius); transition: var(--transition); display: flex; align-items: center; justify-content: center; height: 46px;">
                            <i class="fa-solid fa-chevron-down" id="sidebarChevron" style="font-size: 11px; transition: transform 0.2s;"></i>
                        </button>
                    </div>
                    <div id="sidebarBooksDropdown" style="display: none; flex-direction: column; padding: 6px 12px 10px 42px; gap: 8px; border-left: 2px solid var(--border-color); margin-left: 24px; animation: slideDown 0.2s ease;">
                        <?php foreach ($sidebarBooks as $sbBook): ?>
                            <a href="<?= BASE_URL ?>/books/<?= $sbBook['id'] ?>" style="font-size: 13px; color: var(--text-secondary); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-secondary)'">
                                <?= htmlspecialchars($sbBook['title']) ?>
                            </a>
                        <?php endforeach; ?>
                        <?php if (empty($sidebarBooks)): ?>
                            <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Belum ada buku</span>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>/calendar" class="menu-item">
                    <i class="fa-solid fa-calendar-alt menu-icon"></i>
                    <span>Kalender Membaca</span>
                </a>
                <!-- Opsi Baru Search & Trash -->
                <a href="#" id="findBookBtn" class="menu-item">
                    <i class="fa-solid fa-magnifying-glass menu-icon"></i>
                    <span>Find Book <kbd style="font-size: 10px; background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1); padding: 1px 5px; border-radius: 4px; margin-left: auto; font-family: inherit; font-weight: bold; color: var(--primary);">Ctrl+K</kbd></span>
                </a>
                <a href="<?= BASE_URL ?>/trash" class="menu-item">
                    <i class="fa-solid fa-trash-can menu-icon"></i>
                    <span>Trash</span>
                </a>
                
                <!-- Logout -->
                <a href="<?= BASE_URL ?>/logout" class="menu-item" style="color: var(--danger); margin-top: auto;">
                    <i class="fa-solid fa-right-from-bracket menu-icon" style="color: var(--danger);"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Area Konten Utama -->
        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">
                    <h1>Personal Workspace</h1>
                </div>
                <a href="<?= BASE_URL ?>/settings" class="user-profile" style="text-decoration: none; color: inherit; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                    <div class="user-avatar" style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #e2e8f0;">
                        <?php if ($avatar): ?>
                            <img src="<?= $avatar ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fa-solid fa-circle-user" style="font-size: 38px; color: var(--primary); display: flex;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= $username ?></span>
                        <span class="user-role" style="font-size: 11px; color: var(--text-secondary); display: block; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $bio ?></span>
                    </div>
                </a>
            </header>
            <div class="content-body">
