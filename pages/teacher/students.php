<?php
// pages/teacher/students.php
require_once '../../includes/config.php';
requireRole('teacher');

$db = getDB();

$students = $db->query("
    SELECT u.*,
        COUNT(s.id) as total_subs,
        SUM(s.status='approved') as approved,
        AVG(g.score) as avg_score
    FROM users u
    LEFT JOIN submissions s ON u.id=s.student_id
    LEFT JOIN grades g ON u.id=g.student_id
    WHERE u.role='student'
    GROUP BY u.id
    ORDER BY u.full_name")->fetchAll();

$pageTitle  = 'Students';
$activePage = 'students.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Students</h2><p>Overview of all registered students and their performance.</p></div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <?php if (empty($students)): ?>
      <div class="empty-state"><i class="fas fa-users"></i><h3>No students registered yet.</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Student</th><th>Department</th><th>Year</th><th>Submissions</th><th>Approved</th><th>Avg Score</th><th>Joined</th></tr></thead>
        <tbody>
        <?php foreach ($students as $s):
          $initials = strtoupper(substr($s['full_name'],0,1) . (strpos($s['full_name'],' ')!==false ? substr($s['full_name'],strpos($s['full_name'],' ')+1,1) : ''));
          $avg = round($s['avg_score'] ?? 0, 1);
        ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;flex-shrink:0;"><?= $initials ?></div>
              <div>
                <strong style="font-size:0.875rem;"><?= clean($s['full_name']) ?></strong>
                <br><small style="color:var(--text-muted)"><?= clean($s['email']) ?></small>
              </div>
            </div>
          </td>
          <td><?= clean($s['department'] ?: '—') ?></td>
          <td><?= clean($s['grade_level'] ?: '—') ?></td>
          <td><strong><?= $s['total_subs'] ?></strong></td>
          <td>
            <span class="badge-status approved"><?= $s['approved'] ?? 0 ?></span>
          </td>
          <td>
            <?php if ($avg > 0): ?>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="progress-bar" style="width:60px;">
                <div class="progress-fill" data-width="<?= $avg ?>" style="background:<?= $avg>=70?'var(--green)':($avg>=50?'var(--orange)':'var(--red)') ?>;"></div>
              </div>
              <span style="font-size:0.8rem;"><?= $avg ?>%</span>
            </div>
            <?php else: echo '—'; endif; ?>
          </td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M Y', strtotime($s['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
