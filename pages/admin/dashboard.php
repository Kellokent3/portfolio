<?php
// pages/admin/dashboard.php
require_once '../../includes/config.php';
requireRole('admin');

$db = getDB();

$totalStudents  = $db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$totalTeachers  = $db->query("SELECT COUNT(*) FROM users WHERE role='teacher'")->fetchColumn();
$totalSubs      = $db->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
$pendingSubs    = $db->query("SELECT COUNT(*) FROM submissions WHERE status='pending'")->fetchColumn();
$totalGrades    = $db->query("SELECT COUNT(*) FROM grades")->fetchColumn();
$avgScore       = round($db->query("SELECT AVG(score) FROM grades")->fetchColumn() ?? 0, 1);

// Recent activity
$activity = $db->query("SELECT a.*, u.full_name, u.role FROM activity_log a LEFT JOIN users u ON a.user_id=u.id ORDER BY a.logged_at DESC LIMIT 8")->fetchAll();

// Recent users
$recentUsers = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Subs by type
$byType = $db->query("SELECT type, COUNT(*) as cnt FROM submissions GROUP BY type")->fetchAll();
$typeMap = array_column($byType, 'cnt', 'type');

$pageTitle  = 'Admin Dashboard';
$activePage = 'dashboard.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>👑 Admin Dashboard</h2><p>System overview and management center.</p></div>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
    <h3><?= $totalStudents ?></h3><p>Students</p>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
    <h3><?= $totalTeachers ?></h3><p>Teachers</p>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><i class="fas fa-folder"></i></div>
    <h3><?= $totalSubs ?></h3><p>Submissions</p>
    <div class="trend down"><i class="fas fa-clock"></i> <?= $pendingSubs ?> pending</div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
    <h3><?= $avgScore ?>%</h3><p>System Avg Score</p>
  </div>
</div>

<!-- Portfolio Breakdown + Activity -->
<div class="content-grid">
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-chart-pie"></i> Submissions Breakdown</h3></div>
    <div class="card-body">
      <?php
      $items = [
        ['label'=>'Assignments','key'=>'assignment','color'=>'var(--blue)','icon'=>'fa-file-alt'],
        ['label'=>'Projects',   'key'=>'project',   'color'=>'var(--purple)','icon'=>'fa-lightbulb'],
        ['label'=>'Certificates','key'=>'certificate','color'=>'var(--orange)','icon'=>'fa-award'],
      ];
      foreach ($items as $item):
        $cnt = $typeMap[$item['key']] ?? 0;
        $pct = $totalSubs > 0 ? round(($cnt/$totalSubs)*100) : 0;
      ?>
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
        <div style="width:40px;height:40px;border-radius:12px;background:var(--bg-primary);display:flex;align-items:center;justify-content:center;color:<?= $item['color'] ?>;flex-shrink:0;">
          <i class="fas <?= $item['icon'] ?>"></i>
        </div>
        <div style="flex:1;">
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
            <span style="font-size:0.875rem;font-weight:500;"><?= $item['label'] ?></span>
            <span style="font-size:0.875rem;color:var(--text-muted)"><?= $cnt ?> (<?= $pct ?>%)</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" data-width="<?= $pct ?>" style="background:<?= $item['color'] ?>;"></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
        <div style="padding:12px;background:var(--bg-primary);border-radius:10px;text-align:center;">
          <strong style="font-family:'Sora',sans-serif;font-size:1.2rem;"><?= $totalGrades ?></strong>
          <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Total Graded</p>
        </div>
        <div style="padding:12px;background:var(--bg-primary);border-radius:10px;text-align:center;">
          <strong style="font-family:'Sora',sans-serif;font-size:1.2rem;"><?= $pendingSubs ?></strong>
          <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Pending</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Activity Log -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-history"></i> Recent Activity</h3></div>
    <div class="card-body" style="padding:0;">
      <?php foreach ($activity as $a):
        $icons = ['login'=>'fa-sign-in-alt','logout'=>'fa-sign-out-alt','upload'=>'fa-upload','grade'=>'fa-star','system'=>'fa-cog'];
        $ic = $icons[$a['action']] ?? 'fa-dot-circle';
      ?>
      <div style="display:flex;align-items:center;gap:10px;padding:12px 20px;border-bottom:1px solid var(--border);">
        <div style="width:32px;height:32px;border-radius:10px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;color:var(--accent-dark);flex-shrink:0;">
          <i class="fas <?= $ic ?>" style="font-size:0.8rem;"></i>
        </div>
        <div style="flex:1;">
          <span style="font-size:0.85rem;font-weight:500;"><?= clean($a['full_name'] ?? 'System') ?></span>
          <span style="font-size:0.82rem;color:var(--text-secondary);"> — <?= clean($a['details'] ?: $a['action']) ?></span>
        </div>
        <small style="color:var(--text-muted);font-size:0.75rem;white-space:nowrap;"><?= date('M j, g:i A', strtotime($a['logged_at'])) ?></small>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Recent Users -->
<div class="card">
  <div class="card-header">
    <h3><i class="fas fa-users"></i> Recent Users</h3>
    <a href="users.php" class="btn btn-secondary btn-sm">Manage All</a>
  </div>
  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Joined</th></tr></thead>
        <tbody>
        <?php foreach ($recentUsers as $u): ?>
        <tr>
          <td><strong><?= clean($u['full_name']) ?></strong></td>
          <td style="color:var(--text-muted)"><?= clean($u['email']) ?></td>
          <td><span class="badge-role <?= $u['role'] ?>"><?= $u['role'] ?></span></td>
          <td><?= clean($u['department'] ?: '—') ?></td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
