<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$pdo = getDbConnection();

// 保存配色设置
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme_color'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, description) 
                               VALUES ('theme_color', ?, 'text', '后台主题颜色') 
                               ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$_POST['theme_color'], $_POST['theme_color']]);
        setFlashMessage('success', '主题颜色已切换');
        redirect('theme.php');
    } catch (PDOException $e) {
        setFlashMessage('danger', '保存失败：' . $e->getMessage());
    }
}

// 获取当前配色
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'theme_color'");
$stmt->execute();
$currentTheme = $stmt->fetchColumn() ?: 'purple';

// 主题配色方案
$themes = [
    'purple' => [
        'name' => '浪漫紫色',
        'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'primary' => '#667eea',
        'secondary' => '#764ba2',
        'active' => $currentTheme === 'purple'
    ],
    'blue' => [
        'name' => '深邃蓝色',
        'gradient' => 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
        'primary' => '#1e3c72',
        'secondary' => '#2a5298',
        'active' => $currentTheme === 'blue'
    ],
    'green' => [
        'name' => '清新绿色',
        'gradient' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
        'primary' => '#11998e',
        'secondary' => '#38ef7d',
        'active' => $currentTheme === 'green'
    ],
    'orange' => [
        'name' => '活力橙色',
        'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'primary' => '#f093fb',
        'secondary' => '#f5576c',
        'active' => $currentTheme === 'orange'
    ],
    'pink' => [
        'name' => '甜美粉色',
        'gradient' => 'linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%)',
        'primary' => '#ff758c',
        'secondary' => '#ff7eb3',
        'active' => $currentTheme === 'pink'
    ],
    'dark' => [
        'name' => '经典黑色',
        'gradient' => 'linear-gradient(135deg, #232526 0%, #414345 100%)',
        'primary' => '#232526',
        'secondary' => '#414345',
        'active' => $currentTheme === 'dark'
    ],
    'cyan' => [
        'name' => '青色海洋',
        'gradient' => 'linear-gradient(135deg, #06beb6 0%, #48b1bf 100%)',
        'primary' => '#06beb6',
        'secondary' => '#48b1bf',
        'active' => $currentTheme === 'cyan'
    ],
    'sunset' => [
        'name' => '夕阳红霞',
        'gradient' => 'linear-gradient(135deg, #ff512f 0%, #dd2476 100%)',
        'primary' => '#ff512f',
        'secondary' => '#dd2476',
        'active' => $currentTheme === 'sunset'
    ]
];

ob_start();
?>
<div class="page-header mb-4">
    <h2><i class="bi bi-palette"></i> 主题配色</h2>
    <p class="text-muted mb-0">自定义后台管理系统的主题颜色</p>
</div>

<?php if ($msg = getFlashMessage()): ?>
    <div class="alert alert-<?= $msg['type'] ?> alert-dismissible fade show">
        <i class="bi bi-<?= $msg['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
        <?= $msg['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-brush"></i> 选择主题颜色
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <?php foreach ($themes as $key => $theme): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <form method="POST" action="theme.php">
                            <input type="hidden" name="theme_color" value="<?= $key ?>">
                            <div class="card theme-card h-100" style="cursor: pointer;" onclick="this.querySelector('form').submit()">
                                <div class="card-body text-center position-relative">
                                    <?php if ($theme['active']): ?>
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                        <i class="bi bi-check-circle-fill"></i> 当前
                                    </span>
                                    <?php endif; ?>
                                    
                                    <div class="theme-preview mb-3" style="background: <?= $theme['gradient'] ?>; height: 80px; border-radius: 8px;"></div>
                                    
                                    <h6 class="mb-1"><?= $theme['name'] ?></h6>
                                    <small class="text-muted" style="font-size: 11px;">
                                        <?= $theme['primary'] ?> → <?= $theme['secondary'] ?>
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 预览效果 -->
<div class="row mt-4">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-eye"></i> 导航栏预览
            </div>
            <div class="card-body">
                <div class="top-navbar-preview mb-3" style="background: <?= $themes[$currentTheme]['gradient'] ?>; padding: 12px; border-radius: 8px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600;">🌸 二次元地址发布系统</span>
                        <span style="font-size: 13px;">👤 admin</span>
                    </div>
                </div>
                <p class="text-muted small mb-0">这是顶部导航栏的预览效果</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-menu-button"></i> 侧边栏预览
            </div>
            <div class="card-body">
                <div class="sidebar-preview" style="background: <?= $themes[$currentTheme]['gradient'] ?>; padding: 15px; border-radius: 8px; color: white; width: 100%;">
                    <div style="padding: 10px; opacity: 0.9; border-left: 3px solid transparent;">
                        <i class="bi bi-speedometer2"></i> 管理首页
                    </div>
                    <div style="padding: 10px; background: rgba(255,255,255,0.2); border-left: 3px solid white; margin-top: 5px;">
                        <i class="bi bi-palette"></i> 主题配色
                    </div>
                    <div style="padding: 10px; opacity: 0.9; border-left: 3px solid transparent;">
                        <i class="bi bi-link-45deg"></i> 网址管理
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">这是侧边栏菜单的预览效果</p>
            </div>
        </div>
    </div>
</div>

<!-- 按钮样式预览 -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-ui-checks"></i> 按钮样式预览
            </div>
            <div class="card-body">
                <div class="btn-preview" style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <button class="btn me-2 mb-2" style="background: <?= $themes[$currentTheme]['gradient'] ?>; color: white; border: none;">
                        <i class="bi bi-plus-circle"></i> 主要按钮
                    </button>
                    <button class="btn btn-outline-primary me-2 mb-2">
                        <i class="bi bi-pencil"></i> 次要按钮
                    </button>
                    <button class="btn btn-success me-2 mb-2">
                        <i class="bi bi-check-circle"></i> 成功按钮
                    </button>
                    <button class="btn btn-danger me-2 mb-2">
                        <i class="bi bi-trash"></i> 危险按钮
                    </button>
                    <button class="btn btn-warning me-2 mb-2">
                        <i class="bi bi-exclamation-circle"></i> 警告按钮
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.theme-card {
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.theme-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: <?= $themes[$currentTheme]['primary'] ?>;
}

.theme-preview {
    transition: all 0.3s ease;
}

.theme-card:hover .theme-preview {
    transform: scale(1.05);
}

.top-navbar-preview,
.sidebar-preview {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-preview .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
<?php
$content = ob_get_clean();
require 'layout.php';
?>
