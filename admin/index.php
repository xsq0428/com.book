<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

try {
    $pdo = getDbConnection();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM urls");
    $urlCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM ads WHERE is_active = 1");
    $activeAds = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT SUM(click_count) FROM urls");
    $totalClicks = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM visit_logs WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $weeklyVisits = $stmt->fetchColumn();
    
    ob_start();
    ?>
    <div class="mb-4">
        <h2><i class="bi bi-speedometer2"></i> 管理首页</h2>
        <p class="text-muted">欢迎回来，<?= getCurrentAdmin()['username'] ?>！</p>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number"><?= $urlCount ?></div>
                <div class="stat-label">网址总数</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number"><?= $activeAds ?></div>
                <div class="stat-label">活跃广告</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number"><?= number_format($totalClicks) ?></div>
                <div class="stat-label">总点击量</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number"><?= number_format($weeklyVisits) ?></div>
                <div class="stat-label">本周访问</div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-link-45deg"></i> 网址快速统计
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("SELECT name, click_count FROM urls ORDER BY click_count DESC LIMIT 5");
                    $urls = $stmt->fetchAll();
                    foreach ($urls as $url):
                    ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span><?= sanitize($url['name']) ?></span>
                        <span class="text-muted"><?= number_format($url['click_count']) ?> 次点击</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> 最近访问
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("SELECT v.visited_at, u.name, v.ip_address 
                                        FROM visit_logs v 
                                        LEFT JOIN urls u ON v.url_id = u.id 
                                        ORDER BY v.visited_at DESC LIMIT 5");
                    $logs = $stmt->fetchAll();
                    foreach ($logs as $log):
                    ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span><?= $log['name'] ? sanitize($log['name']) : '未知页面' ?></span>
                        <span class="text-muted"><?= date('m-d H:i', strtotime($log['visited_at'])) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require 'layout.php';
} catch (PDOException $e) {
    die("错误：" . $e->getMessage());
}
