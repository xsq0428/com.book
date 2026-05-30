<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>后台管理 - 二次元地址发布系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 280px;
            <?php
            // 获取主题颜色配置
            require_once '../config/database.php';
            try {
                $pdo = getDbConnection();
                $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'theme_color'");
                $stmt->execute();
                $themeColor = $stmt->fetchColumn() ?: 'purple';
                
                $themeColors = [
                    'purple' => ['#667eea', '#764ba2'],
                    'blue' => ['#1e3c72', '#2a5298'],
                    'green' => ['#11998e', '#38ef7d'],
                    'orange' => ['#f5576c', '#f093fb'],
                    'pink' => ['#ff758c', '#ff7eb3'],
                    'dark' => ['#232526', '#414345'],
                    'cyan' => ['#06beb6', '#48b1bf'],
                    'sunset' => ['#ff512f', '#dd2476']
                ];
                
                $colors = $themeColors[$themeColor] ?? $themeColors['purple'];
                echo "--theme-primary: {$colors[0]};\n";
                echo "--theme-secondary: {$colors[1]};";
            } catch (PDOException $e) {
                echo "--theme-primary: #667eea;\n--theme-secondary: #764ba2;";
            }
            ?>
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            background: #f5f7fa;
            overflow-x: hidden;
            font-size: 14px;
        }
        
        /* 顶部导航栏 */
        .top-navbar {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* 侧边栏 */
        .sidebar {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            min-height: 100vh;
            padding-top: 70px;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            transition: transform 0.3s ease, background 0.3s ease;
            z-index: 1020;
            overflow-y: auto;
        }
        
        /* 按钮 */
        .btn-primary {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%);
        }
        
        .badge.bg-primary {
            background-color: var(--theme-primary) !important;
        }
        
        .navbar-brand {
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        
        .hamburger-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            display: none;
            margin-right: 10px;
        }
        
        /* 侧边栏 */
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 60px;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            transition: transform 0.3s ease;
            z-index: 1020;
            overflow-y: auto;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .sidebar-menu a {
            color: rgba(255,255,255,0.85);
            padding: 14px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }
        
        .sidebar-menu i {
            font-size: 18px;
            width: 24px;
        }
        
        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 15px 25px;
        }
        
        .logout-btn {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 14px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #ff6b6b;
        }
        
        /* 主内容区 */
        .main-content {
            padding: 75px 15px 20px;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        /* 页面标题 */
        .page-header {
            margin-bottom: 20px;
        }
        
        .page-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .page-header p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* 卡片 */
        .card {
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border-radius: 12px;
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .card-body {
            padding: 15px 20px;
        }
        
        /* 统计卡片 */
        .stat-card {
            text-align: center;
            padding: 20px 15px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
            font-size: 13px;
        }
        
        /* 遮罩层 */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1015;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        /* 响应式表格 */
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            margin-bottom: 0;
            min-width: 800px;
        }
        
        .table th {
            font-weight: 600;
            color: #555;
            font-size: 13px;
            padding: 12px 16px;
            white-space: nowrap;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table td {
            padding: 12px 16px;
            vertical-align: middle;
            font-size: 13px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table a {
            word-break: break-all;
        }
        
        /* 按钮 */
        .btn {
            border-radius: 8px;
            font-size: 13px;
            padding: 8px 16px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        }
        
        /* 警告框 */
        .alert {
            border: none;
            border-radius: 10px;
            font-size: 13px;
            padding: 12px 15px;
        }
        
        /* 模态框 */
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        
        .modal-header {
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 15px 20px;
        }
        
        .form-control, .form-select {
            font-size: 14px;
            padding: 10px 12px;
            border-radius: 8px;
        }
        
        .form-label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        /* 用户信息 */
        .user-info {
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            white-space: nowrap;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* 移动端优化 */
        @media (max-width: 991px) {
            .hamburger-btn {
                display: block;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 75px 10px 15px;
            }
            
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
            
            .navbar-brand span {
                display: none;
            }
            
            .user-info span {
                display: none;
            }
            
            .stat-number {
                font-size: 24px;
            }
            
            .page-header h2 {
                font-size: 18px;
            }
            
            .card-header {
                padding: 12px 15px;
                font-size: 15px;
            }
            
            .card-body {
                padding: 12px 15px;
            }
            
            .table {
                min-width: 700px;
                font-size: 12px;
            }
            
            .table th, .table td {
                padding: 10px 12px;
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
        
        /* 超小屏幕 */
        @media (max-width: 576px) {
            .top-navbar {
                height: 56px;
                padding: 10px 12px;
            }
            
            .navbar-brand {
                font-size: 14px;
            }
            
            .navbar-brand i {
                font-size: 18px;
            }
            
            .main-content {
                padding: 70px 10px 10px;
            }
            
            .stat-card {
                padding: 15px 12px;
            }
            
            .stat-number {
                font-size: 22px;
            }
            
            .table {
                min-width: 650px;
                font-size: 11px;
            }
            
            .table th, .table td {
                padding: 8px 10px;
            }
        }
        
        /* 徽章 */
        .badge {
            font-size: 11px;
            padding: 4px 8px;
            font-weight: 500;
        }
        
        /* 栅格系统优化 */
        @media (max-width: 768px) {
            .col-md-3, .col-md-6 {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- 顶部导航栏 -->
    <nav class="top-navbar">
        <div style="display: flex; align-items: center;">
            <button class="hamburger-btn" id="hamburgerBtn">
                <i class="bi bi-list"></i>
            </button>
            <a href="index.php" class="navbar-brand">
                <i class="bi bi-flower1"></i>
                <span>二次元地址发布系统</span>
            </a>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person"></i>
            </div>
            <span><?= getCurrentAdmin()['username'] ?></span>
        </div>
    </nav>
    
    <!-- 遮罩层 -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- 侧边栏 -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>管理首页</span>
            </a>
            <a href="urls.php" class="<?= basename($_SERVER['PHP_SELF']) == 'urls.php' ? 'active' : '' ?>">
                <i class="bi bi-link-45deg"></i>
                <span>网址管理</span>
            </a>
            <a href="ads.php" class="<?= basename($_SERVER['PHP_SELF']) == 'ads.php' ? 'active' : '' ?>">
                <i class="bi bi-megaphone"></i>
                <span>广告管理</span>
            </a>
            <a href="announcements.php" class="<?= basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : '' ?>">
                <i class="bi bi-bell"></i>
                <span>公告管理</span>
            </a>
            <div class="sidebar-divider"></div>
            <a href="theme.php" class="<?= basename($_SERVER['PHP_SELF']) == 'theme.php' ? 'active' : '' ?>">
                <i class="bi bi-palette"></i>
                <span>主题配色</span>
            </a>
            <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                <i class="bi bi-gear"></i>
                <span>系统设置</span>
            </a>
            <a href="stats.php" class="<?= basename($_SERVER['PHP_SELF']) == 'stats.php' ? 'active' : '' ?>">
                <i class="bi bi-graph-up"></i>
                <span>访问统计</span>
            </a>
            <div class="sidebar-divider"></div>
            <a href="../" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>查看前台</span>
            </a>
            <a href="logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>退出登录</span>
            </a>
        </div>
    </aside>
    
    <!-- 主内容区 -->
    <main class="main-content">
        <?php if ($msg = getFlashMessage()): ?>
            <div class="alert alert-<?= $msg['type'] ?> alert-dismissible fade show">
                <i class="bi bi-<?= $msg['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                <?= $msg['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php echo $content ?? ''; ?>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        
        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        hamburgerBtn.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);
        
        // 点击菜单项后关闭侧边栏（移动端）
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });
        
        // 窗口大小变化时重置侧边栏
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
