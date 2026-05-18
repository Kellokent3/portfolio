<?php
// pages/admin/reports.php
require_once '../../includes/config.php';
requireRole('admin');

$db = getDB();

// Summary stats
$stats = [
    'students'    => $db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
    'teachers'    => $db->query("SELECT COUNT(*) FROM users WHERE role='teacher'")->fetchColumn(),
    'submissions' => $db->query("SELECT COUNT(*) FROM submissions")->fetchColumn(),
    'grades'      => $db->query("SELECT COUNT(*) FROM grades")->fetchColumn(),
    'approved'    => $db->query("SELECT COUNT(*) FROM submissions WHERE status='approved'")->fetchColumn(),
    'pending'     => $db->query("SELECT COUNT(*) FROM submissions WHERE status='pending'")->fetchColumn(),
    'avg_score'   => round($db->query("SELECT AVG(score) FROM grades")->fetchColumn() ?? 0, 1),
];

// Top students by average score
$topStudents = $db->query("
    SELECT u.full_name, u.department, u.grade_level,
           COUNT(g.id) as graded, AVG(g.score) as avg_score, MAX(g.score) as best
    FROM users u
    JOIN grades g ON u.id = g.student_id
    WHERE u.role='student'
    GROUP BY u.id
    ORDER BY avg_score DESC LIMIT 10")->fetchAll();

// Submissions per month (last 6 months)
$monthly = $db->query("
    SELECT DATE_FORMAT(submitted_at,'%b %Y') as month,
           DATE_FORMAT(submitted_at,'%Y-%m') as sort_key,
           COUNT(*) as cnt
    FROM submissions
    WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY sort_key, month
    ORDER BY sort_key ASC")->fetchAll();

// Grade distribution
$gradeDist = $db->query("
    SELECT LEFT(grade,1) as letter, COUNT(*) as cnt
    FROM grades
    GROUP BY letter
    ORDER BY letter")->fetchAll();
$gradeMap = array_column($gradeDist, 'cnt', 'letter');

// Department summary
$deptStats = $db->query("
    SELECT u.department, COUNT(DISTINCT u.id) as students,
           COUNT(s.id) as submissions, AVG(g.score) as avg_score
    FROM users u
    LEFT JOIN submissions s ON u.id=s.student_id
    LEFT JOIN grades g ON u.id=g.student_id
    WHERE u.role='student' AND u.department IS NOT NULL AND u.department != ''
    GROUP BY u.department
    ORDER BY students DESC LIMIT 8")->fetchAll();

$pageTitle  = 'Reports & Analytics';
$activePage = 'reports.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Reports & Analytics</h2><p>System-wide academic performance overview.</p></div>
  <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
</div>

<!-- Summary Stats -->
<div class="stats-grid">
  <div class="stat-card blue"><div class="stat-icon"><i class="fas fa-user-graduate"></i></div><h3><?= $stats['students'] ?></h3><p>Students</p></div>
  <div class="stat-card purple"><div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div><h3><?= $stats['teachers'] ?></h3><p>Teachers</p></div>
  <div class="stat-card orange"><div class="stat-icon"><i class="fas fa-folder-open"></i></div><h3><?= $stats['submissions'] ?></h3><p>Total Submissions</p></div>
  <div class="stat-card green"><div class="stat-icon"><i class="fas fa-star"></i></div><h3><?= $stats['avg_score'] ?>%</h3><p>System Avg Score</p></div>
</div>

<div class="content-grid">
  <!-- Monthly Submissions Chart -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-chart-bar"></i> Monthly Submissions (Last 6 Months)</h3></div>
    <div class="card-body">
      <?php if (empty($monthly)): ?>
        <div class="empty-state"><i class="fas fa-chart-bar"></i><h3>No data yet</h3></div>
      <?php else:
        $maxCnt = max(array_column($monthly, 'cnt') ?: [1]);
      ?>
      <div style="display:flex;align-items:flex-end;gap:10px;height:160px;padding-bottom:8px;">
        <?php foreach ($monthly as $m):
          $pct = round(($m['cnt'] / $maxCnt) * 100);
        ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
          <span style="font-size:0.75rem;font-weight:600;color:var(--text-secondary);"><?= $m['cnt'] ?></span>
          <div style="width:100%;background:linear-gradient(180deg,var(--accent),var(--accent-dark));border-radius:6px 6px 0 0;height:<?= max(8,$pct*1.4) ?>px;transition:height 0.6s ease;"></div>
          <span style="font-size:0.7rem;color:var(--text-muted);text-align:center;"><?= $m['month'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Grade Distribution -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-chart-pie"></i> Grade Distribution</h3></div>
    <div class="card-body">
      <?php
      $gradeColors = ['A'=>'var(--green)','B'=>'var(--blue)','C'=>'var(--orange)','D'=>'var(--red)','F'=>'#ef4444'];
      $totalGrades = array_sum($gradeMap);
      foreach (['A','B','C','D','F'] as $letter):
        $cnt = $gradeMap[$letter] ?? 0;
        $pct = $totalGrades > 0 ? round(($cnt/$totalGrades)*100) : 0;
      ?>
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
        <div style="width:36px;height:36px;border-radius:10px;background:<?= $gradeColors[$letter] ?>22;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;color:<?= $gradeColors[$letter] ?>;flex-shrink:0;"><?= $letter ?></div>
        <div style="flex:1;">
          <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
            <span style="font-size:0.82rem;">Grade <?= $letter ?></span>
            <span style="font-size:0.82rem;color:var(--text-muted)"><?= $cnt ?> (<?= $pct ?>%)</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" data-width="<?= $pct ?>" style="background:<?= $gradeColors[$letter] ?>;"></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Top Students -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-header"><h3><i class="fas fa-trophy"></i> Top Performing Students</h3></div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($topStudents)): ?>
      <div class="empty-state"><i class="fas fa-trophy"></i><h3>No graded students yet</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Student</th><th>Department</th><th>Year</th><th>Graded</th><th>Best Score</th><th>Avg Score</th><th>Performance</th></tr></thead>
        <tbody>
        <?php foreach ($topStudents as $i => $s):
          $avg = round($s['avg_score'], 1);
          $color = $avg >= 80 ? 'var(--green)' : ($avg >= 60 ? 'var(--orange)' : 'var(--red)');
        ?>
        <tr>
          <td>
            <?php if ($i === 0): ?><span style="font-size:1.1rem;">🥇</span>
            <?php elseif ($i === 1): ?><span style="font-size:1.1rem;">🥈</span>
            <?php elseif ($i === 2): ?><span style="font-size:1.1rem;">🥉</span>
            <?php else: ?><span style="color:var(--text-muted);font-size:0.875rem;"><?= $i+1 ?></span>
            <?php endif; ?>
          </td>
          <td><strong style="font-size:0.875rem;"><?= clean($s['full_name']) ?></strong></td>
          <td style="color:var(--text-muted)"><?= clean($s['department'] ?: '—') ?></td>
          <td><?= clean($s['grade_level'] ?: '—') ?></td>
          <td><?= $s['graded'] ?></td>
          <td><strong><?= round($s['best'], 1) ?>%</strong></td>
          <td><strong style="color:<?= $color ?>"><?= $avg ?>%</strong></td>
          <td style="min-width:100px;">
            <div class="progress-bar">
              <div class="progress-fill" data-width="<?= $avg ?>" style="background:<?= $color ?>;"></div>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Department Summary -->
<?php if (!empty($deptStats)): ?>
<div class="card">
  <div class="card-header"><h3><i class="fas fa-building"></i> Department Summary</h3></div>
  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Department</th><th>Students</th><th>Submissions</th><th>Avg Score</th></tr></thead>
        <tbody>
        <?php foreach ($deptStats as $d):
          $avg = round($d['avg_score'] ?? 0, 1);
        ?>
        <tr>
          <td><strong><?= clean($d['department']) ?></strong></td>
          <td><?= $d['students'] ?></td>
          <td><?= $d['submissions'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="progress-bar" style="width:80px;"><div class="progress-fill" data-width="<?= $avg ?>"></div></div>
              <span style="font-size:0.875rem;font-weight:500;"><?= $avg ?>%</span>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Submission status summary -->
<div class="content-grid" style="margin-top:20px;">
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-tasks"></i> Submission Status Summary</h3></div>
    <div class="card-body">
      <?php
      $statuses = ['approved'=>[$stats['approved'],'var(--green)'],'pending'=>[$stats['pending'],'var(--orange)'],
                   'reviewed'=>[$db->query("SELECT COUNT(*) FROM submissions WHERE status='reviewed'")->fetchColumn(),'var(--blue)'],
                   'rejected'=>[$db->query("SELECT COUNT(*) FROM submissions WHERE status='rejected'")->fetchColumn(),'var(--red)']];
      $totalS = $stats['submissions'] ?: 1;
      foreach ($statuses as $label => [$cnt, $color]):
        $pct = round(($cnt/$totalS)*100);
      ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
        <div style="display:flex;align-items:center;gap:8px;">
          <div style="width:10px;height:10px;border-radius:50%;background:<?= $color ?>;"></div>
          <span style="text-transform:capitalize;font-size:0.875rem;"><?= $label ?></span>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
          <div class="progress-bar" style="width:80px;"><div class="progress-fill" data-width="<?= $pct ?>" style="background:<?= $color ?>;"></div></div>
          <strong style="font-size:0.875rem;min-width:24px;text-align:right;"><?= $cnt ?></strong>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3><i class="fas fa-info-circle"></i> Quick Stats</h3></div>
    <div class="card-body">
      <?php
      $quickStats = [
        ['label'=>'Total Users', 'value'=>($stats['students']+$stats['teachers']+1), 'icon'=>'fa-users', 'color'=>'var(--blue)'],
        ['label'=>'Grading Rate', 'value'=>($stats['submissions']>0 ? round(($stats['grades']/$stats['submissions'])*100).'%' : '0%'), 'icon'=>'fa-percentage', 'color'=>'var(--green)'],
        ['label'=>'Approval Rate', 'value'=>($stats['submissions']>0 ? round(($stats['approved']/$stats['submissions'])*100).'%' : '0%'), 'icon'=>'fa-check-circle', 'color'=>'var(--purple)'],
        ['label'=>'System Score', 'value'=>$stats['avg_score'].'%', 'icon'=>'fa-chart-line', 'color'=>'var(--orange)'],
      ];
      foreach ($quickStats as $qs):
      ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:var(--bg-primary);border-radius:10px;margin-bottom:8px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:34px;height:34px;border-radius:10px;background:<?= $qs['color'] ?>22;display:flex;align-items:center;justify-content:center;color:<?= $qs['color'] ?>;">
            <i class="fas <?= $qs['icon'] ?>" style="font-size:0.85rem;"></i>
          </div>
          <span style="font-size:0.875rem;"><?= $qs['label'] ?></span>
        </div>
        <strong style="font-family:'Sora',sans-serif;color:<?= $qs['color'] ?>;font-size:1rem;"><?= $qs['value'] ?></strong>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
