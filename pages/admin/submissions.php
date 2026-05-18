<?php
// pages/admin/submissions.php
require_once '../../includes/config.php';
requireRole('admin');

$db = getDB();
$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT s.*, u.full_name as student_name, u.department, g.grade, g.score, t.full_name as teacher_name
        FROM submissions s
        JOIN users u ON s.student_id=u.id
        LEFT JOIN grades g ON s.id=g.submission_id
        LEFT JOIN users t ON g.teacher_id=t.id
        WHERE 1=1";
$params = [];
if ($filter !== 'all') { $sql .= " AND s.status=?"; $params[] = $filter; }
if ($search) { $sql .= " AND (s.title LIKE ? OR u.full_name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$sql .= " ORDER BY s.submitted_at DESC";
$stmt = $db->prepare($sql); $stmt->execute($params);
$submissions = $stmt->fetchAll();

$pageTitle  = 'All Submissions';
$activePage = 'submissions.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>All Submissions</h2><p>Monitor and manage all student portfolio submissions.</p></div>
</div>

<form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
  <input type="text" name="search" value="<?= clean($search) ?>" placeholder="Search title or student..."
    style="padding:10px 14px;border:2px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-secondary);color:var(--text-primary);font-family:'DM Sans',sans-serif;flex:1;min-width:200px;outline:none;">
  <div style="display:flex;gap:6px;flex-wrap:wrap;">
    <?php foreach (['all'=>'All','pending'=>'Pending','reviewed'=>'Reviewed','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v): ?>
    <button type="submit" name="status" value="<?= $k ?>" class="btn btn-sm <?= $filter===$k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></button>
    <?php endforeach; ?>
  </div>
</form>

<div class="card">
  <div class="card-body" style="padding:0;">
    <?php if (empty($submissions)): ?>
      <div class="empty-state"><i class="fas fa-folder-open"></i><h3>No submissions found</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Work</th><th>Student</th><th>Type</th><th>Status</th><th>Grade</th><th>Graded By</th><th>Date</th></tr>
        </thead>
        <tbody>
        <?php foreach ($submissions as $s): ?>
        <tr>
          <td>
            <strong style="font-size:0.875rem;"><?= clean($s['title']) ?></strong>
            <?php if ($s['subject']): ?><br><small style="color:var(--text-muted)"><?= clean($s['subject']) ?></small><?php endif; ?>
          </td>
          <td>
            <span style="font-size:0.875rem;"><?= clean($s['student_name']) ?></span>
            <?php if ($s['department']): ?><br><small style="color:var(--text-muted)"><?= clean($s['department']) ?></small><?php endif; ?>
          </td>
          <td><span class="badge-status approved"><?= clean($s['type']) ?></span></td>
          <td><span class="badge-status <?= $s['status'] ?>"><?= $s['status'] ?></span></td>
          <td>
            <?php if ($s['grade']): ?>
              <div style="display:flex;align-items:center;gap:6px;">
                <div class="grade-circle grade-<?= substr($s['grade'],0,1) ?>" style="width:34px;height:34px;font-size:0.78rem;display:inline-flex;"><?= clean($s['grade']) ?></div>
                <small style="color:var(--text-muted)"><?= $s['score'] ?>/100</small>
              </div>
            <?php else: echo '<span style="color:var(--text-muted)">—</span>'; endif; ?>
          </td>
          <td style="font-size:0.82rem;"><?= clean($s['teacher_name'] ?: '—') ?></td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($s['submitted_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
