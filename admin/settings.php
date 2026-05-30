<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$pdo = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?");
            $stmt->execute([$value, $key]);
        }
        setFlashMessage('success', '设置保存成功');
        redirect('settings.php');
    } catch (PDOException $e) {
        setFlashMessage('danger', '保存失败：' . $e->getMessage());
    }
}

ob_start();
?>
<h2 class="mb-4"><i class="bi bi-gear"></i> 系统设置</h2>

<form method="POST" action="">
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-house"></i> 站点基本信息
        </div>
        <div class="card-body">
            <?php
            $stmt = $pdo->query("SELECT * FROM settings ORDER BY FIELD(setting_key, 'site_title', 'site_subtitle', 'site_logo')");
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row;
            }
            ?>
            
            <div class="mb-3">
                <label class="form-label">网站标题</label>
                <input type="text" class="form-control" name="settings[site_title]" 
                       value="<?= sanitize($settings['site_title']['setting_value'] ?? '') ?>" required>
                <div class="form-text"><?= sanitize($settings['site_title']['description'] ?? '') ?></div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">网站副标题</label>
                <input type="text" class="form-control" name="settings[site_subtitle]" 
                       value="<?= sanitize($settings['site_subtitle']['setting_value'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">网站 Logo URL</label>
                <input type="url" class="form-control" name="settings[site_logo]" 
                       value="<?= sanitize($settings['site_logo']['setting_value'] ?? '') ?>">
                <div class="mt-2">
                    <img src="<?= sanitize($settings['site_logo']['setting_value'] ?? '') ?>" 
                         alt="Logo" style="max-height: 100px; border-radius: 50%;">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-people"></i> 群组链接设置
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">群组链接按钮文字</label>
                <input type="text" class="form-control" name="settings[group_link_text]" 
                       value="<?= sanitize($settings['group_link_text']['setting_value'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">群组链接地址</label>
                <input type="url" class="form-control" name="settings[group_link_url]" 
                       value="<?= sanitize($settings['group_link_url']['setting_value'] ?? '') ?>">
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-link-45deg"></i> 永久地址设置
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">永久地址</label>
                <input type="text" class="form-control" name="settings[permanent_url]" 
                       value="<?= sanitize($settings['permanent_url']['setting_value'] ?? '') ?>">
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-file-text"></i> 页脚设置
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">版权信息</label>
                <input type="text" class="form-control" name="settings[footer_text]" 
                       value="<?= sanitize($settings['footer_text']['setting_value'] ?? '') ?>">
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> 保存设置
    </button>
</form>
<?php
$content = ob_get_clean();
require 'layout.php';
?>
