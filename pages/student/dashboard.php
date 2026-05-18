<?php
// pages/student/dashboard.php
require_once '../../includes/config.php';
requireRole('student');

$db     = getDB();
$uid    = $_SESSION['user_id'];

// Stats
$subs   = $db->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ?");
$subs->execute([$uid]); $totalSubs = $subs->fetchColumn();

$approved = $db->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ? AND status = 'approved'");
$approved->execute([$uid]); $totalApproved = $approved->fetchColumn();

$graded = $db->prepare("SELECT COUNT(*) FROM grades WHERE student_id = ?");
$graded->execute([$uid]); $totalGraded = $graded->fetchColumn();

$avgScore = $db->prepare("SELECT AVG(score) FROM grades WHERE student_id = ?");
$avgScore->execute([$uid]); $avg = round($avgScore->fetchColumn() ?? 0, 1);

// Recent submissions
$recentSubs = $db->prepare("SELECT * FROM submissions WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 5");
$recentSubs->execute([$uid]); $submissions = $recentSubs->fetchAll();

// Recent feedback
$recentFeedback = $db->prepare("
    SELECT g.*, s.title, u.full_name as teacher_name
    FROM grades g
    JOIN submissions s ON g.submission_id = s.id
    JOIN users u ON g.teacher_id = u.id
    WHERE g.student_id = ?
    ORDER BY g.graded_at DESC LIMIT 3");
$recentFeedback->execute([$uid]); $feedbacks = $recentFeedback->fetchAll();

// Announcements
$anns = $db->query("SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.author_id = u.id WHERE audience IN ('all','students') ORDER BY created_at DESC LIMIT 3")->fetchAll();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div>
    <h2>👋 Welcome back, <?= clean(explode(' ', $user['full_name'])[0]) ?>!</h2>
    <p>Here's your academic portfolio overview.</p>
  </div>
  <a href="upload.php" class="btn btn-secondary btn-sm">
    <i class="fas fa-plus"></i> Upload Work
  </a>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-icon"><i class="fas fa-folder-open"></i></div>
    <h3><?= $totalSubs ?></h3>
    <p>Total Submissions</p>
    <div class="trend up"><i class="fas fa-arrow-up"></i> Growing portfolio</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
    <h3><?= $totalApproved ?></h3>
    <p>Approved Works</p>
    <div class="trend up"><i class="fas fa-check"></i> Keep it up!</div>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><i class="fas fa-star"></i></div>
    <h3><?= $totalGraded ?></h3>
    <p>Works Graded</p>
    <div class="trend"><i class="fas fa-pen"></i> Reviewed</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
    <h3><?= $avg ?>%</h3>
    <p>Average Score</p>
    <div class="trend <?= $avg >= 70 ? 'up' : 'down' ?>">
      <i class="fas fa-<?= $avg >= 70 ? 'arrow-up' : 'arrow-down' ?>"></i>
      <?= $avg >= 70 ? 'Good performance' : 'Needs improvement' ?>
    </div>
  </div>
</div>

<div class="content-grid">
  <!-- Recent Submissions -->
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-folder"></i> Recent Submissions</h3>
      <a href="submissions.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($submissions)): ?>
        <div class="empty-state">
          <i class="fas fa-folder-open"></i>
          <h3>No submissions yet</h3>
          <p>Start by uploading your first work!</p>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Title</th><th>Type</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($submissions as $s): ?>
              <tr>
                <td><strong><?= clean($s['title']) ?></strong><br><small style="color:var(--text-muted)"><?= clean($s['subject']) ?></small></td>
                <td><span class="badge-status approved"><?= clean($s['type']) ?></span></td>
                <td><span class="badge-status <?= $s['status'] ?>"><?= $s['status'] ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent Feedback -->
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-comments"></i> Latest Feedback</h3>
      <a href="feedback.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="card-body">
      <?php if (empty($feedbacks)): ?>
        <div class="empty-state">
          <i class="fas fa-comments"></i>
          <h3>No feedback yet</h3>
          <p>Submit your work to receive teacher feedback.</p>
        </div>
      <?php else: ?>
        <?php foreach ($feedbacks as $f): ?>
          <div class="feedback-item">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
              <div class="grade-circle grade-<?= substr($f['grade'],0,1) ?>" style="width:40px;height:40px;font-size:0.9rem;">
                <?= clean($f['grade']) ?>
              </div>
              <div>
                <h4><?= clean($f['title']) ?></h4>
                <div class="feedback-meta">By <?= clean($f['teacher_name']) ?></div>
              </div>
            </div>
            <p><?= clean(substr($f['feedback'], 0, 120)) ?>...</p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Announcements -->
<div class="card">
  <div class="card-header">
    <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
  </div>
  <div class="card-body">
    <?php if (empty($anns)): ?>
      <div class="empty-state"><i class="fas fa-bullhorn"></i><h3>No announcements</h3></div>
    <?php else: ?>
      <?php foreach ($anns as $a): ?>
        <div class="announcement">
          <div class="ann-icon"><i class="fas fa-bell"></i></div>
          <div class="ann-content">
            <h4><?= clean($a['title']) ?></h4>
            <p><?= clean(substr($a['content'], 0, 100)) ?>...</p>
            <span><?= clean($a['full_name']) ?> · <?= date('M j, Y', strtotime($a['created_at'])) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
