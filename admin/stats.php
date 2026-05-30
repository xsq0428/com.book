<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$pdo = getDbConnection();

$dateRange = $_GET['range'] ?? '7';
$startDate = date('Y-m-d', strtotime("-{$dateRange} days"));

ob_start();
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2><i class="bi bi-graph-up"></i> 访问统计</h2>
        <p class="text-muted mb-0">实时查看网站访问数据和点击统计</p>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-<?= $dateRange == 1 ? 'primary' : 'outline-primary' ?>" onclick="loadStats(1)">昨天</button>
        <button type="button" class="btn btn-sm btn-<?= $dateRange == 7 ? 'primary' : 'outline-primary' ?>" onclick="loadStats(7)">7 天</button>
        <button type="button" class="btn btn-sm btn-<?= $dateRange == 30 ? 'primary' : 'outline-primary' ?>" onclick="loadStats(30)">30 天</button>
        <button type="button" class="btn btn-sm btn-<?= $dateRange == 365 ? 'primary' : 'outline-primary' ?>" onclick="loadStats(365)">全年</button>
    </div>
</div>

<!-- 统计卡片 -->
<div id="statsCards">
<div class="row mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_logs WHERE visited_at >= ?");
            $stmt->execute([$startDate]);
            $totalVisits = $stmt->fetchColumn();
            ?>
            <div class="stat-number"><?= number_format($totalVisits) ?></div>
            <div class="stat-label"><i class="bi bi-eye"></i> 总访问次数</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM visit_logs WHERE visited_at >= ?");
            $stmt->execute([$startDate]);
            $uniqueVisitors = $stmt->fetchColumn();
            ?>
            <div class="stat-number"><?= number_format($uniqueVisitors) ?></div>
            <div class="stat-label"><i class="bi bi-people"></i> 独立访客</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <?php
            $stmt = $pdo->query("SELECT SUM(click_count) FROM urls");
            $totalClicks = $stmt->fetchColumn() ?: 0;
            ?>
            <div class="stat-number"><?= number_format($totalClicks) ?></div>
            <div class="stat-label"><i class="bi bi-cursor"></i> 网址点击总量</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_logs WHERE DATE(visited_at) = CURDATE()");
            $stmt->execute();
            $todayVisits = $stmt->fetchColumn();
            ?>
            <div class="stat-number"><?= number_format($todayVisits) ?></div>
            <div class="stat-label"><i class="bi bi-calendar-day"></i> 今日访问</div>
        </div>
    </div>
</div>
</div>

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-trophy"></i> 网址点击排行
            </div>
            <div class="card-body">
                <?php
                $stmt = $pdo->query("SELECT name, type, click_count FROM urls WHERE click_count > 0 ORDER BY click_count DESC LIMIT 10");
                $urls = $stmt->fetchAll();
                if (empty($urls)):
                ?>
                <p class="text-muted text-center py-4">暂无数据</p>
                <?php else: ?>
                <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th width="50">排名</th>
                            <th>名称</th>
                            <th width="80">类型</th>
                            <th width="100">点击量</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($urls as $url): 
                        ?>
                        <tr>
                            <td><span class="badge bg-<?= $rank <= 3 ? 'warning' : 'secondary' ?>"><?= $rank ?></span></td>
                            <td><?= sanitize($url['name']) ?></td>
                            <td><span class="badge bg-<?= $url['type'] === 'main' ? 'primary' : 'secondary' ?>"><?= $url['type'] === 'main' ? '主网址' : '备用' ?></span></td>
                            <td><?= number_format($url['click_count']) ?></td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> 最近访问记录
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php
                $stmt = $pdo->query("SELECT v.*, u.name as url_name 
                                    FROM visit_logs v 
                                    LEFT JOIN urls u ON v.url_id = u.id 
                                    ORDER BY v.visited_at DESC LIMIT 50");
                $logs = $stmt->fetchAll();
                if (empty($logs)):
                ?>
                <p class="text-muted text-center py-4">暂无数据</p>
                <?php else: ?>
                <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>时间</th>
                            <th>网址</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('m-d H:i', strtotime($log['visited_at'])) ?></td>
                            <td><?= $log['url_name'] ? sanitize($log['url_name']) : '首页' ?></td>
                            <td><code><?= sanitize($log['ip_address']) ?></code></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-calendar3"></i> 每日访问趋势
    </div>
    <div class="card-body">
        <div id="dailyStats">
        <?php
        $stmt = $pdo->prepare("
            SELECT DATE(visited_at) as date, COUNT(*) as count 
            FROM visit_logs 
            WHERE visited_at >= ? 
            GROUP BY DATE(visited_at) 
            ORDER BY date DESC
        ");
        $stmt->execute([$startDate]);
        $dailyStats = $stmt->fetchAll();
        ?>
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>日期</th>
                    <th>访问次数</th>
                    <th>独立访客</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dailyStats as $stat): ?>
                <?php
                $stmt2 = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM visit_logs WHERE DATE(visited_at) = ?");
                $stmt2->execute([$stat['date']]);
                $unique = $stmt2->fetchColumn();
                ?>
                <tr>
                    <td><?= date('Y-m-d (D)', strtotime($stat['date'])) ?></td>
                    <td><?= number_format($stat['count']) ?></td>
                    <td><?= number_format($unique) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        </div>
    </div>
</div>

<!-- 加载提示 -->
<div id="loadingOverlay" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 9999; text-align: center; padding-top: 200px;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">加载中...</p>
</div>

<script>
let currentRange = <?= $dateRange ?>;

function loadStats(range) {
    currentRange = range;
    document.getElementById('loadingOverlay').style.display = 'block';
    
    fetch('stats_data.php?range=' + range)
        .then(response => response.json())
        .then(data => {
            // 更新统计卡片
            document.getElementById('statsCards').innerHTML = data.cards;
            // 更新每日访问趋势
            document.getElementById('dailyStats').innerHTML = data.daily;
            // 更新按钮状态
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            event.target.classList.remove('btn-outline-primary');
            event.target.classList.add('btn-primary');
            document.getElementById('loadingOverlay').style.display = 'none';
        })
        .catch(err => {
            console.error('加载失败:', err);
            document.getElementById('loadingOverlay').style.display = 'none';
            alert('加载统计数据失败');
        });
}
</script>
<?php
$content = ob_get_clean();
require 'layout.php';
?>
