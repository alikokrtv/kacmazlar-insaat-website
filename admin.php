<?php
/**
 * Kacmazlar İnşaat CMS - Admin Panel Ana Sayfası
 */

require_once 'includes/Database.php';
require_once 'includes/Auth.php';

$auth = new Auth();

// Giriş kontrolü
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$admin = $auth->getCurrentAdmin();
$db = DatabaseSingleton::getInstance();

// İstatistikleri getir
$stats = [
    'projects' => $db->fetch("SELECT COUNT(*) as count FROM projects WHERE is_published = 1")['count'],
    'blog_posts' => $db->fetch("SELECT COUNT(*) as count FROM blog_posts WHERE is_published = 1")['count'],
    'messages' => $db->fetch("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'],
    'total_views' => $db->fetch("SELECT SUM(view_count) as total FROM blog_posts")['total'] ?? 0
];

// Son projeler
$recentProjects = $db->fetchAll("
    SELECT id, title, status, created_at 
    FROM projects 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Son blog yazıları
$recentPosts = $db->fetchAll("
    SELECT bp.id, bp.title, bp.view_count, bp.published_at, bc.name as category_name
    FROM blog_posts bp
    LEFT JOIN blog_categories bc ON bp.category_id = bc.id
    ORDER BY bp.created_at DESC 
    LIMIT 5
");

// Son mesajlar
$recentMessages = $db->fetchAll("
    SELECT id, name, email, subject, created_at, is_read
    FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kacmazlar İnşaat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #004E89, #FF6B35);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }

        .sidebar-header h2 {
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar-header p {
            opacity: 0.8;
            font-size: 14px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.1);
            border-left-color: white;
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #004E89;
            font-size: 28px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #004E89, #FF6B35);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.projects { background: linear-gradient(135deg, #004E89, #0066cc); }
        .stat-icon.blog { background: linear-gradient(135deg, #28a745, #20c997); }
        .stat-icon.messages { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .stat-icon.views { background: linear-gradient(135deg, #6f42c1, #e83e8c); }

        .stat-content h3 {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #666;
            font-size: 14px;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            color: #004E89;
            font-size: 18px;
        }

        .card-header a {
            color: #FF6B35;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .card-content {
            padding: 20px;
        }

        .list-item {
            padding: 15px 0;
            border-bottom: 1px solid #f1f3f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item-info h4 {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .list-item-info p {
            font-size: 12px;
            color: #666;
        }

        .list-item-meta {
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-planlama { background: #fff3cd; color: #856404; }
        .status-devam { background: #d1ecf1; color: #0c5460; }
        .status-tamamlandi { background: #d4edda; color: #155724; }

        .unread-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Kacmazlar İnşaat CMS</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin-projects.php"><i class="fas fa-building"></i> Projeler</a></li>
                <li><a href="admin-blog.php"><i class="fas fa-blog"></i> Blog Yazıları</a></li>
                <li><a href="admin-messages.php"><i class="fas fa-envelope"></i> Mesajlar</a></li>
                <li><a href="admin-media.php"><i class="fas fa-images"></i> Medya</a></li>
                <li><a href="admin-settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Kullanıcılar</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($admin['full_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?= htmlspecialchars($admin['full_name']) ?></div>
                        <div style="font-size: 12px; color: #666;"><?= ucfirst($admin['role']) ?></div>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Çıkış
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon projects">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['projects'] ?></h3>
                        <p>Aktif Proje</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blog">
                        <i class="fas fa-blog"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['blog_posts'] ?></h3>
                        <p>Blog Yazısı</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon messages">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['messages'] ?></h3>
                        <p>Okunmamış Mesaj</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon views">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($stats['total_views']) ?></h3>
                        <p>Toplam Görüntülenme</p>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Son Projeler -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Son Projeler</h3>
                        <a href="admin-projects.php">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="card-content">
                        <?php foreach ($recentProjects as $project): ?>
                        <div class="list-item">
                            <div class="list-item-info">
                                <h4><?= htmlspecialchars($project['title']) ?></h4>
                                <p><?= date('d.m.Y', strtotime($project['created_at'])) ?></p>
                            </div>
                            <div class="list-item-meta">
                                <span class="status-badge status-<?= $project['status'] ?>">
                                    <?= ucfirst($project['status']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Son Blog Yazıları -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Son Blog Yazıları</h3>
                        <a href="admin-blog.php">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="card-content">
                        <?php foreach ($recentPosts as $post): ?>
                        <div class="list-item">
                            <div class="list-item-info">
                                <h4><?= htmlspecialchars($post['title']) ?></h4>
                                <p><?= $post['category_name'] ?> • <?= date('d.m.Y', strtotime($post['published_at'])) ?></p>
                            </div>
                            <div class="list-item-meta">
                                <i class="fas fa-eye"></i> <?= $post['view_count'] ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Son Mesajlar -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Son Mesajlar</h3>
                        <a href="admin-messages.php">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="card-content">
                        <?php foreach ($recentMessages as $message): ?>
                        <div class="list-item">
                            <div class="list-item-info">
                                <h4><?= htmlspecialchars($message['name']) ?></h4>
                                <p><?= htmlspecialchars($message['subject']) ?> • <?= date('d.m.Y H:i', strtotime($message['created_at'])) ?></p>
                            </div>
                            <div class="list-item-meta">
                                <?php if (!$message['is_read']): ?>
                                <span class="unread-badge">!</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }

        // Auto refresh stats every 30 seconds
        setInterval(function() {
            // AJAX ile stats güncelleme (isteğe bağlı)
        }, 30000);
    </script>
</body>
</html>
