<?php
// league-details.php
$pageTitle = 'League Details - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/league-details.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('league-details');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'League Details';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Get to know the eligibility, age limits, rules, and structures governing our professional league matches.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- Hero -->
      <section class="details-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <h1><?= htmlspecialchars($heroTitle) ?></h1>
          <p><?= htmlspecialchars($heroSubtitle) ?></p>
        </div>
      </section>

      <!-- Player Eligibility & Fees -->
      <section class="details-section">
        <div class="container">
          <div class="details-grid">
            
            <div class="details-card">
              <h2><i class="fa-solid fa-id-card"></i> Player Eligibility</h2>
              <p>The league is open to professional and amateur handball athletes who satisfy the residency and training guidelines. Selection is based on state-level trials conducted prior to each season launch.</p>
              <ul class="details-list">
                <li><i class="fa-solid fa-circle-check"></i> Registration is open to players from all districts of Uttar Pradesh.</li>
                <li><i class="fa-solid fa-circle-check"></i> Minimum age requirement is 18 years as of the trial date.</li>
                <li><i class="fa-solid fa-circle-check"></i> Medical fitness clearance certificate is mandatory.</li>
              </ul>
            </div>

            <div class="details-card">
              <h2><i class="fa-solid fa-money-check-dollar"></i> Registrations & Fees</h2>
              <p>Candidates must register online and pay the trial fee to obtain a trial pass and scheduling slots. Detailed draft guidelines will be sent to selected players.</p>
              <ul class="details-list">
                <li><i class="fa-solid fa-circle-check"></i> Standard Trial Registration Fee: ₹1000 (Non-refundable).</li>
                <li><i class="fa-solid fa-circle-check"></i> Registrations close 7 days before trials begin.</li>
                <li><i class="fa-solid fa-circle-check"></i> Auction draft pool details are managed directly by franchise committees.</li>
              </ul>
            </div>

          </div>
        </div>
      </section>

      <!-- =====================================================
           LEAGUE MANAGEMENT COMMITTEE MEMBERS
      ===================================================== -->
      <section class="management-committee-section">
        <div class="container">
          <div class="committee-heading">
            <span>EXECUTIVE BOARD & OFFICIALS</span>
            <h2>League Management <strong>Committee</strong></h2>
            <p>Key leaders and dignitaries governing the UP Pro Handball League</p>
          </div>

          <div class="committee-grid">
            <?php
            $committeeMembers = upphl_get_committee(true);
            if (!empty($committeeMembers)):
              foreach ($committeeMembers as $cm):
                $cmImg = !empty($cm['imageUrl']) ? htmlspecialchars($cm['imageUrl']) : 'assets/images/league-details/amit-pandey.jpeg';
                $cmBadge = !empty($cm['badge']) ? htmlspecialchars($cm['badge']) : '';
                $cmName = htmlspecialchars($cm['name'] ?? '');
                $cmDesig = htmlspecialchars($cm['designation'] ?? '');
                $cmSubDesig = htmlspecialchars($cm['subDesignation'] ?? '');
            ?>
              <div class="committee-card">
                <div class="committee-card-img-wrapper">
                  <img src="<?= $cmImg ?>" alt="<?= $cmName ?>" loading="lazy" decoding="async" />
                  <?php if ($cmBadge): ?>
                    <div class="committee-badge"><?= $cmBadge ?></div>
                  <?php endif; ?>
                </div>
                <div class="committee-card-content">
                  <h3><?= $cmName ?></h3>
                  <?php if ($cmDesig): ?>
                    <p class="designation"><?= $cmDesig ?></p>
                  <?php endif; ?>
                  <?php if ($cmSubDesig): ?>
                    <p class="sub-designation"><?= $cmSubDesig ?></p>
                  <?php endif; ?>
                </div>
              </div>
            <?php
              endforeach;
            endif;
            ?>
          </div>
        </div>
      </section>

      <!-- =====================================================
           LEAGUE MANAGEMENT SECTION
      ===================================================== -->
      <section class="league-management">
        <div class="container">

          <div class="management-heading">
            <span>HOW WE OPERATE</span>
            <h2>League <strong>Management</strong></h2>
            <p>
              A professionally managed league built to provide players,
              teams and fans with a fair, exciting and organized handball
              experience.
            </p>
          </div>

          <div class="management-wrapper">

            <!-- Image -->
            <div class="management-image">

              <div class="image-frame">
                <img
                  src="assets/images/league-details-banner.jpg"
                  alt="UP Pro Handball League Management"
                />

                <div class="image-badge">
                  <i class="fa-solid fa-trophy"></i>
                  <div>
                    <strong>UPPHL</strong>
                    <small>Professional League</small>
                  </div>
                </div>
              </div>

            </div>


            <!-- Content -->
            <div class="management-content">

              <div class="management-item">
                <div class="management-icon">
                  <i class="fa-solid fa-people-group"></i>
                </div>

                <div>
                  <h3>Team Management</h3>
                  <p>
                    Every participating team is managed through structured
                    team registration, player selection and franchise
                    coordination.
                  </p>
                </div>
              </div>


              <div class="management-item">
                <div class="management-icon">
                  <i class="fa-solid fa-user-check"></i>
                </div>

                <div>
                  <h3>Player Selection</h3>
                  <p>
                    Players are selected through organized trials and
                    evaluation processes to ensure competitive and talented
                    squads.
                  </p>
                </div>
              </div>


              <div class="management-item">
                <div class="management-icon">
                  <i class="fa-solid fa-calendar-days"></i>
                </div>

                <div>
                  <h3>Fixtures & Scheduling</h3>
                  <p>
                    Matches are planned with proper scheduling, venues,
                    match officials and team coordination.
                  </p>
                </div>
              </div>


              <div class="management-item">
                <div class="management-icon">
                  <i class="fa-solid fa-scale-balanced"></i>
                </div>

                <div>
                  <h3>Fair Play & Discipline</h3>
                  <p>
                    The league follows transparent rules and maintains
                    discipline, sportsmanship and fair competition.
                  </p>
                </div>
              </div>


              <!-- Bottom stats -->

              <div class="management-stats">

                <div class="management-stat">
                  <strong>02</strong>
                  <span>League</span>
                </div>

                <div class="management-stat">
                  <strong>18</strong>
                  <span>Players</span>
                </div>

                <div class="management-stat">
                  <strong>60</strong>
                  <span>Minutes</span>
                </div>

              </div>

            </div>

          </div>

        </div>
      </section>

      <!-- Rules Accordion -->
      <section class="rules-section">
        <div class="container">
          <div class="rules-container">
            <h2>Official Rules of Handball</h2>
            
            <div class="accordion-item">
              <button class="accordion-header">
                Match Duration & Players <i class="fa-solid fa-chevron-down"></i>
              </button>
              <div class="accordion-content">
                <p>Each match consists of two halves of 30 minutes each, with a 10-minute halftime break. Each team consists of 7 court players (6 field players and 1 goalkeeper) plus substitutes.</p>
              </div>
            </div>

            <div class="accordion-item">
              <button class="accordion-header">
                Dribbling & 3-Step Rule <i class="fa-solid fa-chevron-down"></i>
              </button>
              <div class="accordion-content">
                <p>Players are allowed to take a maximum of 3 steps with the ball or hold it for up to 3 seconds before passing, dribbling, or shooting. Dribbling behaves similarly to basketball rules.</p>
              </div>
            </div>

            <div class="accordion-item">
              <button class="accordion-header">
                Goal Area (Crease Rules) <i class="fa-solid fa-chevron-down"></i>
              </button>
              <div class="accordion-content">
                <p>Only the goalkeeper is allowed inside the 6-meter goal area line. Players may jump into the area while shooting, provided they release the ball before landing.</p>
              </div>
            </div>

          </div>
        </div>
      </section>
</main>

<script>
      // Simple Accordion Handler
      document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
          const item = header.parentElement;
          item.classList.toggle('open');
        });
      });
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
