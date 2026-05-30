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
                $stmt = $pdo->prepare("INSERT INTO announcements (type, icon, content, is_active, sort_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['type'],
                    $_POST['icon'] ?? '📌',
                    $_POST['content'],
                    isset($_POST['is_active']) ? 1 : 0,
                    (int)$_POST['sort_order']
                ]);
                setFlashMessage('success', '公告添加成功');
                redirect('announcements.php');
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE announcements SET type=?, icon=?, content=?, is_active=?, sort_order=? WHERE id=?");
                $stmt->execute([
                    $_POST['type'],
                    $_POST['icon'] ?? '📌',
                    $_POST['content'],
                    isset($_POST['is_active']) ? 1 : 0,
                    (int)$_POST['sort_order'],
                    (int)$_POST['id']
                ]);
                setFlashMessage('success', '公告修改成功');
                redirect('announcements.php');
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM announcements WHERE id=?");
                $stmt->execute([(int)$_POST['id']]);
                setFlashMessage('success', '公告删除成功');
                redirect('announcements.php');
                break;
        }
    } catch (PDOException $e) {
        setFlashMessage('danger', '操作失败：' . $e->getMessage());
        redirect('announcements.php');
    }
}

ob_start();
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-bell"></i> 公告管理</h2>
        <p class="text-muted mb-0">管理收藏提示、联系信息和公告</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal">
        <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">添加公告</span><span class="d-sm-none">添加</span>
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>排序</th>
                    <th>类型</th>
                    <th>图标</th>
                    <th>内容</th>
                    <th>状态</th>
                    <th width="150">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM announcements ORDER BY sort_order");
                while ($row = $stmt->fetch()):
                ?>
                <tr>
                    <td><?= $row['sort_order'] ?></td>
                    <td>
                        <?php
                        $types = ['bookmark' => '收藏提示', 'contact' => '联系信息', 'notice' => '公告'];
                        echo $types[$row['type']] ?? $row['type'];
                        ?>
                    </td>
                    <td><?= sanitize($row['icon']) ?></td>
                    <td><?= sanitize($row['content']) ?></td>
                    <td><span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>"><?= $row['is_active'] ? '启用' : '禁用' ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" 
                            data-id="<?= $row['id'] ?>"
                            data-type="<?= $row['type'] ?>"
                            data-icon="<?= sanitize($row['icon']) ?>"
                            data-content="<?= sanitize($row['content']) ?>"
                            data-sort="<?= $row['sort_order'] ?>"
                            data-active="<?= $row['is_active'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAnnouncement(<?= $row['id'] ?>)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="announcementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="announcements.php?action=<?= $action ?>">
                <input type="hidden" name="id" id="announcementId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">添加公告</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">类型</label>
                        <select class="form-select" name="type" id="announcementType">
                            <option value="bookmark">收藏提示</option>
                            <option value="contact">联系信息</option>
                            <option value="notice">公告</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">图标</label>
                        <input type="text" class="form-control" name="icon" id="announcementIcon" value="📌">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">内容</label>
                        <textarea class="form-control" name="content" id="announcementContent" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="sort_order" id="announcementSort" value="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="announcementActive" checked>
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

<form method="POST" action="announcements.php?action=delete" id="deleteForm">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
const announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
const modalTitle = document.getElementById('modalTitle');
const announcementId = document.getElementById('announcementId');

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        modalTitle.textContent = '修改公告';
        announcementId.value = this.dataset.id;
        document.getElementById('announcementType').value = this.dataset.type;
        document.getElementById('announcementIcon').value = this.dataset.icon;
        document.getElementById('announcementContent').value = this.dataset.content;
        document.getElementById('announcementSort').value = this.dataset.sort;
        document.getElementById('announcementActive').checked = this.dataset.active == 1;
        announcementModal.show();
    });
});

function deleteAnnouncement(id) {
    if (confirm('确定删除此公告？')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?php
$content = ob_get_clean();
require 'layout.php';
?>
