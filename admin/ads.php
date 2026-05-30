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
                $stmt = $pdo->prepare("INSERT INTO ads (title, content, link_url, copy_text, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['content'],
                    $_POST['link_url'] ?? null,
                    $_POST['copy_text'] ?? null,
                    (int)$_POST['sort_order'],
                    isset($_POST['is_active']) ? 1 : 0
                ]);
                setFlashMessage('success', '广告添加成功');
                redirect('ads.php');
                break;
                
            case 'edit':
                $stmt = $pdo->prepare("UPDATE ads SET title=?, content=?, link_url=?, copy_text=?, sort_order=?, is_active=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['content'],
                    $_POST['link_url'] ?? null,
                    $_POST['copy_text'] ?? null,
                    (int)$_POST['sort_order'],
                    isset($_POST['is_active']) ? 1 : 0,
                    (int)$_POST['id']
                ]);
                setFlashMessage('success', '广告修改成功');
                redirect('ads.php');
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM ads WHERE id=?");
                $stmt->execute([(int)$_POST['id']]);
                setFlashMessage('success', '广告删除成功');
                redirect('ads.php');
                break;
        }
    } catch (PDOException $e) {
        setFlashMessage('danger', '操作失败：' . $e->getMessage());
        redirect('ads.php');
    }
}

ob_start();
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-megaphone"></i> 广告管理</h2>
        <p class="text-muted mb-0">管理推荐服务和广告内容</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adModal">
        <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">添加广告</span><span class="d-sm-none">添加</span>
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
                        <th>标题</th>
                        <th>内容</th>
                        <th>链接 URL</th>
                        <th>复制文本</th>
                        <th>状态</th>
                        <th width="120">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM ads ORDER BY sort_order");
                    while ($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><?= $row['sort_order'] ?></td>
                        <td><?= sanitize($row['title']) ?></td>
                        <td><?= sanitize($row['content']) ?></td>
                        <td><?= $row['link_url'] ? sanitize($row['link_url']) : '-' ?></td>
                        <td><?= $row['copy_text'] ? sanitize($row['copy_text']) : '-' ?></td>
                        <td><span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>"><?= $row['is_active'] ? '启用' : '禁用' ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                data-id="<?= $row['id'] ?>"
                                data-title="<?= sanitize($row['title']) ?>"
                                data-content="<?= sanitize($row['content']) ?>"
                                data-link="<?= sanitize($row['link_url'] ?? '') ?>"
                                data-copy="<?= sanitize($row['copy_text'] ?? '') ?>"
                                data-sort="<?= $row['sort_order'] ?>"
                                data-active="<?= $row['is_active'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAd(<?= $row['id'] ?>)">
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
            $stmt = $pdo->query("SELECT * FROM ads ORDER BY sort_order");
            while ($row = $stmt->fetch()):
            ?>
            <div class="card mb-3 border shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2"><?= sanitize($row['title']) ?></h6>
                    <div class="mb-2">
                        <small class="text-muted">内容:</small><br>
                        <?= nl2br(sanitize($row['content'])) ?>
                    </div>
                    <?php if ($row['link_url']): ?>
                    <div class="mb-2">
                        <small class="text-muted">链接:</small><br>
                        <a href="<?= sanitize($row['link_url']) ?>" target="_blank" class="text-break"><?= sanitize($row['link_url']) ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if ($row['copy_text']): ?>
                    <div class="mb-2">
                        <small class="text-muted">复制文本:</small> <code><?= sanitize($row['copy_text']) ?></code>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>"><?= $row['is_active'] ? '启用' : '禁用' ?></span>
                        <div>
                            <button class="btn btn-sm btn-outline-primary edit-btn-mobile" 
                                data-id="<?= $row['id'] ?>"
                                data-title="<?= sanitize($row['title']) ?>"
                                data-content="<?= sanitize($row['content']) ?>"
                                data-link="<?= sanitize($row['link_url'] ?? '') ?>"
                                data-copy="<?= sanitize($row['copy_text'] ?? '') ?>"
                                data-sort="<?= $row['sort_order'] ?>"
                                data-active="<?= $row['is_active'] ?>">
                                <i class="bi bi-pencil"></i> 编辑
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAd(<?= $row['id'] ?>)">
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

<div class="modal fade" id="adModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="ads.php?action=<?= $action ?>">
                <input type="hidden" name="id" id="adId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">添加广告</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" class="form-control" name="title" id="adTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">内容</label>
                        <textarea class="form-control" name="content" id="adContent" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">链接 URL</label>
                        <input type="url" class="form-control" name="link_url" id="adLink">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">复制文本</label>
                        <input type="text" class="form-control" name="copy_text" id="adCopy">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="sort_order" id="adSort" value="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="adActive" checked>
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

<form method="POST" action="ads.php?action=delete" id="deleteForm">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
const adModal = new bootstrap.Modal(document.getElementById('adModal'));
const modalTitle = document.getElementById('modalTitle');
const adId = document.getElementById('adId');

// 桌面端编辑按钮
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        modalTitle.textContent = '修改广告';
        adId.value = this.dataset.id;
        document.getElementById('adTitle').value = this.dataset.title;
        document.getElementById('adContent').value = this.dataset.content;
        document.getElementById('adLink').value = this.dataset.link;
        document.getElementById('adCopy').value = this.dataset.copy;
        document.getElementById('adSort').value = this.dataset.sort;
        document.getElementById('adActive').checked = this.dataset.active == 1;
        adModal.show();
    });
});

// 移动端编辑按钮
document.querySelectorAll('.edit-btn-mobile').forEach(btn => {
    btn.addEventListener('click', function() {
        modalTitle.textContent = '修改广告';
        adId.value = this.dataset.id;
        document.getElementById('adTitle').value = this.dataset.title;
        document.getElementById('adContent').value = this.dataset.content;
        document.getElementById('adLink').value = this.dataset.link;
        document.getElementById('adCopy').value = this.dataset.copy;
        document.getElementById('adSort').value = this.dataset.sort;
        document.getElementById('adActive').checked = this.dataset.active == 1;
        adModal.show();
    });
});

function deleteAd(id) {
    if (confirm('确定删除此广告？')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?php
$content = ob_get_clean();
require 'layout.php';
?>
