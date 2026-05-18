<?php
// landing.php - Promotional page with top menu (4 items)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>EduPortfolio | Electronic Student Portfolio System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Inter', sans-serif;
      background: #f9fafc;
      color: #111827;
      line-height: 1.5;
    }
    :root {
      --primary: #3b4b8f;
      --primary-dark: #2c3a6e;
      --primary-light: #eef2ff;
      --secondary: #f97316;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-600: #4b5563;
      --gray-800: #1f2937;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }
    .container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 0 24px;
    }
    /* Navbar with menu */
    .navbar {
      background: white;
      box-shadow: var(--shadow-sm);
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(4px);
      background: rgba(255,255,255,0.95);
    }
    .nav-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      padding: 12px 24px;
      max-width: 1280px;
      margin: 0 auto;
      gap: 16px;
    }
    .logo-area {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .logo-circle {
      width: 48px;
      height: 48px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow-md);
      border: 2px solid #eef2ff;
      overflow: hidden;
    }
    .logo-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
    .logo-text h1 {
      font-family: 'Sora', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      background: linear-gradient(135deg, #3b4b8f, #6d5acf);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
    }
    .logo-text span {
      font-size: 0.7rem;
      color: var(--gray-600);
    }
    /* Desktop menu */
    .nav-menu {
      display: flex;
      align-items: center;
      gap: 28px;
      list-style: none;
    }
    .nav-menu li a {
      text-decoration: none;
      font-weight: 500;
      color: var(--gray-600);
      transition: 0.2s;
      font-size: 0.95rem;
    }
    .nav-menu li a:hover {
      color: var(--primary);
    }
    .nav-menu li a i {
      margin-right: 6px;
    }
    /* Right side: language switcher + login */
    .nav-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .lang-switch {
      display: flex;
      gap: 6px;
      background: var(--gray-100);
      padding: 4px 8px;
      border-radius: 40px;
    }
    .lang-btn {
      background: transparent;
      border: none;
      padding: 5px 12px;
      border-radius: 32px;
      font-weight: 600;
      font-size: 0.75rem;
      cursor: pointer;
      color: var(--gray-600);
    }
    .lang-btn.active {
      background: var(--primary);
      color: white;
    }
    .login-link {
      background: var(--primary);
      color: white;
      padding: 8px 18px;
      border-radius: 40px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.85rem;
      transition: 0.2s;
    }
    .login-link:hover {
      background: var(--primary-dark);
    }
    /* Hamburger for mobile */
    .hamburger {
      display: none;
      font-size: 1.6rem;
      cursor: pointer;
      color: var(--primary);
    }
    /* Hero & other sections (keep same as before) */
    .hero {
      padding: 80px 0 60px;
      background: linear-gradient(135deg, #f9fafc 0%, #eef2ff 100%);
    }
    .hero-grid {
      display: flex;
      align-items: center;
      gap: 48px;
      flex-wrap: wrap;
      justify-content: space-between;
    }
    .hero-content {
      flex: 1;
      min-width: 280px;
    }
    .hero-badge {
      background: var(--primary-light);
      color: var(--primary);
      display: inline-block;
      padding: 6px 14px;
      border-radius: 40px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 24px;
    }
    .hero-content h1 {
      font-size: 2.8rem;
      font-weight: 800;
      font-family: 'Sora', sans-serif;
      line-height: 1.2;
      margin-bottom: 20px;
    }
    .hero-content p {
      font-size: 1.1rem;
      color: var(--gray-600);
      margin-bottom: 32px;
    }
    .btn-primary, .btn-outline {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      border-radius: 40px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.2s;
    }
    .btn-primary {
      background: var(--primary);
      color: white;
    }
    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }
    .btn-outline {
      border: 2px solid var(--primary);
      color: var(--primary);
      background: transparent;
    }
    .btn-outline:hover {
      background: var(--primary);
      color: white;
    }
    .illustration-card {
      background: white;
      border-radius: 32px;
      padding: 32px;
      box-shadow: var(--shadow-lg);
      text-align: center;
    }
    .section {
      padding: 80px 0;
    }
    .section-title {
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      font-family: 'Sora', sans-serif;
      margin-bottom: 16px;
    }
    .section-desc {
      text-align: center;
      color: var(--gray-600);
      max-width: 700px;
      margin: 0 auto 48px;
    }
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 32px;
    }
    .feature-card {
      background: white;
      border-radius: 24px;
      padding: 28px;
      transition: 0.25s;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--gray-200);
    }
    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-lg);
    }
    .feature-icon {
      width: 56px;
      height: 56px;
      background: var(--primary-light);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 24px;
    }
    .feature-icon i {
      font-size: 28px;
      color: var(--primary);
    }
    .steps {
      display: flex;
      flex-wrap: wrap;
      gap: 32px;
      justify-content: center;
    }
    .step-item {
      flex: 1;
      min-width: 200px;
      text-align: center;
      background: white;
      padding: 28px 20px;
      border-radius: 32px;
      box-shadow: var(--shadow-sm);
    }
    .step-number {
      width: 48px;
      height: 48px;
      background: var(--primary);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.3rem;
      margin: 0 auto 20px;
    }
    .footer {
      background: #111827;
      color: #9ca3af;
      padding: 48px 0 24px;
      margin-top: 40px;
    }
    @media (max-width: 900px) {
      .nav-menu {
        display: none;
        width: 100%;
        flex-direction: column;
        background: white;
        padding: 20px;
        border-radius: 20px;
        box-shadow: var(--shadow-md);
      }
      .nav-menu.show {
        display: flex;
      }
      .hamburger {
        display: block;
      }
      .nav-container {
        flex-wrap: wrap;
      }
      .nav-right {
        margin-left: auto;
      }
    }
    @media (max-width: 640px) {
      .hero-content h1 { font-size: 2rem; }
      .nav-right { gap: 8px; }
      .login-link { padding: 6px 12px; font-size: 0.75rem; }
    }

    [data-translate] { transition: all 0.1s; }
    .footer {
    background: #111827;
    color: #9ca3af;
    padding: 48px 0 24px;
    margin-top: 60px;
  }
  .footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
  }
  .footer-col h4 {
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
    font-family: 'Sora', sans-serif;
  }
  .footer-col h4 i {
    margin-right: 8px;
    color: var(--primary-light, #eef2ff);
  }
  .footer-contact li, .footer-links li {
    list-style: none;
    margin-bottom: 12px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .footer-contact li i {
    width: 24px;
    color: var(--secondary, #f97316);
  }
  .footer-links li a {
    color: #9ca3af;
    text-decoration: none;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .footer-links li a:hover {
    color: white;
    transform: translateX(4px);
  }
  .social-icons {
    display: flex;
    gap: 16px;
    margin: 20px 0;
  }
  .social-icons a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    color: #9ca3af;
    font-size: 1.1rem;
    transition: 0.2s;
  }
  .social-icons a:hover {
    background: var(--primary, #3b4b8f);
    color: white;
    transform: translateY(-3px);
  }
  .footer-newsletter p {
    margin-top: 20px;
    font-size: 0.85rem;
    border-left: 2px solid var(--primary);
    padding-left: 12px;
  }
  .footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 24px;
    text-align: center;
    font-size: 0.8rem;
  }
  @media (max-width: 768px) {
    .footer-grid {
      gap: 32px;
    }
    .footer-col h4 {
      margin-bottom: 12px;
    }
  }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="nav-container">
    <div class="logo-area">
      <div class="logo-circle">
        <img src="saint babeth logo.webp" alt="Saint Babeth Logo" onerror="this.src='https://placehold.co/200x200?text=SB'">
      </div>
      <div class="logo-text">
        <h1>EduPortfolio</h1>
        <span>Saint Babeth Secondary</span>
      </div>
    </div>
    <!-- Desktop menu (4 items) -->
    <ul class="nav-menu" id="navMenu">
      <li><a href="#home"><i class="fas fa-home"></i> <span data-translate="menu_home">Home</span></a></li>
      <li><a href="#features"><i class="fas fa-star"></i> <span data-translate="menu_features">Features</span></a></li>
      <li><a href="#how-it-works"><i class="fas fa-cogs"></i> <span data-translate="menu_how">How it works</span></a></li>
      <li><a href="#contact"><i class="fas fa-envelope"></i> <span data-translate="menu_contact">Contact</span></a></li>
    </ul>
    <div class="nav-right">
      <div class="lang-switch" id="langSwitcher">
        <button class="lang-btn active" data-lang="en">EN</button>
        <button class="lang-btn" data-lang="fr">FR</button>
        <button class="lang-btn" data-lang="rw">RW</button>
      </div>
      <a href="index.php" class="login-link"><i class="fas fa-arrow-right-to-bracket"></i> <span data-translate="login_btn">Login</span></a>
      <div class="hamburger" id="hamburger">
        <i class="fas fa-bars"></i>
      </div>
    </div>
  </div>
</nav>

<main>
  <section class="hero" id="home">
    <div class="container hero-grid">
      <div class="hero-content">
        <div class="hero-badge" data-translate="badge">✨ Smart Portfolio Management</div>
        <h1 data-translate="hero_title" style="font-family: 'Times New Roman'; font-size: 100px;" >Electronic Student Portfolio System</h1>
        <p data-translate="hero_desc">Centralized digital platform for students, teachers & administrators. Track achievements, upload work, give feedback, and generate smart reports.</p>
        <div class="cta-buttons">
          <a href="index.php" class="btn-primary"><i class="fas fa-graduation-cap"></i> <span data-translate="get_started">Get Started</span></a>
          <a href="#features" class="btn-outline"><i class="fas fa-play-circle"></i> <span data-translate="explore_btn">Explore</span></a>
        </div>
      </div>
      <div class="hero-illustration">
        <div class="illustration-card">
          <i class="fas fa-laptop-code" style="font-size: 3.5rem; color: var(--primary);"></i>
          <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--secondary); margin-left: 12px;"></i>
          <p style="margin-top: 16px;" data-translate="hero_stat">1,200+ students · 45+ teachers</p>
        </div>
      </div>
    </div>
  </section>

  <div class="container" id="features">
    <div class="section">
      <h2 class="section-title" data-translate="features_title">What makes EduPortfolio powerful?</h2>
      <p class="section-desc" data-translate="features_desc">All-in-one solution to digitize student portfolios, feedback, and academic growth.</p>
      <div class="features-grid">
        <div class="feature-card"><div class="feature-icon"><i class="fas fa-user-graduate"></i></div><h3 data-translate="feature_student_title">For Students</h3><p data-translate="feature_student_desc">Upload projects, assignments & certificates. Track grades, read feedback, and build a lifelong portfolio.</p></div>
        <div class="feature-card"><div class="feature-icon"><i class="fas fa-chalkboard-user"></i></div><h3 data-translate="feature_teacher_title">For Teachers</h3><p data-translate="feature_teacher_desc">Grade submissions, leave personalized feedback, monitor student progress, and approve outstanding work.</p></div>
        <div class="feature-card"><div class="feature-icon"><i class="fas fa-chart-simple"></i></div><h3 data-translate="feature_admin_title">For Admins</h3><p data-translate="feature_admin_desc">Manage users, view analytics, generate reports, and ensure data integrity with secure dashboards.</p></div>
        <div class="feature-card"><div class="feature-icon"><i class="fas fa-shield-alt"></i></div><h3 data-translate="feature_secure_title">Secure & Reliable</h3><p data-translate="feature_secure_desc">Encrypted passwords, SQL injection protection, role‑based access & activity logs.</p></div>
        <div class="feature-card"><div class="feature-icon"><i class="fas fa-chart-line"></i></div><h3 data-translate="feature_analytics_title">Smart Reports</h3><p data-translate="feature_analytics_desc">Automatic performance trends, grade distribution, top students, department summaries.</p></div>
        <div class="feature-card"><div class="feature-icon"><i class="fas fa-mobile-alt"></i></div><h3 data-translate="feature_mobile_title">Fully Responsive</h3><p data-translate="feature_mobile_desc">Access from any device – desktop, tablet, or smartphone. Dark/light mode included.</p></div>
      </div>
    </div>
  </div>

  <div class="container" id="how-it-works">
    <div class="section">
      <h2 class="section-title" data-translate="workflow_title">How the system works</h2>
      <p class="section-desc" data-translate="workflow_desc">Simple 4‑step journey from registration to academic growth.</p>
      <div class="steps">
        <div class="step-item"><div class="step-number">1</div><i class="fas fa-user-plus fa-2x" style="color:var(--primary); margin-bottom: 12px;"></i><h3 data-translate="step1_title">Register / Login</h3><p data-translate="step1_desc">Students create profile, teachers & admins have dedicated portals.</p></div>
        <div class="step-item"><div class="step-number">2</div><i class="fas fa-cloud-upload-alt fa-2x" style="color:var(--primary); margin-bottom: 12px;"></i><h3 data-translate="step2_title">Upload Work</h3><p data-translate="step2_desc">Assignments, projects & certificates (PDF, DOC, ZIP, images).</p></div>
        <div class="step-item"><div class="step-number">3</div><i class="fas fa-star fa-2x" style="color:var(--primary); margin-bottom: 12px;"></i><h3 data-translate="step3_title">Review & Grade</h3><p data-translate="step3_desc">Teachers give scores, feedback, and change submission status.</p></div>
        <div class="step-item"><div class="step-number">4</div><i class="fas fa-chart-line fa-2x" style="color:var(--primary); margin-bottom: 12px;"></i><h3 data-translate="step4_title">Track & Grow</h3><p data-translate="step4_desc">Students monitor progress, admins generate reports and insights.</p></div>
      </div>
    </div>
  </div>

  <div class="container" style="margin-bottom: 60px;" id="contact">
    <div style="background: linear-gradient(120deg, var(--primary-light), white); border-radius: 48px; padding: 48px 32px; text-align: center;">
      <i class="fas fa-headset" style="font-size: 3rem; color: var(--primary); margin-bottom: 16px;"></i>
      <h2 data-translate="cta_title" style="font-size: 1.8rem;">Ready to transform your school’s portfolio management?</h2>
      <p data-translate="cta_desc" style="margin: 16px 0 24px;">Join Saint Babeth Secondary & start using modern e‑portfolio system today.</p>
      <a href="index.php" class="btn-primary" style="background: var(--secondary);"><i class="fas fa-rocket"></i> <span data-translate="cta_btn">Get started free</span></a>
    </div>
  </div>
</main>

<!-- ========== FOOTER SECTION (only this part) ========== -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Column 1: Contact info -->
      <div class="footer-col">
        <h4><i class="fas fa-address-card"></i> <span data-translate="footer_contact_title">Contact Us</span></h4>
        <ul class="footer-contact">
          <li><i class="fas fa-envelope"></i> saintbabeth@gmail.com</li>
          <li><i class="fas fa-phone-alt"></i> +250 788 949 416</li>
          <li><i class="fas fa-map-marker-alt"></i> <span data-translate="footer_location">Gicumbi, Rwanda</span></li>
          <li><i class="fas fa-clock"></i> <span data-translate="footer_hours">Mon-Fri: 8:00 AM – 5:00 PM</span></li>
        </ul>
      </div>

      <!-- Column 2: Quick Links -->
      <div class="footer-col">
        <h4><i class="fas fa-link"></i> <span data-translate="footer_links_title">Quick Links</span></h4>
        <ul class="footer-links">
          <li><a href="#home"><i class="fas fa-home"></i> <span data-translate="menu_home">Home</span></a></li>
          <li><a href="#features"><i class="fas fa-star"></i> <span data-translate="menu_features">Features</span></a></li>
          <li><a href="#how-it-works"><i class="fas fa-cogs"></i> <span data-translate="menu_how">How it works</span></a></li>
          <li><a href="index.php"><i class="fas fa-arrow-right-to-bracket"></i> <span data-translate="login_footer">Login Portal</span></a></li>
        </ul>
      </div>

      <!-- Column 3: Follow Us + extra -->
      <div class="footer-col">
        <h4><i class="fas fa-share-alt"></i> <span data-translate="footer_follow_title">Follow Us</span></h4>
        <div class="social-icons">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <div class="footer-newsletter">
          <p><i class="fas fa-graduation-cap"></i> <span data-translate="footer_tagline">Your future, digitally archived.</span></p>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; 2026 <span data-translate="footer_copyright">Saint Babeth Secondary School. All rights reserved.</span></p>
    </div>
  </div>
</footer>

<script>
  const translations = {
    en: {
      menu_home: "Home", menu_features: "Features", menu_how: "How it works", menu_contact: "Contact",
      login_btn: "Login", badge: "✨ Smart Portfolio Management",
      hero_title: "Electronic Student Portfolio System",
      hero_desc: "Centralized digital platform for students, teachers & administrators. Track achievements, upload work, give feedback, and generate smart reports.",
      get_started: "Get Started", explore_btn: "Explore", hero_stat: "1,200+ students · 45+ teachers",
      features_title: "What makes EduPortfolio powerful?", features_desc: "All-in-one solution to digitize student portfolios, feedback, and academic growth.",
      feature_student_title: "For Students", feature_student_desc: "Upload projects, assignments & certificates. Track grades, read feedback, and build a lifelong portfolio.",
      feature_teacher_title: "For Teachers", feature_teacher_desc: "Grade submissions, leave personalized feedback, monitor student progress, and approve outstanding work.",
      feature_admin_title: "For Admins", feature_admin_desc: "Manage users, view analytics, generate reports, and ensure data integrity with secure dashboards.",
      feature_secure_title: "Secure & Reliable", feature_secure_desc: "Encrypted passwords, SQL injection protection, role‑based access & activity logs.",
      feature_analytics_title: "Smart Reports", feature_analytics_desc: "Automatic performance trends, grade distribution, top students, department summaries.",
      feature_mobile_title: "Fully Responsive", feature_mobile_desc: "Access from any device – desktop, tablet, or smartphone. Dark/light mode included.",
      workflow_title: "How the system works", workflow_desc: "Simple 4‑step journey from registration to academic growth.",
      step1_title: "Register / Login", step1_desc: "Students create profile, teachers & admins have dedicated portals.",
      step2_title: "Upload Work", step2_desc: "Assignments, projects & certificates (PDF, DOC, ZIP, images).",
      step3_title: "Review & Grade", step3_desc: "Teachers give scores, feedback, and change submission status.",
      step4_title: "Track & Grow", step4_desc: "Students monitor progress, admins generate reports and insights.",
      cta_title: "Ready to transform your school’s portfolio management?", cta_desc: "Join Saint Babeth Secondary & start using modern e‑portfolio system today.", cta_btn: "Get started free",
      footer_links: "Quick Links", login_footer: "Login Portal"
    },
    fr: {
      menu_home: "Accueil", menu_features: "Fonctionnalités", menu_how: "Fonctionnement", menu_contact: "Contact",
      login_btn: "Connexion", badge: "✨ Gestion de Portefeuille", hero_title: "Système de Portfolio Électronique", hero_desc: "Plateforme numérique centralisée pour étudiants, enseignants et administrateurs.", get_started: "Commencer", explore_btn: "Explorer", hero_stat: "1 200+ étudiants · 45+ enseignants",
      features_title: "Pourquoi EduPortfolio ?", features_desc: "Solution tout-en-un pour numériser les portfolios.", feature_student_title: "Étudiants", feature_student_desc: "Déposez projets, devoirs et certificats.", feature_teacher_title: "Enseignants", feature_teacher_desc: "Notez, commentez et suivez les progrès.", feature_admin_title: "Administrateurs", feature_admin_desc: "Gérez les utilisateurs, rapports et sécurité.", feature_secure_title: "Sécurisé", feature_secure_desc: "Mots de passe chiffrés, protection SQL.", feature_analytics_title: "Rapports", feature_analytics_desc: "Tendances, répartition des notes.", feature_mobile_title: "Responsive", feature_mobile_desc: "Accessible sur tous les appareils.", workflow_title: "Fonctionnement", workflow_desc: "Processus simple en 4 étapes.", step1_title: "Inscription", step1_desc: "Créez votre profil.", step2_title: "Dépôt", step2_desc: "Téléversez vos travaux.", step3_title: "Évaluation", step3_desc: "Les enseignants notent.", step4_title: "Suivi", step4_desc: "Consultez vos progrès.", cta_title: "Prêt à transformer la gestion des portfolios ?", cta_desc: "Rejoignez Saint Babeth Secondary.", cta_btn: "Commencer gratuitement", footer_links: "Liens rapides", login_footer: "Portail de connexion"
    },
    rw: {
      menu_home: "Ahabanza", menu_features: "Ibiranga", menu_how: "Uko ikora", menu_contact: "Twandikire",
      login_btn: "Kwinjira", badge: "✨ Ububiko bw'ibyagezweho", hero_title: "Sisitema ya Portfolio Y'ikoranabuhanga", hero_desc: "Ikibanza cya digitale gikomatanyirijeho abanyeshuri, abarimu n'abaminisitiratori.", get_started: "Tangira", explore_btn: "Reba", hero_stat: "Abanyeshuri 1,200+ · Abarimu 45+",
      features_title: "EduPortfolio ikora iki?", features_desc: "Igisubizo kimwe cyuzuye.", feature_student_title: "Abanyeshuri", feature_student_desc: "Twika imishinga, amanota, ibyangombwa.", feature_teacher_title: "Abarimu", feature_teacher_desc: "Tangira amanota, ibitekerezo.", feature_admin_title: "Abaminisitiratori", feature_admin_desc: "Genga abakoresha, rapora.", feature_secure_title: "Umutekano", feature_secure_desc: "Ijambo ryibanga rihishwe, kurinda SQL.", feature_analytics_title: "Raporo", feature_analytics_desc: "Imyitwarire y'amanota.", feature_mobile_title: "Igikora kuri buri gikoresho", feature_mobile_desc: "Koresha kuri telefoni, tabuleti.", workflow_title: "Uko ikora", workflow_desc: "Intambwe 4.", step1_title: "Kwiyandikisha", step1_desc: "Shyiraho profile.", step2_title: "Gutwika", step2_desc: "Twika imikoro.", step3_title: "Gusuzuma", step3_desc: "Abarimu batanga amanota.", step4_title: "Gukurikirana", step4_desc: "Reba iterambere.", cta_title: "Uriteguye guhindura imiyoborere?", cta_desc: "Injira muri Saint Babeth Secondary.", cta_btn: "Tangira ku buntu", footer_links: "Amahitamo", login_footer: "Urubuga rwinjira"
    }
  };
  let currentLang = 'en';
  function setLanguage(lang) {
    currentLang = lang;
    document.querySelectorAll('[data-translate]').forEach(el => {
      const key = el.getAttribute('data-translate');
      if (translations[lang] && translations[lang][key]) el.innerText = translations[lang][key];
    });
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });
  }
  document.querySelectorAll('.lang-btn').forEach(btn => btn.addEventListener('click', () => setLanguage(btn.getAttribute('data-lang'))));
  setLanguage('en');
  // Mobile hamburger
  const hamburger = document.getElementById('hamburger');
  const navMenu = document.getElementById('navMenu');
  if (hamburger) {
    hamburger.addEventListener('click', () => navMenu.classList.toggle('show'));
  }
  // Extend your existing translations with footer keys (if not already present)
  if (typeof translations !== 'undefined') {
    const footerTranslations = {
      en: {
        footer_contact_title: "Contact Us",
        footer_location: "Gicumbi, Rwanda",
        footer_hours: "Mon-Fri: 8:00 AM – 5:00 PM",
        footer_links_title: "Quick Links",
        footer_follow_title: "Follow Us",
        footer_tagline: "Your future, digitally archived.",
        footer_copyright: "Saint Babeth Secondary School. All rights reserved."
      },
      fr: {
        footer_contact_title: "Contactez-nous",
        footer_location: "Gicumbi, Rwanda",
        footer_hours: "Lun-Ven: 8h00 – 17h00",
        footer_links_title: "Liens rapides",
        footer_follow_title: "Suivez-nous",
        footer_tagline: "Votre avenir, archivé numériquement.",
        footer_copyright: "Saint Babeth Secondary School. Tous droits réservés."
      },
      rw: {
        footer_contact_title: "Twandikire",
        footer_location: "Gicumbi, U Rwanda",
        footer_hours: "Kuwa Mbere–Kuwa Gatanu: 8h00 – 17h00",
        footer_links_title: "Amahitamo yihuse",
        footer_follow_title: "Dukurikire",
        footer_tagline: "Ejo hazaza hanyu, habitswe mu ikoranabuhanga.",
        footer_copyright: "Ishuri ryisumbuye rya Saint Babeth. Uburenganzira bwose burazigemwa."
      }
    };
    for (let lang in footerTranslations) {
      if (translations[lang]) Object.assign(translations[lang], footerTranslations[lang]);
      else translations[lang] = footerTranslations[lang];
    }
    // Re-apply current language to refresh footer text
    if (typeof currentLang !== 'undefined' && typeof setLanguage === 'function') {
      setLanguage(currentLang);
    } else if (typeof setLanguage === 'function') {
      setLanguage('en');
    }
  }
</script>
</body>
</html>