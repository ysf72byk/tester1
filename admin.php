<?php
// admin.php - Güncellenmiş Admin Panel
session_start();
header('Content-Type: text/html; charset=UTF-8');
// AJAX istekleri için
if (isset($_POST['ajax'])) {
    header('Content-Type: application/json');
   
    // Güvenlik kontrolü
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
        exit;
    }
    // Veritabanı bağlantısı
    try {
        $pdo = new PDO('sqlite:love_website.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
        exit;
    }
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'update_settings':
            try {
                $stmt = $pdo->prepare("UPDATE settings SET start_date = ?, couple_names = ? WHERE id = 1");
                $result = $stmt->execute([$_POST['start_date'], $_POST['couple_names']]);
                echo json_encode(['success' => $result, 'message' => $result ? 'Ayarlar güncellendi!' : 'Hata oluştu']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Güncelleme hatası: ' . $e->getMessage()]);
            }
            break;
        case 'add_milestone':
            try {
                $stmt = $pdo->prepare("INSERT INTO milestones (title, note, completed, target_date) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$_POST['title'], $_POST['note'] ?? '', $_POST['completed'] ?? 0, $_POST['target_date']]);
               
                if ($result) {
                    $milestone_id = $pdo->lastInsertId();
                    $milestone = $pdo->prepare("SELECT * FROM milestones WHERE id = ?");
                    $milestone->execute([$milestone_id]);
                    $data = $milestone->fetch(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'message' => 'Kilometre taşı eklendi!', 'data' => $data]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Ekleme hatası']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'update_milestone':
            try {
                $stmt = $pdo->prepare("UPDATE milestones SET title = ?, note = ?, completed = ?, target_date = ? WHERE id = ?");
                $result = $stmt->execute([$_POST['title'], $_POST['note'] ?? '', $_POST['completed'] ?? 0, $_POST['target_date'], $_POST['id']]);
                echo json_encode(['success' => $result, 'message' => $result ? 'Kilometre taşı güncellendi!' : 'Güncelleme hatası']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'delete_milestone':
            try {
                $stmt = $pdo->prepare("DELETE FROM milestones WHERE id = ?");
                $result = $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => $result, 'message' => $result ? 'Kilometre taşı silindi!' : 'Silme hatası']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'add_photo':
            try {
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $upload_dir = 'resimler/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                   
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                   
                    if (!in_array($file_extension, $allowed_types)) {
                        echo json_encode(['success' => false, 'message' => 'Desteklenmeyen dosya türü']);
                        break;
                    }
                   
                    if ($_FILES['image']['size'] > 15 * 1024 * 1024) {
                        echo json_encode(['success' => false, 'message' => 'Dosya boyutu çok büyük (max 15MB)']);
                        break;
                    }
                   
                    $new_filename = date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                   
                    // Resim optimizasyonu
                    $image_info = getimagesize($_FILES['image']['tmp_name']);
                    if ($image_info !== false) {
                        $source = null;
                        switch ($image_info['mime']) {
                            case 'image/jpeg':
                                $source = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                                break;
                            case 'image/png':
                                $source = imagecreatefrompng($_FILES['image']['tmp_name']);
                                break;
                            case 'image/gif':
                                $source = imagecreatefromgif($_FILES['image']['tmp_name']);
                                break;
                            case 'image/webp':
                                $source = imagecreatefromwebp($_FILES['image']['tmp_name']);
                                break;
                        }
                       
                        if ($source) {
                            // Boyutları ayarla (max 1920x1080)
                            $original_width = imagesx($source);
                            $original_height = imagesy($source);
                            $max_width = 1920;
                            $max_height = 1080;
                           
                            if ($original_width > $max_width || $original_height > $max_height) {
                                $ratio = min($max_width / $original_width, $max_height / $original_height);
                                $new_width = round($original_width * $ratio);
                                $new_height = round($original_height * $ratio);
                               
                                $resized = imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($resized, $source, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
                               
                                imagejpeg($resized, $upload_path, 85);
                                imagedestroy($resized);
                            } else {
                                imagejpeg($source, $upload_path, 85);
                            }
                            imagedestroy($source);
                        } else {
                            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
                        }
                    } else {
                        move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
                    }
                   
                    if (file_exists($upload_path)) {
                        $stmt = $pdo->prepare("INSERT INTO gallery (image_path, caption, photo_date, created_at) VALUES (?, ?, ?, datetime('now'))");
                        $result = $stmt->execute([$upload_path, $_POST['caption'], $_POST['photo_date']]);
                       
                        if ($result) {
                            $photo_id = $pdo->lastInsertId();
                            $photo = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
                            $photo->execute([$photo_id]);
                            $data = $photo->fetch(PDO::FETCH_ASSOC);
                            echo json_encode(['success' => true, 'message' => 'Fotoğraf yüklendi!', 'data' => $data]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Dosya yüklenemedi']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Dosya seçilmedi veya hata oluştu']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'delete_photo':
            try {
                $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $image_path = $stmt->fetchColumn();
               
                if ($image_path && file_exists($image_path)) {
                    unlink($image_path);
                }
               
                $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
                $result = $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => $result, 'message' => $result ? 'Fotoğraf silindi!' : 'Silme hatası']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'upload_music':
            try {
                if (isset($_FILES['music']) && $_FILES['music']['error'] == 0) {
                    $upload_dir = 'music/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                   
                    $file_extension = strtolower(pathinfo($_FILES['music']['name'], PATHINFO_EXTENSION));
                    $allowed_types = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
                   
                    if (!in_array($file_extension, $allowed_types)) {
                        echo json_encode(['success' => false, 'message' => 'Desteklenmeyen ses dosyası türü']);
                        break;
                    }
                   
                    if ($_FILES['music']['size'] > 25 * 1024 * 1024) {
                        echo json_encode(['success' => false, 'message' => 'Dosya boyutu çok büyük (max 25MB)']);
                        break;
                    }
                   
                    $new_filename = date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                   
                    if (move_uploaded_file($_FILES['music']['tmp_name'], $upload_path)) {
                        $stmt = $pdo->prepare("INSERT INTO music (title, file_path, is_active, created_at, sort_order) VALUES (?, ?, 0, datetime('now'), 0)");
                        $result = $stmt->execute([$_POST['title'], $upload_path]);
                       
                        if ($result) {
                            $music_id = $pdo->lastInsertId();
                            $max_sort = $pdo->query("SELECT MAX(sort_order) FROM music")->fetchColumn();
                            $sort_order = $max_sort ? $max_sort + 1 : 1;
                            $stmt = $pdo->prepare("UPDATE music SET sort_order = ? WHERE id = ?");
                            $stmt->execute([$sort_order, $music_id]);
                            $music = $pdo->prepare("SELECT * FROM music WHERE id = ?");
                            $music->execute([$music_id]);
                            $data = $music->fetch(PDO::FETCH_ASSOC);
                            echo json_encode(['success' => true, 'message' => 'Müzik yüklendi!', 'data' => $data]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Dosya yüklenemedi']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Dosya seçilmedi veya hata oluştu']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'toggle_music':
            try {
                // Seçili müziğin mevcut durumunu al
                $stmt = $pdo->prepare("SELECT is_active FROM music WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $current_status = $stmt->fetchColumn();
               
                if ($current_status === false) {
                    echo json_encode(['success' => false, 'message' => 'Müzik bulunamadı']);
                    break;
                }
               
                $new_status = $current_status ? 0 : 1;
               
                // Eğer aktif yapılıyorsa, diğerlerini pasif yap
                if ($new_status == 1) {
                    $pdo->exec("UPDATE music SET is_active = 0");
                }
               
                // Seçili müziği güncelle
                $stmt = $pdo->prepare("UPDATE music SET is_active = ? WHERE id = ?");
                $result = $stmt->execute([$new_status, $_POST['id']]);
               
                echo json_encode([
                    'success' => $result,
                    'message' => $new_status ? 'Müzik aktif edildi!' : 'Müzik pasif edildi!',
                    'is_active' => $new_status
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'delete_music':
            try {
                $stmt = $pdo->prepare("SELECT file_path FROM music WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $file_path = $stmt->fetchColumn();
               
                if ($file_path && file_exists($file_path)) {
                    unlink($file_path);
                }
               
                $stmt = $pdo->prepare("DELETE FROM music WHERE id = ?");
                $result = $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => $result, 'message' => $result ? 'Müzik silindi!' : 'Silme hatası']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'get_gallery_photos':
            try {
                $page = (int)($_POST['page'] ?? 1);
                $per_page = 12;
                $offset = ($page - 1) * $per_page;
               
                $total_query = $pdo->query("SELECT COUNT(*) FROM gallery");
                $total = $total_query->fetchColumn();
                $total_pages = ceil($total / $per_page);
               
                $stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY photo_date DESC, created_at DESC LIMIT ? OFFSET ?");
                $stmt->execute([$per_page, $offset]);
                $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
               
                echo json_encode([
                    'success' => true,
                    'data' => $photos,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $total_pages,
                        'per_page' => $per_page,
                        'total_items' => $total
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'get_music_list':
            try {
                $stmt = $pdo->prepare("SELECT * FROM music ORDER BY sort_order ASC, created_at ASC");
                $stmt->execute();
                $music = $stmt->fetchAll(PDO::FETCH_ASSOC);
               
                echo json_encode([
                    'success' => true,
                    'data' => $music
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        case 'update_music_order':
            try {
                $orders = json_decode($_POST['orders'], true);
                foreach ($orders as $order) {
                    $stmt = $pdo->prepare("UPDATE music SET sort_order = ? WHERE id = ?");
                    $stmt->execute([$order['position'], $order['id']]);
                }
                echo json_encode(['success' => true, 'message' => 'Sıralama güncellendi!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Geçersiz işlem']);
    }
    exit;
}
// Logout işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}
// Normal sayfa yükleme için güvenlik kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    if (isset($_POST['password'])) {
        $admin_password = 'admin123'; // Bu şifreyi değiştirin!
       
        if ($_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Hatalı şifre!';
        }
    }
   
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Giriş</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
            <style>
                :root {
                    --primary: #ff6b6b;
                    --background: #f8fafc;
                    --surface: #ffffff;
                    --text: #2d3748;
                    --border: #e2e8f0;
                    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                    --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
               
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
               
                body {
                    font-family: 'Inter', sans-serif;
                    background: var(--gradient);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
               
                .login-form {
                    background: var(--surface);
                    padding: 40px;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 400px;
                    width: 100%;
                    margin: 20px;
                }
               
                .login-form h2 {
                    color: var(--primary);
                    margin-bottom: 30px;
                    font-size: 2rem;
                }
               
                .form-group {
                    margin-bottom: 20px;
                    text-align: left;
                }
               
                .form-control {
                    width: 100%;
                    padding: 15px;
                    border: 2px solid var(--border);
                    border-radius: 10px;
                    font-size: 16px;
                    transition: border-color 0.3s ease;
                }
               
                .form-control:focus {
                    outline: none;
                    border-color: var(--primary);
                }
               
                .btn {
                    background: var(--gradient);
                    color: white;
                    padding: 15px 30px;
                    border: none;
                    border-radius: 10px;
                    font-size: 16px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    width: 100%;
                }
               
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: var(--shadow);
                }
               
                .error {
                    color: #dc3545;
                    margin-bottom: 20px;
                    padding: 10px;
                    background: rgba(220, 53, 69, 0.1);
                    border-radius: 8px;
                }
            </style>
        </head>
        <body>
            <form class="login-form" method="post">
                <h2><i class="fas fa-heart"></i> Admin Giriş</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Admin Şifresi" required>
                </div>
                <button type="submit" class="btn">Giriş Yap</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}
// Veritabanı bağlantısı
try {
    $pdo = new PDO('sqlite:love_website.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    // Tabloların varlığını kontrol et
    try {
        $pdo->query("SELECT 1 FROM settings LIMIT 1");
    } catch (PDOException $e) {
        // Kurulum sayfasına yönlendir
        header('Location: setup.php');
        exit;
    }
    // Add sort_order column if not exists
    try {
        $pdo->exec("ALTER TABLE music ADD COLUMN sort_order INTEGER DEFAULT 0");
    } catch (PDOException $e) {
        // Column already exists, ignore
    }
    // Initialize sort_orders if all are 0
    $check = $pdo->query("SELECT COUNT(*) FROM music WHERE sort_order > 0")->fetchColumn();
    if ($check == 0) {
        $musics = $pdo->query("SELECT id FROM music ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($musics as $i => $m) {
            $stmt = $pdo->prepare("UPDATE music SET sort_order = ? WHERE id = ?");
            $stmt->execute([$i + 1, $m['id']]);
        }
    }
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
// Verileri çek
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
$milestones = $pdo->query("SELECT * FROM milestones ORDER BY target_date")->fetchAll();
// İstatistikler
$stats = [
    'total_photos' => $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn(),
    'total_music' => $pdo->query("SELECT COUNT(*) FROM music")->fetchColumn(),
    'total_milestones' => $pdo->query("SELECT COUNT(*) FROM milestones")->fetchColumn(),
    'completed_milestones' => $pdo->query("SELECT COUNT(*) FROM milestones WHERE completed = 1")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Love Website</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b6b;
            --secondary: #4ecdc4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --background: #f8fafc;
            --surface: #ffffff;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }
        .admin-header {
            background: var(--gradient);
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow-lg);
        }
        .admin-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }
        .header-actions {
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-secondary {
            background: var(--surface);
            color: var(--text);
        }
        .btn-success { background: var(--success); color: white; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: var(--surface);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 5px;
        }
        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        .admin-tabs {
            display: flex;
            background: var(--surface);
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            flex-wrap: wrap;
        }
        .tab-btn {
            flex: 1;
            min-width: 200px;
            padding: 15px 20px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .tab-btn.active {
            background: var(--primary);
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .section {
            background: var(--surface);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        .item-list {
            display: grid;
            gap: 20px;
        }
        .list-item {
            background: var(--background);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .list-item:hover {
            box-shadow: var(--shadow);
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .item-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            flex: 1;
        }
        .item-actions {
            display: flex;
            gap: 10px;
        }
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid;
            position: relative;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success);
            color: var(--success);
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay.active {
            display: flex;
        }
        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }
        .spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 107, 107, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }
        .music-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .music-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .play-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--primary);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .music-info {
            flex: 1;
        }
        .music-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .music-status {
            font-size: 0.9rem;
            color: var(--text-light);
        }
        .music-status.active {
            color: var(--success);
            font-weight: 500;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: var(--success);
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .gallery-item {
            background: var(--background);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .gallery-info {
            padding: 15px;
        }
        .gallery-date {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 5px;
        }
        .gallery-caption {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
        }
        .pagination-btn:hover:not(.active):not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
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
            color: var(--text-light);
            font-size: 14px;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .admin-header .container {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .admin-tabs {
                flex-direction: column;
            }
            .tab-btn {
                min-width: auto;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .item-header {
                flex-direction: column;
                gap: 10px;
            }
            .item-actions {
                justify-content: center;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }
        /* Loading states */
        .loading-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: var(--text-light);
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 107, 107, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-light);
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        .drag-handle {
            cursor: move;
            color: var(--text-light);
            font-size: 1.2rem;
            padding: 0 5px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <h1><i class="fas fa-heart"></i> Love Website Admin</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-home"></i> Ana Sayfa
                </a>
                <a href="?logout=1" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Çıkış
                </a>
            </div>
        </div>
    </div>
    <div class="main-content">
        <!-- Alert alanı -->
        <div id="alertContainer"></div>
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_photos']; ?></div>
                <div class="stat-label">Toplam Fotoğraf</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-music"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_music']; ?></div>
                <div class="stat-label">Müzik Dosyası</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_milestones']; ?></div>
                <div class="stat-label">Kilometre Taşı</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $stats['completed_milestones']; ?></div>
                <div class="stat-label">Tamamlanan</div>
            </div>
        </div>
        <!-- Tab Navigation -->
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="showTab('settings')">
                <i class="fas fa-cog"></i> Genel Ayarlar
            </button>
            <button class="tab-btn" onclick="showTab('milestones')">
                <i class="fas fa-flag"></i> Kilometre Taşları
            </button>
            <button class="tab-btn" onclick="showTab('music')">
                <i class="fas fa-music"></i> Müzikler
            </button>
            <button class="tab-btn" onclick="showTab('gallery')">
                <i class="fas fa-images"></i> Galeri
            </button>
        </div>
        <!-- Settings Tab -->
        <div id="settings" class="tab-content active">
            <div class="section">
                <h2 class="section-title">Genel Ayarlar</h2>
                <form id="settingsForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($settings['start_date'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site Başlığı</label>
                            <input type="text" name="couple_names" class="form-control" value="<?php echo htmlspecialchars($settings['couple_names'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </form>
            </div>
        </div>
        <!-- Milestones Tab -->
        <div id="milestones" class="tab-content">
            <div class="section">
                <h2 class="section-title">Yeni Kilometre Taşı Ekle</h2>
                <form id="milestoneForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Başlık</label>
                            <input type="text" name="title" class="form-control" placeholder="Örn: 1. Yıl Dönümümüz" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hedef Tarih</label>
                            <input type="date" name="target_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Not</label>
                        <textarea name="note" class="form-control" placeholder="Kilometre taşı ile ilgili notunuz..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="completed" value="1"> Tamamlandı
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ekle
                    </button>
                </form>
            </div>
            <div class="section">
                <h2 class="section-title">Mevcut Kilometre Taşları</h2>
                <div id="milestoneList" class="item-list">
                    <?php foreach ($milestones as $milestone): ?>
                        <div class="list-item" data-id="<?php echo $milestone['id']; ?>">
                            <div class="item-header">
                                <div class="item-title"><?php echo htmlspecialchars($milestone['title']); ?></div>
                                <div class="item-actions">
                                    <button class="btn btn-sm btn-danger" onclick="deleteMilestone(<?php echo $milestone['id']; ?>)">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </div>
                            </div>
                            <div class="item-content">
                                <p><strong>Tarih:</strong> <?php echo date('d.m.Y', strtotime($milestone['target_date'])); ?></p>
                                <p><strong>Durum:</strong> <?php echo $milestone['completed'] ? 'Tamamlandı ✅' : 'Bekliyor ⏳'; ?></p>
                                <?php if ($milestone['note']): ?>
                                    <p><strong>Not:</strong> <?php echo htmlspecialchars($milestone['note']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Music Tab -->
        <div id="music" class="tab-content">
            <div class="section">
                <h2 class="section-title">Yeni Müzik Ekle</h2>
                <form id="musicForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Müzik Dosyası</label>
                            <input type="file" name="music" class="form-control" accept="audio/*" required>
                            <small style="color: var(--text-light);">Desteklenen formatlar: MP3, WAV, OGG, M4A, AAC (Max: 25MB)</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Başlık</label>
                            <input type="text" name="title" class="form-control" placeholder="Şarkı adı..." required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Yükle
                    </button>
                </form>
            </div>
            <div class="section">
                <h2 class="section-title">Müzik Listesi</h2>
                <div id="musicPaginationTop" class="pagination"></div>
                <div id="musicListContainer">
                    <div class="loading-grid">
                        <div class="loading-spinner"></div>
                        Müzikler yükleniyor...
                    </div>
                </div>
                <div id="musicPaginationBottom" class="pagination"></div>
            </div>
        </div>
        <!-- Gallery Tab -->
        <div id="gallery" class="tab-content">
            <div class="section">
                <h2 class="section-title">Yeni Fotoğraf Ekle</h2>
                <form id="galleryForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Fotoğraf</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <small style="color: var(--text-light);">Desteklenen formatlar: JPG, PNG, GIF, WEBP (Max: 15MB)</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="photo_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea name="caption" class="form-control" placeholder="Fotoğraf açıklaması..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Yükle
                    </button>
                </form>
            </div>
            <div class="section">
                <h2 class="section-title">Fotoğraf Galerisi</h2>
                <div id="galleryPaginationTop" class="pagination"></div>
                <div id="galleryContainer">
                    <div class="loading-grid">
                        <div class="loading-spinner"></div>
                        Fotoğraflar yükleniyor...
                    </div>
                </div>
                <div id="galleryPaginationBottom" class="pagination"></div>
            </div>
        </div>
    </div>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>İşlem yapılıyor...</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Global variables
        let currentMusicPlaying = null;
        let currentGalleryPage = 1;
        let currentMusicPage = 1;
        let galleryData = { total_pages: 1 };
        let musicData = { total_pages: 1 };
        // Tab switching
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
           
            // Hide all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
           
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
            // Load data for specific tabs
            if (tabName === 'gallery' && currentGalleryPage === 1) {
                loadGalleryPhotos(1);
            } else if (tabName === 'music') {
                loadMusicList();
            }
        }
        // Show alert
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
           
            const alertHTML = `
                <div class="alert ${alertClass}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    ${message}
                </div>
            `;
           
            alertContainer.innerHTML = alertHTML;
           
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
            // Scroll to top to show alert
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        // Show/hide loading
        function showLoading(show = true) {
            const loading = document.getElementById('loadingOverlay');
            if (show) {
                loading.classList.add('active');
            } else {
                loading.classList.remove('active');
            }
        }
        // AJAX helper function
        function sendAjaxRequest(formData, successCallback) {
            showLoading(true);
           
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                if (data.success) {
                    showAlert(data.message, 'success');
                    if (successCallback) successCallback(data);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showLoading(false);
                showAlert('Bir hata oluştu: ' + error.message, 'error');
                console.error('Error:', error);
            });
        }
        // Load gallery photos with pagination
        function loadGalleryPhotos(page = 1) {
            currentGalleryPage = page;
            const container = document.getElementById('galleryContainer');
           
            container.innerHTML = `
                <div class="loading-grid">
                    <div class="loading-spinner"></div>
                    Fotoğraflar yükleniyor...
                </div>
            `;
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'get_gallery_photos');
            formData.append('page', page);
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    galleryData = data.pagination;
                    displayGalleryPhotos(data.data);
                    updateGalleryPagination();
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <h3>Fotoğraf bulunamadı</h3>
                        </div>
                    `;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Yükleme hatası</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            });
        }
        // Display gallery photos
        function displayGalleryPhotos(photos) {
            const container = document.getElementById('galleryContainer');
           
            if (photos.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <h3>Bu sayfada fotoğraf bulunamadı</h3>
                    </div>
                `;
                return;
            }
            const photosHTML = photos.map(photo => {
                const photoDate = new Date(photo.photo_date);
                const formattedDate = photoDate.toLocaleDateString('tr-TR');
               
                return `
                    <div class="gallery-item" data-id="${photo.id}">
                        <img src="${photo.image_path}" alt="${photo.caption}" class="gallery-image">
                        <div class="gallery-info">
                            <div class="gallery-date">${formattedDate}</div>
                            <div class="gallery-caption">${photo.caption}</div>
                            <button class="btn btn-sm btn-danger" onclick="deletePhoto(${photo.id})">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
           
            container.innerHTML = `<div class="gallery-grid">${photosHTML}</div>`;
        }
        // Update gallery pagination
        function updateGalleryPagination() {
            const topPagination = document.getElementById('galleryPaginationTop');
            const bottomPagination = document.getElementById('galleryPaginationBottom');
           
            if (galleryData.total_pages <= 1) {
                topPagination.innerHTML = '';
                bottomPagination.innerHTML = '';
                return;
            }
            const paginationHTML = generatePaginationHTML(
                currentGalleryPage,
                galleryData.total_pages,
                'loadGalleryPhotos'
            );
           
            topPagination.innerHTML = paginationHTML;
            bottomPagination.innerHTML = paginationHTML;
        }
        // Load music list with pagination
        function loadMusicList() {
            const container = document.getElementById('musicListContainer');
           
            container.innerHTML = `
                <div class="loading-grid">
                    <div class="loading-spinner"></div>
                    Müzikler yükleniyor...
                </div>
            `;
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'get_music_list');
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMusicList(data.data);
                    document.getElementById('musicPaginationTop').innerHTML = '';
                    document.getElementById('musicPaginationBottom').innerHTML = '';
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-music"></i>
                            <h3>Müzik bulunamadı</h3>
                        </div>
                    `;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Yükleme hatası</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            });
        }
        // Display music list
        function displayMusicList(music) {
            const container = document.getElementById('musicListContainer');
           
            if (music.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-music"></i>
                        <h3>Bu sayfada müzik bulunamadı</h3>
                    </div>
                `;
                return;
            }
            const musicHTML = music.map(song => `
                <div class="list-item music-item" data-id="${song.id}">
                    <div class="drag-handle"><i class="fas fa-grip-lines"></i></div>
                    <div class="music-controls">
                        <button class="play-btn" onclick="previewMusic(${song.id})">
                            <i class="fas fa-play" id="playIcon${song.id}"></i>
                        </button>
                        <audio style="display: none;" id="audio${song.id}">
                            <source src="${song.file_path}" type="audio/mpeg">
                        </audio>
                    </div>
                    <div class="music-info">
                        <div class="music-title">${song.title}</div>
                        <div class="music-status ${song.is_active ? 'active' : ''}" id="musicStatus${song.id}">
                            ${song.is_active ? 'Ana sayfada aktif' : 'Pasif'}
                        </div>
                    </div>
                    <div class="item-actions">
                        <label class="toggle-switch">
                            <input type="checkbox" ${song.is_active ? 'checked' : ''}
                                   onchange="toggleMusic(${song.id})" id="musicToggle${song.id}">
                            <span class="slider"></span>
                        </label>
                        <button class="btn btn-sm btn-danger" onclick="deleteMusic(${song.id})">
                            <i class="fas fa-trash"></i> Sil
                        </button>
                    </div>
                </div>
            `).join('');
           
            container.innerHTML = `<div class="item-list">${musicHTML}</div>`;
            const list = document.querySelector('#musicListContainer .item-list');
            if (list) {
                new Sortable(list, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: function (evt) {
                        saveMusicOrder();
                    }
                });
            }
        }
        // Generate pagination HTML
        function generatePaginationHTML(currentPage, totalPages, functionName) {
            let html = '';
           
            // Previous button
            if (currentPage > 1) {
                html += `<button class="pagination-btn" onclick="${functionName}(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i> Önceki
                </button>`;
            }
           
            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
           
            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="${functionName}(1)">1</button>`;
                if (startPage > 2) {
                    html += `<span class="pagination-info">...</span>`;
                }
            }
           
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `<button class="pagination-btn ${activeClass}" onclick="${functionName}(${i})">${i}</button>`;
            }
           
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="pagination-info">...</span>`;
                }
                html += `<button class="pagination-btn" onclick="${functionName}(${totalPages})">${totalPages}</button>`;
            }
           
            // Next button
            if (currentPage < totalPages) {
                html += `<button class="pagination-btn" onclick="${functionName}(${currentPage + 1})">
                    Sonraki <i class="fas fa-chevron-right"></i>
                </button>`;
            }
           
            // Page info
            html += `<div class="pagination-info">${currentPage} / ${totalPages} sayfa</div>`;
           
            return html;
        }
        // Settings form
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
           
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('action', 'update_settings');
           
            sendAjaxRequest(formData);
        });
        // Milestone form
        document.getElementById('milestoneForm').addEventListener('submit', function(e) {
            e.preventDefault();
           
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('action', 'add_milestone');
           
            sendAjaxRequest(formData, (data) => {
                if (data.data) {
                    const milestoneList = document.getElementById('milestoneList');
                    const newMilestone = document.createElement('div');
                    newMilestone.className = 'list-item';
                    newMilestone.setAttribute('data-id', data.data.id);
                   
                    const targetDate = new Date(data.data.target_date);
                    const formattedDate = targetDate.toLocaleDateString('tr-TR');
                   
                    newMilestone.innerHTML = `
                        <div class="item-header">
                            <div class="item-title">${data.data.title}</div>
                            <div class="item-actions">
                                <button class="btn btn-sm btn-danger" onclick="deleteMilestone(${data.data.id})">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </div>
                        </div>
                        <div class="item-content">
                            <p><strong>Tarih:</strong> ${formattedDate}</p>
                            <p><strong>Durum:</strong> ${data.data.completed ? 'Tamamlandı ✅' : 'Bekliyor ⏳'}</p>
                            ${data.data.note ? `<p><strong>Not:</strong> ${data.data.note}</p>` : ''}
                        </div>
                    `;
                    milestoneList.appendChild(newMilestone);
                    this.reset();
                }
            });
        });
        // Music form
        document.getElementById('musicForm').addEventListener('submit', function(e) {
            e.preventDefault();
           
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('action', 'upload_music');
           
            sendAjaxRequest(formData, (data) => {
                if (data.data) {
                    // Reload current page
                    loadMusicList();
                    this.reset();
                }
            });
        });
        // Gallery form
        document.getElementById('galleryForm').addEventListener('submit', function(e) {
            e.preventDefault();
           
            const formData = new FormData(this);
            formData.append('ajax', '1');
            formData.append('action', 'add_photo');
           
            sendAjaxRequest(formData, (data) => {
                if (data.data) {
                    // Reload current gallery page
                    loadGalleryPhotos(currentGalleryPage);
                    this.reset();
                    // Remove preview image if exists
                    const preview = this.querySelector('.preview-image');
                    if (preview) preview.remove();
                }
            });
        });
        // Delete functions
        function deleteMilestone(id) {
            if (!confirm('Bu kilometre taşını silmek istediğinizden emin misiniz?')) return;
           
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'delete_milestone');
            formData.append('id', id);
           
            sendAjaxRequest(formData, () => {
                const element = document.querySelector(`#milestoneList [data-id="${id}"]`);
                if (element) element.remove();
            });
        }
        function deleteMusic(id) {
            if (!confirm('Bu müziği silmek istediğinizden emin misiniz?')) return;
           
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'delete_music');
            formData.append('id', id);
           
            sendAjaxRequest(formData, () => {
                // Reload current page
                loadMusicList();
            });
        }
        function deletePhoto(id) {
            if (!confirm('Bu fotoğrafı silmek istediğinizden emin misiniz?')) return;
           
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'delete_photo');
            formData.append('id', id);
           
            sendAjaxRequest(formData, () => {
                // Reload current page
                loadGalleryPhotos(currentGalleryPage);
            });
        }
        // Music functions
        function previewMusic(musicId) {
            const audio = document.getElementById(`audio${musicId}`);
            const icon = document.getElementById(`playIcon${musicId}`);
           
            if (!audio || !icon) return;
           
            // Stop current playing music
            if (currentMusicPlaying && currentMusicPlaying !== musicId) {
                const currentAudio = document.getElementById(`audio${currentMusicPlaying}`);
                const currentIcon = document.getElementById(`playIcon${currentMusicPlaying}`);
                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio.currentTime = 0;
                }
                if (currentIcon) {
                    currentIcon.className = 'fas fa-play';
                }
            }
           
            if (audio.paused) {
                audio.play().then(() => {
                    icon.className = 'fas fa-pause';
                    currentMusicPlaying = musicId;
                }).catch(e => {
                    showAlert('Müzik çalınamadı: ' + e.message, 'error');
                });
            } else {
                audio.pause();
                icon.className = 'fas fa-play';
                currentMusicPlaying = null;
            }
           
            audio.addEventListener('ended', () => {
                icon.className = 'fas fa-play';
                currentMusicPlaying = null;
            });
        }
        function toggleMusic(id) {
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'toggle_music');
            formData.append('id', id);
           
            sendAjaxRequest(formData, (data) => {
                // Update current page display
                loadMusicList();
            });
        }
        function saveMusicOrder() {
            const items = document.querySelectorAll('#musicListContainer .music-item');
            const orders = Array.from(items).map((item, index) => ({
                id: parseInt(item.dataset.id),
                position: index + 1
            }));
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'update_music_order');
            formData.append('orders', JSON.stringify(orders));
            sendAjaxRequest(formData, () => {
                showAlert('Sıralama kaydedildi!', 'success');
            });
        }
        // File preview
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let preview = input.parentNode.querySelector('.preview-image');
                        if (!preview) {
                            preview = document.createElement('img');
                            preview.className = 'preview-image';
                            input.parentNode.appendChild(preview);
                        }
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set default dates to today
            const today = new Date().toISOString().split('T')[0];
            document.querySelectorAll('input[type="date"]').forEach(input => {
                if (!input.value) {
                    input.value = today;
                }
            });
            // Load initial data for active tab
            loadGalleryPhotos(1);
            loadMusicList();
        });
    </script>
</body>
</html>
