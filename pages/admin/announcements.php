<?php
// pages/admin/announcements.php
require_once '../../includes/config.php';
requireRole('admin');

$db  = getDB();
$uid = $_SESSION['user_id'];

// Handle delete
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM announcements WHERE id=?")->execute([intval($_GET['delete'])]);
    setFlash('success', 'Announcement deleted.');
    header('Location: announcements.php'); exit();
}

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $audience = $_POST['audience'] ?? 'all';

    if (empty($title) || empty($content)) {
        setFlash('error', 'Title and content are required.');
    } else {
        $db->prepare("INSERT INTO announcements (author_id, title, content, audience) VALUES (?,?,?,?)")->execute([$uid, $title, $content, $audience]);
        setFlash('success', 'Announcement posted!');
    }
    header('Location: announcements.php'); exit();
}

$announcements = $db->query("SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.author_id=u.id ORDER BY a.created_at DESC")->fetchAll();

$pageTitle  = 'Announcements';
$activePage = 'announcements.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Announcements</h2><p>Post and manage school announcements.</p></div>
</div>

<div class="content-grid">
  <!-- Create Announcement -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-plus-circle"></i> New Announcement</h3></div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" placeholder="Announcement title..." required>
        </div>
        <div class="form-group">
          <label>Audience</label>
          <select name="audience">
            <option value="all">Everyone (All Users)</option>
            <option value="students">Students Only</option>
            <option value="teachers">Teachers Only</option>
          </select>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="content" placeholder="Write your announcement..." rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-bullhorn"></i> Post Announcement</button>
      </form>
    </div>
  </div>

  <!-- Existing Announcements -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-list"></i> All Announcements (<?= count($announcements) ?>)</h3></div>
    <div class="card-body" style="padding:0;max-height:520px;overflow-y:auto;">
      <?php if (empty($announcements)): ?>
        <div class="empty-state"><i class="fas fa-bullhorn"></i><h3>No announcements yet</h3></div>
      <?php else: ?>
        <?php foreach ($announcements as $a): ?>
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
            <div style="flex:1;">
              <h4 style="font-size:0.9rem;margin-bottom:4px;"><?= clean($a['title']) ?></h4>
              <p style="font-size:0.82rem;color:var(--text-secondary);line-height:1.5;"><?= clean(substr($a['content'],0,120)) ?>...</p>
              <div style="margin-top:6px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <span class="badge-status <?= $a['audience']==='all'?'approved':($a['audience']==='students'?'reviewed':'pending') ?>"><?= $a['audience'] ?></span>
                <small style="color:var(--text-muted)"><?= clean($a['full_name']) ?> · <?= date('M j, Y', strtotime($a['created_at'])) ?></small>
              </div>
            </div>
            <a href="?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm btn-icon"
               data-confirm="Delete this announcement?"><i class="fas fa-trash"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
