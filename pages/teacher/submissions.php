<?php
// pages/teacher/submissions.php
require_once '../../includes/config.php';
requireRole('teacher');

$db = getDB();
$filter = $_GET['status'] ?? 'all';

if ($filter !== 'all') {
    $stmt = $db->prepare("SELECT s.*, u.full_name as student_name, u.department, g.grade, g.score FROM submissions s JOIN users u ON s.student_id=u.id LEFT JOIN grades g ON s.id=g.submission_id WHERE s.status=? ORDER BY s.submitted_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $db->query("SELECT s.*, u.full_name as student_name, u.department, g.grade, g.score FROM submissions s JOIN users u ON s.student_id=u.id LEFT JOIN grades g ON s.id=g.submission_id ORDER BY s.submitted_at DESC");
}
$submissions = $stmt->fetchAll();

$pageTitle  = 'Student Submissions';
$activePage = 'submissions.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Student Submissions</h2><p>Review and grade all student work.</p></div>
</div>

<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <?php foreach (['all'=>'All','pending'=>'Pending','reviewed'=>'Reviewed','approved'=>'Approved'] as $k=>$v): ?>
  <a href="?status=<?= $k ?>" class="btn btn-sm <?= $filter===$k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></a>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <?php if (empty($submissions)): ?>
      <div class="empty-state"><i class="fas fa-folder-open"></i><h3>No submissions</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Work</th><th>Student</th><th>Type</th><th>Status</th><th>Grade</th><th>Submitted</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($submissions as $s): ?>
        <tr>
          <td><strong><?= clean($s['title']) ?></strong><br><small style="color:var(--text-muted)"><?= clean($s['subject']) ?></small></td>
          <td><?= clean($s['student_name']) ?><br><small style="color:var(--text-muted)"><?= clean($s['department']) ?></small></td>
          <td><span class="badge-status approved"><?= clean($s['type']) ?></span></td>
          <td><span class="badge-status <?= $s['status'] ?>"><?= $s['status'] ?></span></td>
          <td><?= $s['grade'] ? '<span class="grade-circle grade-'.substr($s['grade'],0,1).'" style="width:36px;height:36px;font-size:0.8rem;display:inline-flex;">'.clean($s['grade']).'</span>' : '—' ?></td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($s['submitted_at'])) ?></td>
          <td>
            <a href="grade.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">
              <i class="fas fa-<?= $s['grade'] ? 'edit' : 'pen' ?>"></i> <?= $s['grade'] ? 'Edit' : 'Grade' ?>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
