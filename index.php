<?php
// index.php - AJAX Destekli Ana Sayfa
session_start();
// Veritabanı bağlantısı
try {
    $pdo = new PDO('sqlite:love_website.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
// AJAX istekleri için
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
   
    $action = $_GET['action'] ?? '';
   
    switch ($action) {
        case 'get_gallery':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = 6;
            $offset = ($page - 1) * $per_page;
           
            // Toplam fotoğraf sayısı
            $total_query = $pdo->query("SELECT COUNT(*) FROM gallery");
            $total = $total_query->fetchColumn();
            $total_pages = ceil($total / $per_page);
           
            // Fotoğrafları çek (ilk tarihten son tarihe)
            $stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY photo_date ASC LIMIT ? OFFSET ?");
            $stmt->execute([$per_page, $offset]);
            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
           
            echo json_encode([
                'success' => true,
                'photos' => $photos,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'per_page' => $per_page,
                    'total' => $total
                ]
            ]);
            exit;
           
        case 'get_music':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per_page = 10;
            $offset = ($page - 1) * $per_page;
           
            // Toplam müzik sayısı
            $total_query = $pdo->query("SELECT COUNT(*) FROM music");
            $total = $total_query->fetchColumn();
            $total_pages = ceil($total / $per_page);
           
            // Müzikleri çek
            $stmt = $pdo->prepare("SELECT * FROM music ORDER BY sort_order ASC, created_at ASC LIMIT ? OFFSET ?");
            $stmt->execute([$per_page, $offset]);
            $music = $stmt->fetchAll(PDO::FETCH_ASSOC);
           
            echo json_encode([
                'success' => true,
                'music' => $music,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'per_page' => $per_page,
                    'total' => $total
                ]
            ]);
            exit;
    }
   
    echo json_encode(['success' => false, 'message' => 'Geçersiz işlem']);
    exit;
}
// Tabloların varlığını kontrol et
$tables_exist = true;
try {
    $pdo->query("SELECT 1 FROM settings LIMIT 1");
    $pdo->query("SELECT 1 FROM milestones LIMIT 1");
    $pdo->query("SELECT 1 FROM gallery LIMIT 1");
    $pdo->query("SELECT 1 FROM music LIMIT 1");
} catch (PDOException $e) {
    $tables_exist = false;
}
if (!$tables_exist) {
    // Kurulum sayfasına yönlendir
    header('Location: setup.php');
    exit;
}
// Normal sayfa yükleme için veriler
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
$milestones = $pdo->query("SELECT * FROM milestones ORDER BY target_date")->fetchAll();
// Eğer ayarlar yoksa varsayılanları kullan
if (!$settings) {
    $settings = [
        'start_date' => '2024-03-15',
        'couple_names' => 'Bizim Hikayemiz'
    ];
}
// Kilometre taşlarının durumunu hesapla
function calculateCountdown($target_date) {
    $now = new DateTime();
    $target = new DateTime($target_date);
   
    if ($now > $target) {
        return null;
    }
   
    $diff = $now->diff($target);
   
    if ($diff->days > 30) {
        return $diff->days . ' gün';
    } elseif ($diff->days > 0) {
        return $diff->days . ' gün';
    } elseif ($diff->h > 0) {
        return $diff->h . ' saat';
    } else {
        return $diff->i . ' dakika';
    }
}
function isMilestoneCompleted($target_date) {
    $now = new DateTime();
    $target = new DateTime($target_date);
    return $now >= $target;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['couple_names']); ?> ❤️</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ffb6c1; /* Pastel pembe */
            --primary-rgb: 255, 182, 193;
            --secondary: #ff69b4; /* Sıcak pembe */
            --secondary-rgb: 255, 105, 180;
            --accent: #db7093; /* Soluk morumsu pembe */
            --accent-rgb: 219, 112, 147;
            --background: #fff0f5; /* Çok açık pembe */
            --surface: #ffffff;
            --text: #442222; /* Koyu bordo-kahve tonu kontrast için */
            --text-light: #664444;
            --border: #eed4d4;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        [data-theme="dark"] {
            --primary: #800020; /* Koyu bordo */
            --primary-rgb: 128, 0, 32;
            --secondary: #a00028; /* Daha koyu kırmızımsı bordo */
            --secondary-rgb: 160, 0, 40;
            --accent: #b0305c; /* Bordo vurgu */
            --accent-rgb: 176, 48, 92;
            --background: #200010; /* Çok koyu bordo */
            --surface: #300018;
            --text: #ffd0d0; /* Açık pembe metin */
            --text-light: #ffb0b0;
            --border: #500028;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        /* Header */
        .header {
            background: var(--gradient);
            padding: 4rem 0;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>') no-repeat center/cover;
        }
        .header h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: var(--shadow);
        }
        .header .subtitle {
            font-size: clamp(1rem, 3vw, 1.2rem);
            opacity: 0.9;
            font-weight: 300;
        }
        /* Section */
        .section {
            padding: clamp(3rem, 8vw, 5rem) 0;
        }
        .section-title {
            text-align: center;
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 600;
            margin-bottom: 3rem;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -0.75rem;
            left: 50%;
            transform: translateX(-50%);
            width: 3rem;
            height: 0.25rem;
            background: var(--gradient);
            border-radius: 0.125rem;
        }
        /* Love Counter */
        .love-counter {
            background: var(--surface);
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            margin-bottom: 5rem;
            transition: all 0.3s ease;
        }
        .counter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr));
            gap: 1.5rem;
            margin-top: 2.5rem;
        }
        .counter-item {
            text-align: center;
            padding: 1.5rem 1rem;
            background: var(--gradient);
            border-radius: 1rem;
            color: white;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }
        .counter-item:hover {
            transform: translateY(-0.25rem);
        }
        .counter-number {
            display: block;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .counter-label {
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            opacity: 0.9;
        }
        /* Music Section */
        .music-section {
            background: var(--surface);
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            margin-bottom: 5rem;
            transition: all 0.3s ease;
        }
        .music-controls-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .music-list {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
            min-height: 12.5rem;
        }
        .music-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: var(--background);
            border-radius: 0.75rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .music-item.active {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb), 0.05);
            box-shadow: var(--shadow);
        }
        .music-item:hover {
            transform: translateY(-0.125rem);
            box-shadow: var(--shadow);
        }
        .music-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-right: 1rem;
        }
        .play-btn {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: none;
            background: var(--gradient);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        .play-btn:hover {
            transform: scale(1.05);
        }
        .music-info {
            flex: 1;
        }
        .music-title {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .music-status {
            font-size: 0.9rem;
            color: var(--text-light);
        }
        .music-status.active {
            color: var(--primary);
            font-weight: 500;
        }
        /* Milestones */
        .milestones {
            background: var(--surface);
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            margin-bottom: 5rem;
            transition: all 0.3s ease;
        }
        .timeline {
            position: relative;
            margin-top: 2.5rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 1.875rem;
            top: 0;
            bottom: 0;
            width: 0.125rem;
            background: var(--border);
        }
        .milestone {
            position: relative;
            padding: 1.5rem 0 1.5rem 4rem;
            margin-bottom: 1rem;
        }
        .milestone::before {
            content: '';
            position: absolute;
            left: 1.3125rem;
            top: 2.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: var(--surface);
            border: 0.25rem solid var(--border);
        }
        .milestone.completed::before {
            background: var(--secondary);
            border-color: var(--secondary);
        }
        .milestone-content {
            background: var(--background);
            padding: 1.25rem;
            border-radius: 0.75rem;
            border-left: 0.25rem solid var(--primary);
            transition: all 0.3s ease;
        }
        .milestone-content:hover {
            box-shadow: var(--shadow);
        }
        .milestone-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .milestone-note {
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }
        .milestone-countdown {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(var(--primary-rgb), 0.1);
            color: var(--primary);
            border-radius: 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        /* Gallery */
        .gallery-section {
            background: var(--surface);
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
            gap: 1.5rem;
            margin: 2.5rem 0;
            min-height: 18.75rem;
        }
        .gallery-item {
            background: var(--background);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
        }
        .gallery-item:hover {
            transform: translateY(-0.5rem);
            box-shadow: var(--shadow-lg);
        }
        .gallery-item::before {
            content: '';
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            width: 0.75rem;
            height: 0.75rem;
            background: var(--primary);
            border-radius: 50%;
            z-index: 2;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.8);
        }
        .gallery-image {
            width: 100%;
            height: 15rem;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .gallery-image:hover {
            transform: scale(1.03);
        }
        .gallery-caption {
            padding: 1rem;
        }
        .gallery-date {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .gallery-note {
            color: var(--text-light);
            line-height: 1.5;
        }
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        .pagination-btn {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
        }
        .pagination-btn:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-0.125rem);
        }
        .pagination-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .pagination-info {
            padding: 0.5rem 0.75rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            backdrop-filter: blur(0.3125rem);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
        }
        .modal-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0.625rem;
        }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        .empty-state h3 {
            margin-bottom: 0.5rem;
        }
        /* Loading */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            color: var(--text-light);
        }
        .loading-spinner {
            width: 1.5rem;
            height: 1.5rem;
            border: 0.1875rem solid rgba(var(--primary-rgb), 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.75rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(1rem); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Progress bar for audio */
        .audio-progress {
            width: 100%;
            height: 0.25rem;
            background: var(--border);
            border-radius: 0.125rem;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        .audio-progress-bar {
            height: 100%;
            background: var(--primary);
            width: 0%;
            transition: width 0.1s ease;
        }
        .music-duration {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        }
        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .theme-toggle:hover {
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body data-theme="light">
    <!-- Theme Toggle -->
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <h1><?php echo htmlspecialchars($settings['couple_names']); ?></h1>
            <p class="subtitle">Birlikte yazdığımız aşk hikayesi ❤</p>
        </div>
    </header>
    <!-- Love Counter -->
    <section class="section">
        <div class="container">
            <article class="love-counter">
                <h2 class="section-title">Birlikte Geçirdiğimiz Zaman</h2>
                <div class="counter-grid">
                    <div class="counter-item">
                        <span class="counter-number" id="days">0</span>
                        <span class="counter-label">Gün</span>
                    </div>
                    <div class="counter-item">
                        <span class="counter-number" id="hours">0</span>
                        <span class="counter-label">Saat</span>
                    </div>
                    <div class="counter-item">
                        <span class="counter-number" id="minutes">0</span>
                        <span class="counter-label">Dakika</span>
                    </div>
                    <div class="counter-item">
                        <span class="counter-number" id="seconds">0</span>
                        <span class="counter-label">Saniye</span>
                    </div>
                </div>
            </article>
        </div>
    </section>
    <!-- Music Section -->
    <section class="section">
        <div class="container">
            <article class="music-section">
                <header class="music-controls-header">
                    <h2 class="section-title" style="margin-bottom: 0;">Müziklerimiz</h2>
                    <nav id="musicPagination" class="pagination"></nav>
                </header>
                <div id="musicList" class="music-list">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Müzikler yükleniyor...
                    </div>
                </div>
            </article>
        </div>
    </section>
    <!-- Milestones -->
    <section class="section">
        <div class="container">
            <article class="milestones">
                <h2 class="section-title">Kilometre Taşlarımız</h2>
                <?php if (empty($milestones)): ?>
                    <div class="empty-state">
                        <i class="fas fa-flag"></i>
                        <h3>Henüz kilometre taşı eklenmemiş</h3>
                        <p>İlk kilometre taşınızı eklemek için admin panelini kullanın.</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($milestones as $milestone): ?>
                            <?php
                            $is_completed = $milestone['completed'] || isMilestoneCompleted($milestone['target_date']);
                            $countdown = calculateCountdown($milestone['target_date']);
                            ?>
                            <div class="milestone <?php echo $is_completed ? 'completed' : ''; ?>">
                                <div class="milestone-content">
                                    <h3 class="milestone-title"><?php echo htmlspecialchars($milestone['title']); ?></h3>
                                    <?php if ($milestone['note']): ?>
                                        <p class="milestone-note"><?php echo htmlspecialchars($milestone['note']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!$is_completed && $countdown): ?>
                                        <span class="milestone-countdown"><?php echo $countdown; ?> kaldı</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>
        </div>
    </section>
    <!-- Gallery -->
    <section class="section">
        <div class="container">
            <article class="gallery-section">
                <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                    <h2 class="section-title" style="margin-bottom: 0;">Anılarımız</h2>
                    <nav id="galleryPagination" class="pagination"></nav>
                </header>
                <div id="galleryGrid" class="gallery-grid">
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Fotoğraflar yükleniyor...
                    </div>
                </div>
            </article>
        </div>
    </section>
    <!-- Image Modal -->
    <div class="modal" id="imageModal" onclick="closeModal()">
        <button class="modal-close" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-content">
            <img src="" alt="" class="modal-image" id="modalImage">
        </div>
    </div>
    <script>
        // Global variables
        const startDate = new Date('<?php echo $settings['start_date']; ?>T00:00:00');
        let currentMusic = null;
        let currentGalleryPage = 1;
        let currentMusicPage = 1;
        let galleryData = { total_pages: 1 };
        let musicData = { total_pages: 1 };
        // Love counter
        function updateCounter() {
            const now = new Date();
            const difference = now.getTime() - startDate.getTime();
           
            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);
           
            document.getElementById('days').textContent = days.toLocaleString();
            document.getElementById('hours').textContent = hours;
            document.getElementById('minutes').textContent = minutes;
            document.getElementById('seconds').textContent = seconds;
        }
       
        setInterval(updateCounter, 1000);
        updateCounter();
        // AJAX request helper
        function makeRequest(url, callback) {
            fetch(url)
                .then(response => response.json())
                .then(data => callback(data))
                .catch(error => {
                    console.error('AJAX Error:', error);
                    callback({ success: false, message: 'İstek hatası' });
                });
        }
        // Load gallery
        function loadGallery(page = 1) {
            currentGalleryPage = page;
            const galleryGrid = document.getElementById('galleryGrid');
           
            galleryGrid.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    Fotoğraflar yükleniyor...
                </div>
            `;
            makeRequest(`?ajax=1&action=get_gallery&page=${page}`, (data) => {
                if (data.success) {
                    galleryData = data.pagination;
                    displayGallery(data.photos);
                    updateGalleryPagination();
                } else {
                    galleryGrid.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <h3>Henüz fotoğraf yüklenmemiş</h3>
                            <p>İlk fotoğrafınızı eklemek için admin panelini kullanın.</p>
                        </div>
                    `;
                }
            });
        }
        // Display gallery
        function displayGallery(photos) {
            const galleryGrid = document.getElementById('galleryGrid');
           
            if (photos.length === 0) {
                galleryGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <h3>Bu sayfada fotoğraf bulunamadı</h3>
                        <p>Diğer sayfalara göz atabilirsiniz.</p>
                    </div>
                `;
                return;
            }
            const photosHTML = photos.map((photo, index) => `
                <figure class="gallery-item fade-in" style="animation-delay: ${index * 0.1}s">
                    <img src="${photo.image_path}"
                         alt="${photo.caption}"
                         class="gallery-image"
                         onclick="openModal('${photo.image_path}', '${photo.caption}')"
                         loading="lazy">
                    <figcaption class="gallery-caption">
                        <div class="gallery-date">${formatDate(photo.photo_date)}</div>
                        <div class="gallery-note">${photo.caption}</div>
                    </figcaption>
                </figure>
            `).join('');
           
            galleryGrid.innerHTML = photosHTML;
        }
        // Update gallery pagination
        function updateGalleryPagination() {
            const pagination = document.getElementById('galleryPagination');
           
            if (galleryData.total_pages <= 1) {
                pagination.innerHTML = '';
                return;
            }
            let paginationHTML = '';
           
            if (currentGalleryPage > 1) {
                paginationHTML += `
                    <button class="pagination-btn" onclick="loadGallery(${currentGalleryPage - 1})">
                        <i class="fas fa-chevron-left"></i> Önceki
                    </button>
                `;
            }
           
            const startPage = Math.max(1, currentGalleryPage - 2);
            const endPage = Math.min(galleryData.total_pages, currentGalleryPage + 2);
           
            if (startPage > 1) {
                paginationHTML += `<button class="pagination-btn" onclick="loadGallery(1)">1</button>`;
                if (startPage > 2) {
                    paginationHTML += `<span class="pagination-info">...</span>`;
                }
            }
           
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentGalleryPage ? 'active' : '';
                paginationHTML += `<button class="pagination-btn ${activeClass}" onclick="loadGallery(${i})">${i}</button>`;
            }
           
            if (endPage < galleryData.total_pages) {
                if (endPage < galleryData.total_pages - 1) {
                    paginationHTML += `<span class="pagination-info">...</span>`;
                }
                paginationHTML += `<button class="pagination-btn" onclick="loadGallery(${galleryData.total_pages})">${galleryData.total_pages}</button>`;
            }
           
            if (currentGalleryPage < galleryData.total_pages) {
                paginationHTML += `
                    <button class="pagination-btn" onclick="loadGallery(${currentGalleryPage + 1})">
                        Sonraki <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }
           
            paginationHTML += `<div class="pagination-info">${currentGalleryPage} / ${galleryData.total_pages} sayfa</div>`;
           
            pagination.innerHTML = paginationHTML;
        }
        // Load music
        function loadMusic(page = 1) {
            currentMusicPage = page;
            const musicList = document.getElementById('musicList');
           
            musicList.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    Müzikler yükleniyor...
                </div>
            `;
            makeRequest(`?ajax=1&action=get_music&page=${page}`, (data) => {
                if (data.success) {
                    musicData = data.pagination;
                    displayMusic(data.music);
                    updateMusicPagination();
                } else {
                    musicList.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-music"></i>
                            <h3>Henüz müzik eklenmemiş</h3>
                            <p>İlk müziğinizi eklemek için admin panelini kullanın.</p>
                        </div>
                    `;
                }
            });
        }
        // Display music
        function displayMusic(music) {
            const musicList = document.getElementById('musicList');
           
            if (music.length === 0) {
                musicList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-music"></i>
                        <h3>Bu sayfada müzik bulunamadı</h3>
                        <p>Diğer sayfalara göz atabilirsiniz.</p>
                    </div>
                `;
                return;
            }
            const musicHTML = music.map((song, index) => `
                <div class="music-item fade-in" style="animation-delay: ${index * 0.1}s" data-id="${song.id}">
                    <div class="music-controls">
                        <button class="play-btn" onclick="toggleMusic(${song.id})">
                            <i class="fas fa-play" id="playIcon${song.id}"></i>
                        </button>
                    </div>
                    <div class="music-info">
                        <div class="music-title">${song.title}</div>
                        <div class="music-status" id="status${song.id}">Hazır</div>
                        <div class="audio-progress">
                            <div class="audio-progress-bar" id="progress${song.id}"></div>
                        </div>
                        <div class="music-duration" id="duration${song.id}">--:--</div>
                    </div>
                    <audio id="audio${song.id}" preload="metadata" onloadedmetadata="updateDuration(${song.id})" ontimeupdate="updateProgress(${song.id})">
                        <source src="${song.file_path}" type="audio/mpeg">
                    </audio>
                </div>
            `).join('');
           
            musicList.innerHTML = musicHTML;
        }
        // Update music pagination
        function updateMusicPagination() {
            const pagination = document.getElementById('musicPagination');
           
            if (musicData.total_pages <= 1) {
                pagination.innerHTML = '';
                return;
            }
            let paginationHTML = '';
           
            if (currentMusicPage > 1) {
                paginationHTML += `
                    <button class="pagination-btn" onclick="loadMusic(${currentMusicPage - 1})">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;
            }
           
            const startPage = Math.max(1, currentMusicPage - 1);
            const endPage = Math.min(musicData.total_pages, currentMusicPage + 1);
           
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentMusicPage ? 'active' : '';
                paginationHTML += `<button class="pagination-btn ${activeClass}" onclick="loadMusic(${i})">${i}</button>`;
            }
           
            if (currentMusicPage < musicData.total_pages) {
                paginationHTML += `
                    <button class="pagination-btn" onclick="loadMusic(${currentMusicPage + 1})">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }
           
            pagination.innerHTML = paginationHTML;
        }
        // Music player functions
        function toggleMusic(musicId) {
            const audio = document.getElementById(`audio${musicId}`);
            const icon = document.getElementById(`playIcon${musicId}`);
            const status = document.getElementById(`status${musicId}`);
           
            if (!audio || !icon || !status) return;
            if (currentMusic && currentMusic.id !== musicId) {
                pauseMusic(currentMusic.id);
            }
            if (audio.paused) {
                audio.play().then(() => {
                    icon.className = 'fas fa-pause';
                    status.textContent = 'Çalıyor';
                    status.classList.add('active');
                    audio.closest('.music-item').classList.add('active');
                    currentMusic = { id: musicId, audio: audio };
                }).catch(e => {
                    console.error('Müzik çalma hatası:', e);
                    status.textContent = 'Hata oluştu';
                });
            } else {
                pauseMusic(musicId);
            }
            audio.addEventListener('ended', () => resetMusic(musicId), { once: true });
            audio.addEventListener('error', () => {
                status.textContent = 'Yüklenemiyor';
                icon.className = 'fas fa-exclamation-triangle';
            }, { once: true });
        }
        function pauseMusic(musicId) {
            const audio = document.getElementById(`audio${musicId}`);
            const icon = document.getElementById(`playIcon${musicId}`);
            const status = document.getElementById(`status${musicId}`);
           
            audio?.pause();
            icon.className = 'fas fa-play';
            status.textContent = 'Duraklatıldı';
            status.classList.remove('active');
            audio?.closest('.music-item').classList.remove('active');
           
            if (currentMusic?.id === musicId) currentMusic = null;
        }
        function resetMusic(musicId) {
            const audio = document.getElementById(`audio${musicId}`);
            const icon = document.getElementById(`playIcon${musicId}`);
            const status = document.getElementById(`status${musicId}`);
            const progress = document.getElementById(`progress${musicId}`);
           
            audio.currentTime = 0;
            icon.className = 'fas fa-play';
            status.textContent = 'Hazır';
            status.classList.remove('active');
            progress.style.width = '0%';
            audio.closest('.music-item').classList.remove('active');
            currentMusic = null;
        }
        function updateDuration(musicId) {
            const audio = document.getElementById(`audio${musicId}`);
            const duration = document.getElementById(`duration${musicId}`);
           
            if (audio && duration && !isNaN(audio.duration)) {
                duration.textContent = formatTime(audio.duration);
            }
        }
        function updateProgress(musicId) {
            const audio = document.getElementById(`audio${musicId}`);
            const progress = document.getElementById(`progress${musicId}`);
            const duration = document.getElementById(`duration${musicId}`);
           
            if (audio && progress && !isNaN(audio.duration) && audio.duration > 0) {
                const percent = (audio.currentTime / audio.duration) * 100;
                progress.style.width = percent + '%';
                duration.textContent = `${formatTime(audio.currentTime)} / ${formatTime(audio.duration)}`;
            }
        }
        function formatTime(seconds) {
            if (isNaN(seconds)) return '--:--';
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
        // Modal functions
        function openModal(imageSrc, caption = '') {
            const modalImage = document.getElementById('modalImage');
            const imageModal = document.getElementById('imageModal');
           
            modalImage.src = imageSrc;
            modalImage.alt = caption;
            imageModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            const imageModal = document.getElementById('imageModal');
            imageModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'long'
            };
            return date.toLocaleDateString('tr-TR', options);
        }
        // Theme toggle
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('themeIcon');
            if (body.dataset.theme === 'light') {
                body.dataset.theme = 'dark';
                icon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            } else {
                body.dataset.theme = 'light';
                icon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            }
        }
        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.body.dataset.theme = savedTheme;
        if (savedTheme === 'dark') {
            document.getElementById('themeIcon').className = 'fas fa-sun';
        }
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
            if (e.key === ' ' && currentMusic) {
                e.preventDefault();
                toggleMusic(currentMusic.id);
            }
        });
        // Prevent context menu on images
        document.addEventListener('contextmenu', (e) => {
            if (e.target.matches('.gallery-image, .modal-image')) e.preventDefault();
        });
        // Initialize page
        window.addEventListener('DOMContentLoaded', () => {
            loadGallery(1);
            loadMusic(1);
            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelector(anchor.getAttribute('href'))?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });
            // Lazy loading observer
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('loaded');
                        imageObserver.unobserve(entry.target);
                    }
                });
            });
            document.querySelectorAll('img[loading="lazy"]').forEach(img => imageObserver.observe(img));
        });
        // Pause music on unload/visibility change
        window.addEventListener('beforeunload', () => currentMusic?.audio.pause());
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && currentMusic) pauseMusic(currentMusic.id);
        });
    </script>
</body>
</html>
