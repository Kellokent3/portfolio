<?php
// pages/student/upload.php
require_once '../../includes/config.php';
requireRole('student');

$db  = getDB();
$uid = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type        = $_POST['type'] ?? 'assignment';
    $subject     = trim($_POST['subject'] ?? '');

    if (empty($title)) {
        setFlash('error', 'Title is required.');
    } else {
        $fileName = null;
        $filePath = null;

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_TYPES)) {
                setFlash('error', 'File type not allowed. Use PDF, DOC, DOCX, ZIP, JPG, PNG, XLSX, PPTX.');
            } elseif ($_FILES['file']['size'] > MAX_FILE_SIZE) {
                setFlash('error', 'File too large. Maximum size is 10MB.');
            } else {
                $fileName = time() . '_' . preg_replace('/[^a-z0-9._-]/i', '_', $_FILES['file']['name']);
                $dest     = UPLOAD_PATH . 'assignments/' . $fileName;
                move_uploaded_file($_FILES['file']['tmp_name'], $dest);
                $filePath = 'uploads/assignments/' . $fileName;
            }
        }

        if (!isset($_SESSION['flash'])) { // no error set above
            $stmt = $db->prepare("INSERT INTO submissions (student_id, title, description, type, subject, file_path, file_name) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$uid, $title, $description, $type, $subject, $filePath, $fileName]);
            logActivity($uid, 'upload', "Uploaded: $title");
            setFlash('success', 'Work submitted successfully!');
            header('Location: submissions.php');
            exit();
        }
    }
}

$pageTitle  = 'Upload Work';
$activePage = 'upload.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div>
    <h2>Upload Work</h2>
    <p>Submit assignments, projects, or certificates to your portfolio.</p>
  </div>
</div>

<div class="content-grid">
  <!-- Upload Form -->
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-cloud-upload-alt"></i> New Submission</h3>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
          <label>Title <span style="color:var(--red)">*</span></label>
          <input type="text" name="title" placeholder="e.g. Web Design Final Project" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Type</label>
            <select name="type">
              <option value="assignment">📝 Assignment</option>
              <option value="project">💡 Project</option>
              <option value="certificate">🏆 Certificate</option>
            </select>
          </div>
          <div class="form-group">
            <label>Subject / Course</label>
            <input type="text" name="subject" placeholder="e.g. Web Development">
          </div>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" placeholder="Briefly describe your work..."></textarea>
        </div>

        <!-- Drag & Drop Upload Zone -->
        <div class="form-group">
          <label>Attach File (optional)</label>
          <div class="upload-zone" id="uploadZone">
            <i class="fas fa-cloud-upload-alt"></i>
            <h4>Drag & drop your file here</h4>
            <p>or click to browse — PDF, DOC, ZIP, JPG, PNG, XLSX up to 10MB</p>
          </div>
          <div id="filePreview"></div>
          <input type="file" id="fileInput" name="file" style="display:none"
                 accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png,.xlsx,.pptx">
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-paper-plane"></i> Submit Work
        </button>
      </form>
    </div>
  </div>

  <!-- Tips Card -->
  <div>
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header"><h3><i class="fas fa-lightbulb"></i> Submission Tips</h3></div>
      <div class="card-body">
        <div style="display:flex;flex-direction:column;gap:14px;">
          <?php
          $tips = [
            ['icon'=>'fa-file-alt','color'=>'var(--blue)','title'=>'Use clear titles','desc'=>'Name your work descriptively so teachers can identify it easily.'],
            ['icon'=>'fa-compress','color'=>'var(--green)','title'=>'Compress large files','desc'=>'Zip multiple files together before uploading.'],
            ['icon'=>'fa-tags','color'=>'var(--orange)','title'=>'Tag the subject','desc'=>'Always fill in the subject field for better organization.'],
            ['icon'=>'fa-check','color'=>'var(--purple)','title'=>'Review before submit','desc'=>'Double-check your file is complete before uploading.'],
          ];
          foreach ($tips as $t): ?>
          <div style="display:flex;gap:12px;align-items:flex-start;">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,0,0,0.04);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:<?= $t['color'] ?>;">
              <i class="fas <?= $t['icon'] ?>"></i>
            </div>
            <div>
              <strong style="font-size:0.875rem;"><?= $t['title'] ?></strong>
              <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;"><?= $t['desc'] ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3><i class="fas fa-info-circle"></i> Allowed Formats</h3></div>
      <div class="card-body">
        <?php
        $formats = [
          ['ext'=>'PDF','color'=>'var(--red)','icon'=>'fa-file-pdf'],
          ['ext'=>'DOC/DOCX','color'=>'var(--blue)','icon'=>'fa-file-word'],
          ['ext'=>'ZIP','color'=>'var(--orange)','icon'=>'fa-file-archive'],
          ['ext'=>'JPG/PNG','color'=>'var(--green)','icon'=>'fa-file-image'],
          ['ext'=>'XLSX','color'=>'var(--purple)','icon'=>'fa-file-excel'],
          ['ext'=>'PPTX','color'=>'var(--accent-dark)','icon'=>'fa-file-powerpoint'],
        ];
        ?>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
          <?php foreach ($formats as $f): ?>
          <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:var(--bg-primary);border-radius:8px;font-size:0.8rem;border:1px solid var(--border);">
            <i class="fas <?= $f['icon'] ?>" style="color:<?= $f['color'] ?>;"></i>
            <?= $f['ext'] ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
