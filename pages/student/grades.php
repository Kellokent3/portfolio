<?php
// pages/student/grades.php
require_once '../../includes/config.php';
requireRole('student');

$db  = getDB();
$uid = $_SESSION['user_id'];

$stmt = $db->prepare("
    SELECT g.*, s.title, s.type, s.subject, u.full_name as teacher_name
    FROM grades g
    JOIN submissions s ON g.submission_id = s.id
    JOIN users u ON g.teacher_id = u.id
    WHERE g.student_id = ?
    ORDER BY g.graded_at DESC");
$stmt->execute([$uid]);
$grades = $stmt->fetchAll();

// Calculate stats
$totalGrades = count($grades);
$avgScore = $totalGrades ? round(array_sum(array_column($grades, 'score')) / $totalGrades, 1) : 0;
$highest = $totalGrades ? max(array_column($grades, 'score')) : 0;
$lowest  = $totalGrades ? min(array_column($grades, 'score')) : 0;

$pageTitle  = 'My Grades';
$activePage = 'grades.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>My Grades</h2><p>Track your academic performance and scores.</p></div>
</div>

<!-- Grade Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
  <div class="stat-card blue">
    <div class="stat-icon"><i class="fas fa-star"></i></div>
    <h3><?= $avgScore ?>%</h3><p>Average Score</p>
  </div>
  <div class="stat-card green">
    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
    <h3><?= $highest ?>%</h3><p>Highest Score</p>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
    <h3><?= $lowest ?>%</h3><p>Lowest Score</p>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon"><i class="fas fa-list"></i></div>
    <h3><?= $totalGrades ?></h3><p>Total Graded</p>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3><i class="fas fa-star"></i> Grade History</h3></div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($grades)): ?>
      <div class="empty-state">
        <i class="fas fa-star"></i>
        <h3>No grades yet</h3>
        <p>Submit your work and wait for teacher review.</p>
      </div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Assignment</th><th>Subject</th><th>Teacher</th><th>Score</th><th>Grade</th><th>Progress</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($grades as $g): $pct = ($g['score'] / $g['max_score']) * 100; ?>
        <tr>
          <td>
            <strong><?= clean($g['title']) ?></strong>
            <br><small style="color:var(--text-muted)"><?= ucfirst($g['type']) ?></small>
          </td>
          <td><?= clean($g['subject'] ?: '—') ?></td>
          <td><?= clean($g['teacher_name']) ?></td>
          <td><strong><?= $g['score'] ?> / <?= $g['max_score'] ?></strong></td>
          <td>
            <div class="grade-circle grade-<?= substr($g['grade'],0,1) ?>">
              <?= clean($g['grade']) ?>
            </div>
          </td>
          <td style="min-width:100px;">
            <div class="progress-bar">
              <div class="progress-fill" data-width="<?= round($pct) ?>"></div>
            </div>
            <small style="color:var(--text-muted);font-size:0.75rem;"><?= round($pct) ?>%</small>
          </td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($g['graded_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
