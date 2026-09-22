<?php
// includes/topbar.php
// Top Bar Contact & Social Links
require_once __DIR__ . '/functions.php';
$contact = upphl_contact_settings();
$phoneClean = preg_replace('/[^0-9+]/', '', $contact['phone'] ?? '+917084900009');
?>
<!-- Top Bar Contact & Social Info -->
<div class="top-bar">
  <div class="container top-bar-flex">
    <div class="top-bar-left">
      <a href="tel:<?= htmlspecialchars($phoneClean) ?>">
        <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($contact['phone'] ?? '+91 7084900009') ?>
      </a>
      <a href="mailto:<?= htmlspecialchars($contact['email'] ?? 'uphandballleague@gmail.com') ?>">
        <i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($contact['email'] ?? 'uphandballleague@gmail.com') ?>
      </a>
    </div>
    <div class="top-bar-right">
      <a href="<?= htmlspecialchars($contact['facebook'] ?? 'https://facebook.com/upprohandballleague') ?>" target="_blank" aria-label="Facebook">
        <i class="fa-brands fa-facebook-f"></i>
      </a>
      <a href="<?= htmlspecialchars($contact['instagram'] ?? 'https://instagram.com/upprohandballleague') ?>" target="_blank" aria-label="Instagram">
        <i class="fa-brands fa-instagram"></i>
      </a>
      <a href="<?= htmlspecialchars($contact['youtube'] ?? 'https://www.youtube.com/@sportscastindia') ?>" target="_blank" aria-label="Youtube">
        <i class="fa-brands fa-youtube"></i>
      </a>
    </div>
  </div>
</div>
