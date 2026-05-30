<?php
/**
 * 前台主题切换 API
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['theme_color'])) {
        $theme = $input['theme_color'];
        $validThemes = ['pink', 'purple', 'blue', 'green', 'orange', 'dark', 'cyan', 'sunset'];
        
        if (in_array($theme, $validThemes)) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, description) 
                                   VALUES ('theme_color', ?, 'text', '前台主题颜色') 
                                   ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$theme, $theme]);
            
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => '无效的主题']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '缺少主题参数']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
