<?php
// pages/student/feedback.php
require_once '../../includes/config.php';
requireRole('student');

$db  = getDB();
$uid = $_SESSION['user_id'];

$stmt = $db->prepare("
    SELECT g.*, s.title, s.type, s.subject, u.full_name as teacher_name, u.department
    FROM grades g
    JOIN submissions s ON g.submission_id = s.id
    JOIN users u ON g.teacher_id = u.id
    WHERE g.student_id = ? AND g.feedback IS NOT NULL AND g.feedback != ''
    ORDER BY g.graded_at DESC");
$stmt->execute([$uid]);
$feedbacks = $stmt->fetchAll();

$pageTitle  = 'Teacher Feedback';
$activePage = 'feedback.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Teacher Feedback</h2><p>Review comments and suggestions from your teachers.</p></div>
</div>

<?php if (empty($feedbacks)): ?>
  <div class="card">
    <div class="card-body">
      <div class="empty-state">
        <i class="fas fa-comments"></i>
        <h3>No feedback yet</h3>
        <p>Submit your work and teachers will leave feedback after reviewing.</p>
      </div>
    </div>
  </div>
<?php else: ?>
  <div style="display:flex;flex-direction:column;gap:16px;">
    <?php foreach ($feedbacks as $f):
      $gradeClass = 'grade-' . substr($f['grade'], 0, 1);
    ?>
    <div class="card">
      <div class="card-body">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
          <div class="grade-circle <?= $gradeClass ?>"><?= clean($f['grade']) ?></div>
          <div style="flex:1;min-width:200px;">
            <h3 style="font-size:1rem;margin-bottom:4px;"><?= clean($f['title']) ?></h3>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
              <span class="badge-status approved"><?= clean($f['type']) ?></span>
              <?php if ($f['subject']): ?><span class="profile-tag"><?= clean($f['subject']) ?></span><?php endif; ?>
              <span style="font-size:0.78rem;color:var(--text-muted);">Score: <strong><?= $f['score'] ?>/<?= $f['max_score'] ?></strong></span>
            </div>

            <div style="background:var(--bg-primary);border-left:4px solid var(--accent);border-radius:0 10px 10px 0;padding:14px 16px;">
              <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.7;"><?= clean($f['feedback']) ?></p>
            </div>

            <div style="margin-top:10px;font-size:0.8rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
              <i class="fas fa-user-tie"></i>
              <strong><?= clean($f['teacher_name']) ?></strong>
              <?php if ($f['department']): ?>· <?= clean($f['department']) ?><?php endif; ?>
              · <?= date('M j, Y', strtotime($f['graded_at'])) ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once '../../includes/layout_end.php'; ?>
