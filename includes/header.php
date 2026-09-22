<?php
// includes/header.php
// Global Header Navigation & Meta Layout
require_once __DIR__ . '/functions.php';

$title = $pageTitle ?? 'UP Pro Handball League';
$extraCssFiles = $extraCss ?? [];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($title) ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/upphl-logo.png" />
    <link rel="shortcut icon" type="image/png" href="assets/images/upphl-logo.png" />
    <link rel="apple-touch-icon" href="assets/images/upphl-logo.png" />
    <!-- Google Fonts: Outfit, Archivo Black, Barlow Condensed, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/responsive.css" />
    <?php foreach ($extraCssFiles as $cssPath): ?>
      <link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>" />
    <?php endforeach; ?>
  </head>

  <body>
    <?php if (!empty($showLoader)): ?>
    <!-- =========================================
         PAGE LOADER - HANDBALL THROW & GOAL ANIMATION
    ========================================== -->
    <div class="page-loader" id="pageLoader">
      <div class="loader-content">
        <img src="assets/images/logo.png" alt="UP Pro Handball League" class="loader-logo" />
        <div class="handball-throw-arena">
          <div class="throw-track-integrated">
            <div class="throw-track-bg"></div>
            <div class="throw-track-fill" id="trackFill"></div>
            <div class="flying-handball" id="flyingBall">
              <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="ball-svg">
                <circle cx="15" cy="15" r="13" fill="#f97316" stroke="#fff" stroke-width="2" />
                <path d="M7 11 C11 15 11 19 7 23" stroke="#fff" stroke-width="1.8" stroke-linecap="round" />
                <path d="M23 11 C19 15 19 19 23 23" stroke="#fff" stroke-width="1.8" stroke-linecap="round" />
                <path d="M11 7 C15 11 19 11 23 7" stroke="#fff" stroke-width="1.8" stroke-linecap="round" />
                <path d="M11 23 C15 19 19 19 23 23" stroke="#fff" stroke-width="1.8" stroke-linecap="round" />
              </svg>
            </div>
          </div>
        </div>
        <div class="loader-percentage-box">
          <span class="loader-percent-num" id="loaderPercent">0%</span>
          <span class="loader-percent-status" id="loaderStatus">MATCH READY IN PROGRESS</span>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php require __DIR__ . '/topbar.php'; ?>

    <!-- Header Navigation -->
    <header>
      <div class="container header-container">
        <a href="index.php" class="logo">
          <img src="assets/images/logo.png" alt="UP Pro Handball League Logo" />
        </a>

        <nav id="nav-menu">
          <ul class="nav-links">
            <li><a href="index.php" class="<?= is_active_page('index.php') ?>">Home</a></li>
            <li><a href="about.php" class="<?= is_active_page('about.php') ?>">About</a></li>
            <li><a href="league-details.php" class="<?= is_active_page('league-details.php') ?>">League Details</a></li>
            <li><a href="fixtures.php" class="<?= is_active_page('fixtures.php') ?>">Fixtures</a></li>
            <li><a href="team-start.php" class="<?= is_active_page('team-start.php') ?>">Team &amp; Start</a></li>
            <li><a href="point-table.php" class="<?= is_active_page('point-table.php') ?>">Point Table</a></li>
            
            <!-- Player Dropdown -->
            <li class="dropdown <?= (is_active_page('player-registration.php') || is_active_page('players.php')) ? 'active' : '' ?>">
              <a href="#" class="dropdown-toggle">
                Player <i class="fa-solid fa-chevron-down nav-arrow"></i>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="player-registration.php">
                    <i class="fa-solid fa-user-plus"></i> Player Registration
                  </a>
                </li>
                <li>
                  <a href="players.php">
                    <i class="fa-solid fa-users"></i> Player Profile
                  </a>
                </li>
                <li>
                  <a href="#" onclick="openPlayerLoginModal(event)">
                    <i class="fa-solid fa-right-to-bracket"></i> Player Login
                  </a>
                </li>
              </ul>
            </li>

            <li><a href="gallery.php" class="<?= is_active_page('gallery.php') ?>">Gallery</a></li>
            <li><a href="contact.php" class="<?= is_active_page('contact.php') ?>">Contact Us</a></li>
            <li>
              <a href="live.php" class="nav-link-live <?= is_active_page('live.php') ?>">
                <span class="nav-live-dot"></span> UPPHL Live
              </a>
            </li>
            <li>
              <a href="player-registration.php" class="btn-nav-register">
                <i class="fa-solid fa-user-plus"></i> Register Now
              </a>
            </li>
          </ul>
        </nav>

        <button class="mobile-menu-btn" id="mobile-toggle" aria-label="Toggle Navigation Menu">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </header>
