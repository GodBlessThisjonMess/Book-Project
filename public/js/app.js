// ====================================================================
// FRONTEND INTERACTION UTILITY (BOOKJOURNEY - BOOK JOURNAL TRACKER)
// ====================================================================

document.addEventListener('DOMContentLoaded', () => {
    // 1. Efek Fade-Out Otomatis untuk Notifikasi Flash Message
    const flashMessages = document.querySelectorAll('[style*="rgba(16, 185, 129, 0.15)"]');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => msg.remove(), 800);
        }, 4000);
    });

    // 2. Pewarnaan Menu Navigasi Aktif Dinamis Berdasarkan URL Halaman
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar-menu .menu-item');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href === currentPath || (href !== '/' && href !== '#' && currentPath.startsWith(href))) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // 3. Notion-style Search Overlay
    const searchOverlay = document.getElementById('searchOverlay');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const findBookBtn = document.getElementById('findBookBtn');

    function openSearch() {
        if (searchOverlay) {
            searchOverlay.style.display = 'flex';
            searchInput.focus();
        }
    }

    function closeSearch() {
        if (searchOverlay) {
            searchOverlay.style.display = 'none';
            searchInput.value = '';
            searchResults.innerHTML = '<div style="color: var(--text-secondary); text-align: center; font-size: 13px; padding: 30px 0;">Ketik sesuatu untuk mulai mencari...</div>';
        }
    }

    if (findBookBtn) {
        findBookBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openSearch();
        });
    }

    // Close on click outside search modal
    if (searchOverlay) {
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) {
                closeSearch();
            }
        });
    }

    // Keyboard Shortcuts: Ctrl + K and Escape
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
        if (e.key === 'Escape') {
            closeSearch();
        }
    });

    // Live search input handler (with simple debounce)
    let searchDebounceTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchDebounceTimeout);
            const query = searchInput.value.trim();
            
            if (query.length < 2) {
                searchResults.innerHTML = '<div style="color: var(--text-secondary); text-align: center; font-size: 13px; padding: 30px 0;">Ketik minimal 2 karakter untuk mencari...</div>';
                return;
            }

            searchResults.innerHTML = '<div style="color: var(--text-secondary); text-align: center; font-size: 13px; padding: 30px 0;"><i class="fa-solid fa-spinner fa-spin" style="margin-right: 5px;"></i> Mencari...</div>';

            searchDebounceTimeout = setTimeout(() => {
                const basePath = window.BASE_PATH || '';
                const searchUrl = `${basePath}/api/search?q=${encodeURIComponent(query)}`;
                console.log('Search URL:', searchUrl);
                
                fetch(searchUrl)
                    .then(res => {
                        console.log('Response status:', res.status);
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Search results:', data);
                        if (!Array.isArray(data) || data.length === 0) {
                            searchResults.innerHTML = '<div style="color: var(--text-secondary); text-align: center; font-size: 13px; padding: 30px 0;">Tidak ada buku yang ditemukan.</div>';
                            return;
                        }

                        let html = '';
                        data.forEach(book => {
                            html += `
                                <a href="${basePath}/books/${book.id}" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 8px; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                                    <div style="width: 32px; height: 48px; border-radius: 3px; background: ${book.cover_url ? 'url(' + book.cover_url + ') center/cover' : 'linear-gradient(135deg, #007979, #004d4d)'}; box-shadow: 0 2px 4px rgba(0,0,0,0.15); flex-shrink: 0; position: relative;"></div>
                                    <div style="flex: 1; overflow: hidden;">
                                        <div style="font-size: 14px; font-weight: 600; color: var(--text-primary); text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">${book.title}</div>
                                        <div style="font-size: 12px; color: var(--text-secondary); text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">oleh ${book.author}</div>
                                    </div>
                                    <span style="font-size: 11px; padding: 3px 6px; border-radius: 4px; background: rgba(0, 121, 121, 0.1); color: var(--primary); font-weight: 600;">${book.status_label || ''}</span>
                                </a>
                            `;
                        });
                        searchResults.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        searchResults.innerHTML = '<div style="color: var(--danger); text-align: center; font-size: 13px; padding: 30px 0;">Gagal memuat hasil pencarian.</div>';
                    });
            }, 300);
        });
    }

    // 4. Toggle Sidebar Koleksi Buku Dropdown
    const btnToggleSidebarBooks = document.getElementById('btnToggleSidebarBooks');
    const sidebarBooksDropdown = document.getElementById('sidebarBooksDropdown');
    const sidebarChevron = document.getElementById('sidebarChevron');

    if (btnToggleSidebarBooks && sidebarBooksDropdown) {
        btnToggleSidebarBooks.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isHidden = sidebarBooksDropdown.style.display === 'none';
            if (isHidden) {
                sidebarBooksDropdown.style.display = 'flex';
                if (sidebarChevron) sidebarChevron.style.transform = 'rotate(180deg)';
            } else {
                sidebarBooksDropdown.style.display = 'none';
                if (sidebarChevron) sidebarChevron.style.transform = 'rotate(0deg)';
            }
        });
    }

    // 5. Open Library Auto-Fetch Cover Art (create.php)
    const inputTitle = document.getElementById('title');
    const inputAuthor = document.getElementById('author');
    const inputCoverUrl = document.getElementById('cover_url');
    const coverSuggestionInfo = document.getElementById('cover_suggestion_info');
    const coverPreviewWrapper = document.getElementById('cover_preview_wrapper');
    const coverPreviewImg = document.getElementById('cover_preview_img');

    function updatePreview(url) {
        if (url && url.trim() !== '') {
            coverPreviewImg.style.backgroundImage = `url('${url}')`;
            coverPreviewWrapper.style.display = 'flex';
        } else {
            coverPreviewWrapper.style.display = 'none';
        }
    }

    // Preview change when user inputs URL manually
    if (inputCoverUrl) {
        inputCoverUrl.addEventListener('input', () => {
            updatePreview(inputCoverUrl.value);
        });
    }

    // Auto search covers when Title or Author is typed
    let openLibraryDebounce;
    function autoFetchCover() {
        if (!inputTitle || !inputCoverUrl) return;
        
        const title = inputTitle.value.trim();
        const author = inputAuthor ? inputAuthor.value.trim() : '';

        // Only auto-search if cover_url input is empty
        if (title.length < 2 || inputCoverUrl.value.trim() !== '') return;

        clearTimeout(openLibraryDebounce);
        openLibraryDebounce = setTimeout(() => {
            coverSuggestionInfo.style.display = 'flex';
            
            let query = `title=${encodeURIComponent(title)}`;
            if (author.length > 0) {
                query += `&author=${encodeURIComponent(author)}`;
            }

            fetch(`https://openlibrary.org/search.json?${query}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    coverSuggestionInfo.style.display = 'none';
                    if (data.docs && data.docs.length > 0 && data.docs[0].cover_i) {
                        const coverId = data.docs[0].cover_i;
                        const coverUrl = `https://covers.openlibrary.org/b/id/${coverId}-M.jpg`;
                        
                        // Fill input and update preview
                        inputCoverUrl.value = coverUrl;
                        updatePreview(coverUrl);
                    }
                })
                .catch(err => {
                    coverSuggestionInfo.style.display = 'none';
                    console.error("Open Library cover fetch failed", err);
                });
        }, 1000);
    }

    if (inputTitle) {
        inputTitle.addEventListener('input', autoFetchCover);
    }
    if (inputAuthor) {
        inputAuthor.addEventListener('input', autoFetchCover);
    }
});
