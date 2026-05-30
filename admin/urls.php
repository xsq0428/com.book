<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$action = $_GET['action'] ?? 'list';
$pdo = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($action) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO urls (name, url, type, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['url'],
                    $_POST['type'],
                    $_POST['icon'] ?? '🔗',
                    (int)$_POST['sort_order'],
                    isset($_POST['is_active']) ? 1 : 0
                ]);
                setFlashMessage('success', '网址添加成功');
                redirect('urls.php');
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE urls SET name=?, url=?, type=?, icon=?, sort_order=?, is_active=? WHERE id=?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['url'],
                    $_POST['type'],
                    $_POST['icon'] ?? '🔗',
                    (int)$_POST['sort_order'],
                    isset($_POST['is_active']) ? 1 : 0,
                    (int)$_POST['id']
                ]);
                setFlashMessage('success', '网址修改成功');
                redirect('urls.php');
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM urls WHERE id=?");
                $stmt->execute([(int)$_POST['id']]);
                setFlashMessage('success', '网址删除成功');
                redirect('urls.php');
                break;
        }
    } catch (PDOException $e) {
        setFlashMessage('danger', '操作失败：' . $e->getMessage());
        redirect('urls.php');
    }
}

ob_start();
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-link-45deg"></i> 网址管理</h2>
        <p class="text-muted mb-0">管理主网址和备用网址</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#urlModal">
        <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">添加网址</span><span class="d-sm-none">添加</span>
    </button>
</div>

<div class="card">
    <div class="card-body">
        <!-- 桌面端表格 -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>排序</th>
                        <th>图标</th>
                        <th>名称</th>
                        <th>URL</th>
                        <th>类型</th>
                        <th>点击量</th>
                        <th>状态</th>
                        <th width="120">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM urls ORDER BY sort_order");
                    while ($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><?= $row['sort_order'] ?></td>
                        <td><?= sanitize($row['icon']) ?></td>
                        <td><?= sanitize($row['name']) ?></td>
                        <td><a href="<?= sanitize($row['url']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;"><?= sanitize($row['url']) ?></a></td>
                        <td><span class="badge bg-<?= $row['type'] === 'main' ? 'primary' : 'secondary' ?>"><?= $row['type'] === 'main' ? '主网址' : '备用' ?></span></td>
                        <td><?= number_format($row['click_count']) ?></td>
                        <td><span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>"><?= $row['is_active'] ? '启用' : '禁用' ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= sanitize($row['name']) ?>"
                                data-url="<?= sanitize($row['url']) ?>"
                                data-type="<?= $row['type'] ?>"
                                data-icon="<?= sanitize($row['icon']) ?>"
                                data-sort="<?= $row['sort_order'] ?>"
                                data-active="<?= $row['is_active'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUrl(<?= $row['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 移动端卡片列表 -->
        <div class="d-md-none">
            <?php
            $stmt = $pdo->query("SELECT * FROM urls ORDER BY sort_order");
            while ($row = $stmt->fetch()):
            ?>
            <div class="card mb-3 border shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><?= sanitize($row['icon']) ?> <?= sanitize($row['name']) ?></h6>
                        <span class="badge bg-<?= $row['type'] === 'main' ? 'primary' : 'secondary' ?>"><?= $row['type'] === 'main' ? '主网址' : '备用' ?></span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">URL:</small><br>
                        <a href="<?= sanitize($row['url']) ?>" target="_blank" class="text-break"><?= sanitize($row['url']) ?></a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?> me-2"><?= $row['is_active'] ? '启用' : '禁用' ?></span>
                            <small class="text-muted">📊 <?= number_format($row['click_count']) ?> 点击</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary edit-btn-mobile" 
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= sanitize($row['name']) ?>"
                                data-url="<?= sanitize($row['url']) ?>"
                                data-type="<?= $row['type'] ?>"
                                data-icon="<?= sanitize($row['icon']) ?>"
                                data-sort="<?= $row['sort_order'] ?>"
                                data-active="<?= $row['is_active'] ?>">
                                <i class="bi bi-pencil"></i> 编辑
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUrl(<?= $row['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="urlModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="urls.php?action=<?= $action ?>">
                <input type="hidden" name="id" id="urlId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">添加网址</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">名称</label>
                        <input type="text" class="form-control" name="name" id="urlName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" name="url" id="urlUrl" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">图标</label>
                        <input type="text" class="form-control" name="icon" id="urlIcon" value="🔗">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">类型</label>
                        <select class="form-select" name="type" id="urlType">
                            <option value="backup">备用网址</option>
                            <option value="main">主网址</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="sort_order" id="urlSort" value="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="urlActive" checked>
                        <label class="form-check-label">启用</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form method="POST" action="urls.php?action=delete" id="deleteForm">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
const urlModal = new bootstrap.Modal(document.getElementById('urlModal'));
const modalTitle = document.getElementById('modalTitle');
const urlId = document.getElementById('urlId');

// 桌面端编辑按钮
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        modalTitle.textContent = '修改网址';
        urlId.value = this.dataset.id;
        document.getElementById('urlName').value = this.dataset.name;
        document.getElementById('urlUrl').value = this.dataset.url;
        document.getElementById('urlType').value = this.dataset.type;
        document.getElementById('urlIcon').value = this.dataset.icon;
        document.getElementById('urlSort').value = this.dataset.sort;
        document.getElementById('urlActive').checked = this.dataset.active == 1;
        urlModal.show();
    });
});

// 移动端编辑按钮
document.querySelectorAll('.edit-btn-mobile').forEach(btn => {
    btn.addEventListener('click', function() {
        modalTitle.textContent = '修改网址';
        urlId.value = this.dataset.id;
        document.getElementById('urlName').value = this.dataset.name;
        document.getElementById('urlUrl').value = this.dataset.url;
        document.getElementById('urlType').value = this.dataset.type;
        document.getElementById('urlIcon').value = this.dataset.icon;
        document.getElementById('urlSort').value = this.dataset.sort;
        document.getElementById('urlActive').checked = this.dataset.active == 1;
        urlModal.show();
    });
});

function deleteUrl(id) {
    if (confirm('确定删除此网址？')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?php
$content = ob_get_clean();
require 'layout.php';
?>
