<?php
// team-details.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Franchise Teams & Squads - UP Pro Handball League';
$extraCss = array (
);
require_once __DIR__ . '/includes/header.php';
?>

<main>
<!-- Dedicated Team Profile Hero -->
      <section class="dedicated-team-hero">
        <div class="container">
          <div class="team-hero-breadcrumb">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <span>/</span>
            <a href="team-start.php">Franchise Teams</a>
            <span>/</span>
            <span id="breadcrumbTeamName">Team Details</span>
          </div>

          <h1 id="heroTeamTitle">Franchise Team Details</h1>
          <p id="heroTeamDesc">Official squad, coaching staff, and management of the UP Pro Handball League.</p>

          <!-- 6 Team Selection Pill Tabs -->
          <div class="team-tab-selector" id="teamTabSelector">
            <!-- Populated via JS -->
          </div>
        </div>
      </section>

      <!-- Main Dedicated Team Content Section -->
      <section class="dedicated-team-section">
        <div class="container" id="dedicatedTeamContainer">
          <!-- Dynamic Content Rendered via JS -->
        </div>
      </section>
</main>

<script>
window.UPPHL_TEAMS_DATA = <?= json_encode(upphl_get_teams(null, false), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
