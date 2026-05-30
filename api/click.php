<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$urlId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ipAddress = getClientIp();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';

try {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("INSERT INTO visit_logs (url_id, ip_address, user_agent, referer) VALUES (?, ?, ?, ?)");
    $stmt->execute([$urlId, $ipAddress, $userAgent, $referer]);
    
    if ($urlId > 0) {
        $stmt = $pdo->prepare("UPDATE urls SET click_count = click_count + 1 WHERE id = ?");
        $stmt->execute([$urlId]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
