<?php
// pages/student/submissions.php
require_once '../../includes/config.php';
requireRole('student');

$db  = getDB();
$uid = $_SESSION['user_id'];

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM submissions WHERE id = ? AND student_id = ?");
    $stmt->execute([$_GET['delete'], $uid]);
    setFlash('success', 'Submission deleted.');
    header('Location: submissions.php'); exit();
}

// Filter
$filter = $_GET['type'] ?? 'all';
if ($filter !== 'all') {
    $stmt = $db->prepare("SELECT s.*, g.grade, g.score FROM submissions s LEFT JOIN grades g ON s.id = g.submission_id WHERE s.student_id = ? AND s.type = ? ORDER BY s.submitted_at DESC");
    $stmt->execute([$uid, $filter]);
} else {
    $stmt = $db->prepare("SELECT s.*, g.grade, g.score FROM submissions s LEFT JOIN grades g ON s.id = g.submission_id WHERE s.student_id = ? ORDER BY s.submitted_at DESC");
    $stmt->execute([$uid]);
}
$submissions = $stmt->fetchAll();

$pageTitle  = 'My Submissions';
$activePage = 'submissions.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div>
    <h2>My Submissions</h2>
    <p>All your uploaded assignments, projects, and certificates.</p>
  </div>
  <a href="upload.php" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Upload New</a>
</div>

<!-- Filter tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <?php foreach (['all'=>'All','assignment'=>'Assignments','project'=>'Projects','certificate'=>'Certificates'] as $k=>$v): ?>
  <a href="?type=<?= $k ?>" class="btn btn-sm <?= $filter===$k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></a>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <?php if (empty($submissions)): ?>
      <div class="empty-state">
        <i class="fas fa-folder-open"></i>
        <h3>No submissions found</h3>
        <p>Upload your first work to build your portfolio!</p>
        <a href="upload.php" class="btn btn-primary btn-sm" style="margin-top:12px;width:auto;">Upload Now</a>
      </div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Work</th><th>Type</th><th>Subject</th><th>Status</th><th>Grade</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($submissions as $s):
          $ext  = strtolower(pathinfo($s['file_name'] ?? '', PATHINFO_EXTENSION));
          $iconClass = in_array($ext,['pdf']) ? 'pdf' : (in_array($ext,['doc','docx']) ? 'doc' : (in_array($ext,['zip']) ? 'zip' : (in_array($ext,['jpg','jpeg','png']) ? 'img' : 'other')));
          $icons = ['pdf'=>'fa-file-pdf','doc'=>'fa-file-word','zip'=>'fa-file-archive','img'=>'fa-file-image','other'=>'fa-file'];
        ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="file-icon <?= $iconClass ?>"><i class="fas <?= $icons[$iconClass] ?>"></i></div>
              <div>
                <strong><?= clean($s['title']) ?></strong>
                <?php if ($s['file_name']): ?>
                <br><small style="color:var(--text-muted)"><?= clean($s['file_name']) ?></small>
                <?php endif; ?>
              </div>
            </div>
          </td>
          <td><span class="badge-status approved"><?= clean($s['type']) ?></span></td>
          <td><?= clean($s['subject'] ?: '—') ?></td>
          <td><span class="badge-status <?= $s['status'] ?>"><?= $s['status'] ?></span></td>
          <td>
            <?php if ($s['grade']): ?>
              <span class="grade-circle grade-<?= substr($s['grade'],0,1) ?>" style="width:36px;height:36px;font-size:0.8rem;display:inline-flex;">
                <?= clean($s['grade']) ?>
              </span>
            <?php else: ?>
              <span style="color:var(--text-muted);font-size:0.85rem;">—</span>
            <?php endif; ?>
          </td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($s['submitted_at'])) ?></td>
          <td>
            <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm btn-icon"
               data-confirm="Delete this submission?"><i class="fas fa-trash"></i></a>
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
