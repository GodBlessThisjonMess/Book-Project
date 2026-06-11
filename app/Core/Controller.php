<?php

namespace App\Core;

class Controller {
    /**
     * Merender template View dan meneruskan data variabel ke dalamnya
     *
     * @param string $view Nama berkas view (misal: 'books/index' atau 'dashboard/index')
     * @param array $data Data asosiatif yang akan diekstrak menjadi variabel di dalam View
     */
    protected function render($view, $data = []) {
        // Mengubah kunci array menjadi variabel lokal di scope ini
        extract($data);

        // Alamat path berkas view utama
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            // Cek apakah request adalah AJAX
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
            
            // Jika bukan AJAX dan bukan komponen pecahan, sisipkan header layout utama
            if (!$isAjax && strpos($view, 'components/') === false) {
                require_once __DIR__ . '/../Views/layouts/header.php';
            }
            
            // Render isi utama halaman
            require $viewFile;
            
            // Sisipkan footer layout utama
            if (!$isAjax && strpos($view, 'components/') === false) {
                require_once __DIR__ . '/../Views/layouts/footer.php';
            }
        } else {
            die("Galat Presentasi: File View '$view' tidak ditemukan di lokasi: " . htmlspecialchars($viewFile));
        }
    }

    /**
     * Melakukan pemindahan rute halaman (HTTP Redirect)
     *
     * @param string $path URL tujuan pemindahan (contoh: '/books')
     */
    protected function redirect($path) {
        // Jika rute tidak diawali dengan http:// atau https://, tambahkan BASE_URL
        if (strpos($path, 'http://') !== 0 && strpos($path, 'https://') !== 0) {
            $path = BASE_URL . '/' . ltrim($path, '/');
        }
        header("Location: " . $path);
        exit;
    }
}
