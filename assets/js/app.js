// ============================================
// EduPortfolio - Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- Dark/Light Theme Toggle ----
  const themeBtn = document.getElementById('themeToggle');
  const savedTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      const current = document.documentElement.getAttribute('data-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('theme', next);
      updateThemeIcon(next);
    });
  }

  function updateThemeIcon(theme) {
    if (themeBtn) {
      themeBtn.innerHTML = theme === 'dark'
        ? '<i class="fas fa-sun"></i>'
        : '<i class="fas fa-moon"></i>';
    }
  }

  // ---- Sidebar Toggle (Mobile) ----
  const hamburger = document.getElementById('hamburger');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (hamburger) {
    hamburger.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    });
  }
  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }

  // ---- File Upload Drag & Drop ----
  const uploadZone = document.getElementById('uploadZone');
  const fileInput = document.getElementById('fileInput');

  if (uploadZone && fileInput) {
    uploadZone.addEventListener('click', () => fileInput.click());

    uploadZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadZone.classList.add('drag-over');
    });

    uploadZone.addEventListener('dragleave', () => {
      uploadZone.classList.remove('drag-over');
    });

    uploadZone.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadZone.classList.remove('drag-over');
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        fileInput.files = files;
        showFilePreview(files[0]);
      }
    });

    fileInput.addEventListener('change', function () {
      if (this.files.length > 0) {
        showFilePreview(this.files[0]);
      }
    });

    function showFilePreview(file) {
      const preview = document.getElementById('filePreview');
      if (preview) {
        const size = (file.size / 1024 / 1024).toFixed(2);
        preview.innerHTML = `
          <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--accent-light);border-radius:10px;margin-top:12px;">
            <i class="fas fa-file" style="color:var(--accent-dark);font-size:1.5rem;"></i>
            <div>
              <strong style="display:block;font-size:0.9rem;">${file.name}</strong>
              <span style="font-size:0.8rem;color:var(--text-muted);">${size} MB</span>
            </div>
            <i class="fas fa-check-circle" style="margin-left:auto;color:var(--green);"></i>
          </div>`;
      }
    }
  }

  // ---- Auto-dismiss flash alerts ----
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-8px)';
      alert.style.transition = 'all 0.3s ease';
      setTimeout(() => alert.remove(), 300);
    }, 4000);
  });

  // ---- Simple Bar Charts (no library needed) ----
  const barCharts = document.querySelectorAll('.bar-chart');
  barCharts.forEach(chart => {
    const bars = chart.querySelectorAll('.bar');
    bars.forEach((bar, i) => {
      const val = bar.getAttribute('data-value') || 0;
      const max = bar.getAttribute('data-max') || 100;
      const pct = Math.min((val / max) * 100, 100);
      // Animate on load
      setTimeout(() => {
        bar.querySelector('.bar-fill').style.height = pct + '%';
      }, 100 + i * 80);
    });
  });

  // ---- Progress Bars Animation ----
  const fills = document.querySelectorAll('.progress-fill');
  fills.forEach(fill => {
    const target = fill.getAttribute('data-width') || '0';
    setTimeout(() => { fill.style.width = target + '%'; }, 300);
  });

  // ---- Confirm Delete ----
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      if (!confirm(this.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });

  // ---- Active nav highlight ----
  const currentPage = window.location.pathname.split('/').pop();
  document.querySelectorAll('.nav-item').forEach(item => {
    const href = item.getAttribute('href');
    if (href && href.includes(currentPage) && currentPage !== '') {
      item.classList.add('active');
    }
  });

});
