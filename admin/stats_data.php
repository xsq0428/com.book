<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

$pdo = getDbConnection();
$range = isset($_GET['range']) ? (int)$_GET['range'] : 7;
$startDate = date('Y-m-d', strtotime("-{$range} days"));

try {
    // 统计卡片 HTML
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_logs WHERE visited_at >= ?");
    $stmt->execute([$startDate]);
    $totalVisits = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM visit_logs WHERE visited_at >= ?");
    $stmt->execute([$startDate]);
    $uniqueVisitors = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT SUM(click_count) FROM urls");
    $totalClicks = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_logs WHERE DATE(visited_at) = CURDATE()");
    $stmt->execute();
    $todayVisits = $stmt->fetchColumn();
    
    $cardsHtml = '
    <div class="row mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card">
                <div class="stat-number">'.number_format($totalVisits).'</div>
                <div class="stat-label"><i class="bi bi-eye"></i> 总访问次数</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card">
                <div class="stat-number">'.number_format($uniqueVisitors).'</div>
                <div class="stat-label"><i class="bi bi-people"></i> 独立访客</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card">
                <div class="stat-number">'.number_format($totalClicks).'</div>
                <div class="stat-label"><i class="bi bi-cursor"></i> 网址点击总量</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card">
                <div class="stat-number">'.number_format($todayVisits).'</div>
                <div class="stat-label"><i class="bi bi-calendar-day"></i> 今日访问</div>
            </div>
        </div>
    </div>';
    
    // 每日访问趋势 HTML
    $stmt = $pdo->prepare("
        SELECT DATE(visited_at) as date, COUNT(*) as count 
        FROM visit_logs 
        WHERE visited_at >= ? 
        GROUP BY DATE(visited_at) 
        ORDER BY date DESC
    ");
    $stmt->execute([$startDate]);
    $dailyStats = $stmt->fetchAll();
    
    $dailyHtml = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>日期</th><th>访问次数</th><th>独立访客</th></tr></thead><tbody>';
    
    foreach ($dailyStats as $stat) {
        $stmt2 = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM visit_logs WHERE DATE(visited_at) = ?");
        $stmt2->execute([$stat['date']]);
        $unique = $stmt2->fetchColumn();
        
        $dailyHtml .= '<tr>
            <td>'.date('Y-m-d (D)', strtotime($stat['date'])).'</td>
            <td>'.number_format($stat['count']).'</td>
            <td>'.number_format($unique).'</td>
        </tr>';
    }
    
    $dailyHtml .= '</tbody></table></div>';
    
    echo json_encode([
        'success' => true,
        'cards' => $cardsHtml,
        'daily' => $dailyHtml
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
