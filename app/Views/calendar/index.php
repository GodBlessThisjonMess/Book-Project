<?php
// ====================================================================
// VIEW KALENDER MEMBACA BULANAN
// ====================================================================

// Nama bulan dalam Bahasa Indonesia
$namaBulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Dapatkan hari pertama & jumlah hari
$firstDayOfMonth = strtotime("$year-$month-01");
$jumlahHari = (int)date('t', $firstDayOfMonth);
$dayOfWeek  = (int)date('N', $firstDayOfMonth); // 1 (Senin) - 7 (Minggu)
$emptyCells = $dayOfWeek - 1; // Jumlah kotak kosong di awal minggu grid Senin

// Hitung navigasi bulan lalu
$prevMonth = $month - 1;
$prevYear  = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

// Hitung navigasi bulan depan
$nextMonth = $month + 1;
$nextYear  = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
    <div>
        <h2>Kalender Membaca Buku</h2>
        <p style="color: var(--text-secondary); margin-top: 5px;">Jadwalkan dan lacak rutinitas membaca harian Anda secara terorganisir.</p>
    </div>
</div>

<!-- Flash Message Notifications -->
<?php if (isset($_SESSION['flash_success'])): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--success); color: var(--success); padding: 12px 20px; border-radius: var(--radius); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></span>
    </div>
<?php endif; ?>

<!-- Kontainer Utama Kalender -->
<div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 30px; align-items: start; flex-wrap: wrap;">
    
    <!-- Bagian 1: Grid Kalender Interaktif (Kiri) -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow);">
        
        <!-- Bar Navigasi Bulan -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <a href="<?= BASE_URL ?>/calendar?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="btn" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--primary); padding: 8px 16px;">
                <i class="fa-solid fa-chevron-left"></i> Bulan Lalu
            </a>
            
            <h3 style="font-size: 20px; font-weight: 700; color: var(--primary);"><?= $namaBulan[$month] . ' ' . $year ?></h3>
            
            <a href="<?= BASE_URL ?>/calendar?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--primary); padding: 8px 16px;">
                Bulan Depan <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <!-- Grid Kalender Bulanan -->
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px;">
            
            <!-- Header Hari (Sen - Min) -->
            <?php 
            $hariMenu = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            foreach ($hariMenu as $hari): 
            ?>
                <div style="text-align: center; font-weight: 700; font-size: 13px; color: var(--text-secondary); padding: 10px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                    <?= $hari ?>
                </div>
            <?php endforeach; ?>

            <!-- Sel Kosong Awal Bulan -->
            <?php for ($i = 0; $i < $emptyCells; $i++): ?>
                <div style="background: rgba(0,0,0,0.01); border: 1px dashed rgba(0,0,0,0.04); border-radius: 8px; min-height: 95px;"></div>
            <?php endfor; ?>

            <!-- Render Hari Aktif -->
            <?php 
            for ($day = 1; $day <= $jumlahHari; $day++): 
                $formattedDate = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $isToday = ($formattedDate === date('Y-m-d'));
                $hasSchedule = isset($schedulesByDate[$formattedDate]);
            ?>
                <div style="background: <?= $isToday ? 'rgba(0, 121, 121, 0.04)' : 'var(--bg-secondary)' ?>; 
                            border: 1px solid <?= $isToday ? 'var(--primary)' : 'var(--border-color)' ?>; 
                            border-radius: 8px; min-height: 95px; padding: 8px; display: flex; flex-direction: column; justify-content: space-between; transition: var(--transition);">
                    
                    <!-- Angka Tanggal -->
                    <span style="font-size: 13px; font-weight: 700; color: <?= $isToday ? 'var(--primary)' : 'var(--text-primary)' ?>;">
                        <?= $day ?>
                    </span>

                    <!-- Buku Terjadwal -->
                    <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 8px; max-height: 55px; overflow-y: auto;">
                        <?php if ($hasSchedule): ?>
                            <?php foreach ($schedulesByDate[$formattedDate] as $sched): ?>
                                <div style="font-size: 10px; font-weight: 600; padding: 4px 6px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; gap: 4px;
                                    <?php 
                                    if ($sched['book_status'] === 'On Going') echo 'background: rgba(6, 182, 212, 0.15); color: #06b6d4;';
                                    elseif ($sched['book_status'] === 'Done') echo 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
                                    elseif ($sched['book_status'] === 'Unfinished') echo 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
                                    else echo 'background: rgba(100, 116, 139, 0.15); color: #64748b;';
                                    ?>">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65px;" title="<?= htmlspecialchars($sched['book_title']) ?>">
                                        <?= htmlspecialchars($sched['book_title']) ?>
                                    </span>
                                    
                                    <!-- Aksi Batal Jadwal Cepat -->
                                    <form action="<?= BASE_URL ?>/calendar/<?= $sched['id'] ?>/delete" method="POST" style="display: inline-block;">
                                        <input type="hidden" name="year" value="<?= $year ?>">
                                        <input type="hidden" name="month" value="<?= $month ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 10px; padding: 0;" title="Hapus jadwal">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Bagian 2: Form Penjadwalan Cepat (Kanan) -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: var(--primary); display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-calendar-plus"></i> Jadwalkan Buku
        </h3>
        
        <?php if (empty($books)): ?>
            <div style="text-align: center; padding: 30px 20px; color: var(--text-secondary); border: 1px dashed var(--border-color); border-radius: 8px; font-size: 13px; background: var(--bg-secondary);">
                <i class="fa-solid fa-book-open" style="font-size: 28px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                Belum ada buku untuk dijadwalkan.<br>
                <a href="<?= BASE_URL ?>/books/create" style="color: var(--primary); text-decoration: none; font-weight: 600; display: inline-block; margin-top: 8px;">Tambah Buku Pertama &rarr;</a>
            </div>
        <?php else: ?>
            <form action="<?= BASE_URL ?>/calendar" method="POST">
                <!-- Dropdown Seleksi Buku -->
                <div style="margin-bottom: 20px;">
                    <label for="book_id" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">Pilih Buku Koleksi:</label>
                    <select id="book_id" name="book_id" required style="width: 100%; padding: 12px; background: #fff; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 14px; color: var(--text-primary);">
                        <?php foreach ($books as $book): ?>
                            <option value="<?= $book->id ?>"><?= htmlspecialchars($book->title) ?> (<?= htmlspecialchars($book->author) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tanggal Picker -->
                <div style="margin-bottom: 25px;">
                    <label for="reading_date" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">Pilih Tanggal:</label>
                    <input type="date" id="reading_date" name="reading_date" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 10px; background: #fff; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 14px; color: var(--text-primary);">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                    <i class="fa-solid fa-circle-check"></i> Jadwalkan Buku
                </button>
            </form>
        <?php endif; ?>
    </div>

</div>
