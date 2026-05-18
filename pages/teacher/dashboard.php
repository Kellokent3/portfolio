<?php
// pages/teacher/dashboard.php
require_once '../../includes/config.php';
requireRole('teacher');

$db  = getDB();
$tid = $_SESSION['user_id'];

// Stats
$totalStudents = $db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$pendingSubs   = $db->query("SELECT COUNT(*) FROM submissions WHERE status='pending'")->fetchColumn();
$graded        = $db->prepare("SELECT COUNT(*) FROM grades WHERE teacher_id=?"); $graded->execute([$tid]); $totalGraded = $graded->fetchColumn();
$totalSubs     = $db->query("SELECT COUNT(*) FROM submissions")->fetchColumn();

// Pending submissions (not yet graded)
$pending = $db->query("
    SELECT s.*, u.full_name as student_name, u.department
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    WHERE s.status = 'pending'
    ORDER BY s.submitted_at DESC LIMIT 6")->fetchAll();

// Recent grades by this teacher
$recentGrades = $db->prepare("
    SELECT g.*, s.title, s.type, u.full_name as student_name
    FROM grades g
    JOIN submissions s ON g.submission_id = s.id
    JOIN users u ON g.student_id = u.id
    WHERE g.teacher_id = ?
    ORDER BY g.graded_at DESC LIMIT 5");
$recentGrades->execute([$tid]); $recentGrades = $recentGrades->fetchAll();

$pageTitle  = 'Teacher Dashboard';
$activePage = 'dashboard.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div>
    <h2>👩‍🏫 Teacher Dashboard</h2>
    <p>Review student submissions and manage grades.</p>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-icon"><i class="fas fa-users"></i></div>
    <h3><?= $totalStudents ?></h3><p>Total Students</p>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><i class="fas fa-clock"></i></div>
    <h3><?= $pendingSubs ?></h3><p>Pending Review</p>
    <?php if ($pendingSubs > 0): ?><div class="trend down"><i class="fas fa-exclamation"></i> Needs attention</div><?php endif; ?>
  </div>
  <div class="stat-card green">
    <div class="stat-icon"><i class="fas fa-check-double"></i></div>
    <h3><?= $totalGraded ?></h3><p>Graded by You</p>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon"><i class="fas fa-folder"></i></div>
    <h3><?= $totalSubs ?></h3><p>All Submissions</p>
  </div>
</div>

<div class="content-grid">
  <!-- Pending Submissions -->
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-inbox"></i> Pending Submissions</h3>
      <a href="submissions.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($pending)): ?>
        <div class="empty-state"><i class="fas fa-check-circle" style="color:var(--green);"></i><h3>All caught up!</h3><p>No pending submissions right now.</p></div>
      <?php else: ?>
      <?php foreach ($pending as $s): ?>
      <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border);">
        <div style="width:40px;height:40px;border-radius:12px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;color:var(--accent-dark);flex-shrink:0;">
          <i class="fas fa-file-alt"></i>
        </div>
        <div style="flex:1;overflow:hidden;">
          <strong style="font-size:0.875rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= clean($s['title']) ?></strong>
          <small style="color:var(--text-muted)"><?= clean($s['student_name']) ?> · <?= clean($s['type']) ?></small>
        </div>
        <a href="grade.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">Grade</a>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent Grades -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-star"></i> Recently Graded</h3></div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($recentGrades)): ?>
        <div class="empty-state"><i class="fas fa-star"></i><h3>No grades yet</h3><p>Start reviewing student submissions.</p></div>
      <?php else: ?>
      <?php foreach ($recentGrades as $g): ?>
      <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border);">
        <div class="grade-circle grade-<?= substr($g['grade'],0,1) ?>" style="width:40px;height:40px;font-size:0.85rem;"><?= clean($g['grade']) ?></div>
        <div style="flex:1;">
          <strong style="font-size:0.875rem;display:block;"><?= clean($g['title']) ?></strong>
          <small style="color:var(--text-muted)"><?= clean($g['student_name']) ?> · <?= $g['score'] ?>/<?= $g['max_score'] ?></small>
        </div>
        <small style="color:var(--text-muted);font-size:0.75rem;"><?= date('M j', strtotime($g['graded_at'])) ?></small>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
