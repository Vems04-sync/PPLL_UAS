<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/config/database.php"; // pastikan $pdo tersedia
include_once __DIR__ . "/layout/header.php";
include_once __DIR__ . "/layout/sidebar.php";

$user_id = $_SESSION['user_id'] ?? 0;

$totalTasks = $pendingTasks = $completedTasks = 0;
$tasks = [];

if ($user_id > 0 && isset($pdo)) {
    try {
        // Hitung statistik task
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $totalTasks = intval($stmt->fetchColumn());

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status='pending'");
        $stmt->execute([$user_id]);
        $pendingTasks = intval($stmt->fetchColumn());

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status='completed'");
        $stmt->execute([$user_id]);
        $completedTasks = intval($stmt->fetchColumn());

        // Ambil semua task
        $stmt = $pdo->prepare("
            SELECT t.id, t.title, t.description, t.priority, t.deadline, t.status, c.name AS category
            FROM tasks t
            LEFT JOIN categories c ON t.category_id=c.id
            WHERE t.user_id=?
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("dashboard fetch error: ".$e->getMessage());
    }
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/JS-164.js"></script>

<style>
/* ==== CSS Gabungan ==== */
#page-content-wrapper { width:100%; margin:0; padding:28px 40px; min-height:100vh; background:#f6f8fa; box-sizing:border-box; }
.app-container { max-width:1200px; margin:0 auto; }
.header-row { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
.header-left h2 { margin:0; font-size:26px; font-weight:700; }
.header-left p { margin:6px 0 0; color:#6b7280; }
.cards-wrap { display:flex; gap:18px; margin-top:20px; }
.stat-card { flex:1; background:#fff; border-radius:12px; padding:18px; display:flex; align-items:center; gap:14px; border:1px solid rgba(15,23,42,0.03); box-shadow:0 1px 0 rgba(16,24,40,0.03); }
.stat-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; }
.stat-content .label { color:#6b7280; font-weight:600; }
.stat-content .value { font-weight:700; font-size:18px; margin-top:6px; }
.icon-blue { background:#eef6ff; color:#2b6cb0; display:flex; align-items:center; justify-content:center; font-size:24px; }
.icon-amber { background:#fff7ed; color:#b45309; display:flex; align-items:center; justify-content:center; font-size:24px; }
.icon-green { background:#ecfdf5; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:24px; }
.controls { margin-top:18px; background:#fff; border-radius:12px; padding:16px; border:1px solid rgba(15,23,42,0.03); box-shadow:0 1px 0 rgba(16,24,40,0.03); }
.controls .row { display:flex; gap:12px; align-items:center; }
.search { flex:1; display:flex; align-items:center; gap:12px; background:#f3f4f6; padding:12px 16px; border-radius:10px; }
.search input { border:0; background:transparent; outline:none; width:100%; }
.filter-tabs { display:flex; gap:12px; margin-top:12px; }
.tab { padding:8px 14px; background:#f3f4f6; border-radius:20px; font-weight:600; cursor:pointer; }
.tab.active { background:#fff; box-shadow:0 1px 3px rgba(2,6,23,0.06); }
.table-wrap { margin-top:18px; }
.table-card { background:#fff; border-radius:12px; padding:10px; border:1px solid rgba(15,23,42,0.03); box-shadow:0 1px 0 rgba(16,24,40,0.03); }
.table { width:100%; border-collapse:collapse; }
.table thead th { text-align:left; padding:16px; font-weight:700; font-size:14px; color:#111827; border-bottom:1px solid #eef2f7; }
.table tbody td { padding:16px; vertical-align:middle; border-bottom:1px solid #f1f5f9; color:#475569; }
.checkbox-td { width:60px; }
.task-title { font-weight:600; }
.task-desc { font-size:13px; color:#6b7280; margin-top:6px; }
.priority-badge { padding:6px 10px; border-radius:8px; font-weight:700; font-size:12px; display:inline-block; }
.priority-low { background:#eefcf0; color:#15803d; }
.priority-medium { background:#eef2ff; color:#4c1d95; }
.priority-high { background:#fff1f2; color:#9b1c1c; }
.category-pill { display:inline-block; padding:6px 10px; border-radius:12px; background:#f5f3ff; color:#6b21a8; font-weight:700; }

/* ==== Action Buttons ==== */
.actions { display:flex; gap:8px; }
.action-btn { border:none; background:transparent; cursor:pointer; padding:6px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; transition: background 0.2s; }
.action-btn svg { stroke:#475569; width:18px; height:18px; }
.action-btn:hover svg { stroke-width:2.5; }
.action-btn.view-btn:hover svg { stroke:#1d4ed8; }
.action-btn.edit-btn:hover svg { stroke:#f59e0b; }
.action-btn.delete-btn:hover svg { stroke:#ef4444; }

.empty-state { padding:40px; text-align:center; color:#6b7280; }
.empty-state h4 { margin:0 0 8px; }

@media (max-width:900px){
    .cards-wrap{flex-direction:column}
    .header-row{flex-direction:column;align-items:flex-start}
}
</style>

<div class="app-container">
  <div class="header-row">
    <div class="header-left">
      <h2>Dashboard</h2>
      <p>Manage your tasks and track progress</p>
    </div>
    <div>
      <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addTaskModal">+ New Task</button>
    </div>
  </div>

  <!-- Statistik Task -->
  <div class="cards-wrap">
    <div class="stat-card">
      <div class="stat-icon icon-blue">📋</div>
      <div class="stat-content">
        <div class="label">Total Tasks</div>
        <div class="value" id="totalTasks"><?= $totalTasks ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-amber">⏳</div>
      <div class="stat-content">
        <div class="label">Pending</div>
        <div class="value" id="pendingTasks"><?= $pendingTasks ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-green">✅</div>
      <div class="stat-content">
        <div class="label">Completed</div>
        <div class="value" id="completedTasks"><?= $completedTasks ?></div>
      </div>
    </div>
  </div>

  <!-- Search / Filter -->
  <div class="controls">
    <div class="row">
      <div class="search">
        <input id="searchInput" placeholder="Search task title..." />
      </div>
    </div>
    <div class="filter-tabs">
      <div class="tab active" data-filter="all">All</div>
      <div class="tab" data-filter="pending">Pending</div>
      <div class="tab" data-filter="completed">Completed</div>
    </div>
  </div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="addTaskForm">
        <div class="modal-header">
          <h5 class="modal-title">Add New Task</h5>
          <button type="button" class="btn-close" onclick="closeModal('addTaskModal')"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label>Priority</label>
            <select name="priority" class="form-select">
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Category</label>
            <select name="category" class="form-select">
              <option value="work">Work</option>
              <option value="personal">Personal</option>
              <option value="uncategorized" selected>Uncategorized</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Deadline</label>
            <input type="date" name="deadline" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Task</button>
        </div>
      </form>
    </div>
  </div>
</div>


  <!-- View Modal -->
<div class="modal fade" id="viewModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">View Task</h5>
        <button type="button" class="btn-close" onclick="closeModal('viewModal')"></button>
      </div>
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="viewTitle"></span></p>
        <p><strong>Description:</strong> <span id="viewDesc"></span></p>
        <p><strong>Priority:</strong> <span id="viewPriority"></span></p>
        <p><strong>Category:</strong> <span id="viewCategory"></span></p>
        <p><strong>Deadline:</strong> <span id="viewDeadline"></span></p>
        <p><strong>Status:</strong> <span id="viewStatus"></span></p>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editTaskForm">
        <div class="modal-header"><h5 class="modal-title">Edit Task</h5>
          <button type="button" class="btn-close" onclick="closeModal('editModal')"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="editTaskId">
          <div class="mb-3"><label>Title</label><input type="text" name="title" id="editTitle" class="form-control" required></div>
          <div class="mb-3"><label>Description</label><textarea name="description" id="editDesc" class="form-control"></textarea></div>
          <div class="mb-3"><label>Priority</label>
            <select name="priority" id="editPriority" class="form-select">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Category</label>
            <select name="category" id="editCategory" class="form-select">
              <option value="work">Work</option>
              <option value="personal" selected>Personal</option>
              <option value="uncategorized">Uncategorized</option>
            </select>
          </div>
          <div class="mb-3"><label>Deadline</label><input type="date" name="deadline" id="editDeadline" class="form-control"></div>
          <div class="mb-3"><label>Status</label>
            <select name="status" id="editStatus" class="form-select">
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
      </form>
    </div>
  </div>
</div>


  <!-- Table Tasks -->
  <div class="table-wrap">
    <div class="table-card">
      <table class="table">
        <thead>
          <tr>
            <th class="checkbox-td">Done</th>
            <th>Task</th>
            <th style="width:120px;">Priority</th>
            <th style="width:140px;">Category</th>
            <th style="width:120px;">Deadline</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody id="tasksTbody">
        <?php if (!empty($tasks)): ?>
            <?php foreach($tasks as $t):
                $tid = (int)$t['id'];
                $title = htmlspecialchars($t['title']);
                $desc = htmlspecialchars($t['description']);
                $priority = strtolower($t['priority'] ?? 'medium');
                $category = htmlspecialchars($t['category'] ?? 'Uncategorized');
                $deadline = $t['deadline'] ?: '-';
                $status = strtolower($t['status'] ?? 'pending');
                $pclass = $priority==='low'?'priority-low':($priority==='high'?'priority-high':'priority-medium');
            ?>
            <tr data-id="<?= $tid ?>" data-title="<?= $title ?>" data-desc="<?= $desc ?>" data-priority="<?= $priority ?>" data-category="<?= $category ?>" data-deadline="<?= $deadline ?>" data-status="<?= $status ?>">
                <td class="checkbox-td"><input type="checkbox" <?= $status==='completed'?'checked':'' ?> disabled></td>
                <td><div class="task-title"><?= $title ?></div><?php if($desc): ?><div class="task-desc"><?= strlen($desc)>120?substr($desc,0,120).'...':$desc ?></div><?php endif;?></td>
                <td><span class="priority-badge <?= $pclass ?>"><?= ucfirst($priority) ?></span></td>
                <td><span class="category-pill"><?= $category ?></span></td>
                <td><?= $deadline ?></td>
                <td class="actions">
                    <button class="action-btn view-btn" onclick="openView(this.closest('tr'))" title="View">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button class="action-btn edit-btn" onclick="openEdit(this.closest('tr'))" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6m2 2l-6 6m-2 2H6v-3"/></svg>
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteTask(<?= $tid ?>)" title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </td>
            </tr>
            <?php endforeach;?>
        <?php else: ?>
            <tr><td colspan="6"><div class="empty-state"><h4>No tasks yet</h4><div>Add your first task using the <strong>New Task</strong> button.</div></div></td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>