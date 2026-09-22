<?php
// includes/footer.php
// Global Footer Component
require_once __DIR__ . '/functions.php';
$contact = upphl_contact_settings();
?>
    <!-- Modern Footer -->
    <footer>
      <div class="container footer-new-layout">
        <!-- 1. Top Row: Circular Team Logos (Dynamic Backend Driven) -->
        <div class="footer-team-logos-row">
          <?php 
          $footerTeams = upphl_get_teams(null, true);
          if (empty($footerTeams)) {
              // Fallback
              $footerTeams = [
                  ['id' => 'barbarik-warriors', 'name' => 'Barbarik Warriors', 'footerLogo' => 'assets/images/teams/bhadohi-logo.jpeg'],
                  ['id' => 'ghaziabad-panthers', 'name' => 'Ghaziabad Panthers', 'footerLogo' => 'assets/images/teams/ghaziabad-logo.jpeg'],
                  ['id' => 'gorakhpur-rowdies', 'name' => 'Gorakhpur Rowdies', 'footerLogo' => 'assets/images/teams/gorakhapur-logo.jpeg'],
                  ['id' => 'kashi-kings', 'name' => 'Kashi Kings', 'footerLogo' => 'assets/images/teams/kashi-logo.jpeg'],
                  ['id' => 'mathura-brij-star', 'name' => 'Mathura Brij Star', 'footerLogo' => 'assets/images/teams/mathura-logo.jpeg'],
                  ['id' => 'noida-blasters', 'name' => 'Noida Blasters', 'footerLogo' => 'assets/images/teams/noida-logo.jpeg'],
              ];
          }
          foreach ($footerTeams as $fTeam): 
              $fLogo = !empty($fTeam['footerLogo']) ? htmlspecialchars($fTeam['footerLogo']) : (!empty($fTeam['logo']) ? htmlspecialchars($fTeam['logo']) : 'assets/images/teams/bhadohi-logo.jpeg');
              $fName = htmlspecialchars($fTeam['name'] ?? 'Team');
              $fSlug = htmlspecialchars($fTeam['id'] ?? 'team');
          ?>
            <a href="team-details.php?team=<?= $fSlug ?>" class="footer-team-round-logo" title="<?= $fName ?>">
              <img src="<?= $fLogo ?>" alt="<?= $fName ?>" />
            </a>
          <?php endforeach; ?>
        </div>

        <!-- 2. Second Row: Horizontal Navigation Links -->
        <nav class="footer-nav-menu">
          <a href="index.php">HOME</a>
          <a href="about.php">ABOUT</a>
          <a href="team-start.php">TEAMS &amp; STATS</a>
          <a href="point-table.php">POINTS TABLE</a>
          <a href="fixtures.php">FIXTURES</a>
          <a href="gallery.php">GALLERY</a>
          <a href="contact.php">CONTACT US</a>
        </nav>

        <!-- 3. Third Row: Center Brand Logo + Right Social Icons -->
        <div class="footer-brand-social-row">
          <div class="footer-center-logo">
            <a href="index.php">
              <img src="assets/images/logo.png" alt="UP Pro Handball League Logo" />
            </a>
          </div>
          <div class="footer-social-icons">
            <a href="<?= htmlspecialchars($contact['facebook'] ?? 'https://facebook.com/upprohandballleague') ?>" target="_blank" aria-label="Facebook" class="social-btn facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="<?= htmlspecialchars($contact['instagram'] ?? 'https://instagram.com/upprohandballleague') ?>" target="_blank" aria-label="Instagram" class="social-btn instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="<?= htmlspecialchars($contact['youtube'] ?? 'https://www.youtube.com/@sportscastindia') ?>" target="_blank" aria-label="YouTube" class="social-btn youtube"><i class="fa-brands fa-youtube"></i></a>
          </div>
        </div>
      </div>

      <!-- 4. Bottom Row -->
      <div class="footer-bottom">
        <div class="container footer-bottom-flex">
          <p>&copy; 2026 Uttar Pradesh Pro Handball League. All Rights Reserved.</p>
          <div class="footer-legal-links">
            <a href="privacy-policy.php">Privacy Policy</a>
            <span>•</span>
            <a href="terms-and-conditions.php">Terms &amp; Conditions</a>
            <span>•</span>
            <a href="refund-policy.php">Refund Policy</a>
          </div>
        </div>
      </div>
    </footer>

    <script src="assets/js/script.js"></script>
  </body>
</html>
