<?php
// index.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/player-registration.css',
  1 => 'assets/css/player-profile.css',
);
$showLoader = true;

// Load dynamic Winner & Runner-Up slides
$winnersJsonPath = __DIR__ . '/uploads/winners.json';
$homeWinners = [];
if (file_exists($winnersJsonPath)) {
    $homeWinners = json_decode(file_get_contents($winnersJsonPath), true) ?? [];
}
$homeWinners = array_values(array_filter($homeWinners, fn($w) => ($w['status'] ?? 'Active') === 'Active'));

// Load dynamic Announcement Ticker items
$homeAnnouncements = upphl_get_announcements(true);

// Load dynamic Home Stats Counter
$homeStats = upphl_get_home_stats();

// Load dynamic Latest Season Standings
$standingsSeasons = upphl_get_standings_seasons();
$activeSeasonForHome = !empty($standingsSeasons) ? $standingsSeasons[0] : 'Season 2';
$homeStandings = upphl_get_standings($activeSeasonForHome);

require_once __DIR__ . '/includes/header.php';
?>

<main>
<?php if (!empty($homeAnnouncements)): ?>
<!-- Dynamic Marquee Announcement Ticker Strip -->
<div class="announcement-strip-wrapper" id="announcementTicker">
  <div class="announcement-strip-container">
    <div class="announcement-badge">
      <span class="badge-pulse"><i class="fa-solid fa-bullhorn"></i></span>
      <span class="badge-text">ANNOUNCEMENTS</span>
    </div>
    <div class="announcement-marquee-track">
      <div class="announcement-marquee-content">
        <?php foreach ($homeAnnouncements as $ann): ?>
          <div class="announcement-item">
            <span class="ann-tag"><?= htmlspecialchars($ann['badgeText'] ?? 'ANNOUNCEMENT') ?></span>
            <span class="ann-text"><?= htmlspecialchars($ann['text'] ?? '') ?></span>
            <?php if (!empty($ann['linkUrl'])): ?>
              <a href="<?= htmlspecialchars($ann['linkUrl']) ?>" class="ann-link" <?= (!empty($ann['openInNewTab'])) ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                <?= !empty($ann['linkText']) ? htmlspecialchars($ann['linkText']) : 'View Details' ?>
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </a>
            <?php endif; ?>
          </div>
          <span class="ann-separator">✦</span>
        <?php endforeach; ?>
      </div>
      <!-- Duplicate content for seamless continuous infinite marquee loop -->
      <div class="announcement-marquee-content" aria-hidden="true">
        <?php foreach ($homeAnnouncements as $ann): ?>
          <div class="announcement-item">
            <span class="ann-tag"><?= htmlspecialchars($ann['badgeText'] ?? 'ANNOUNCEMENT') ?></span>
            <span class="ann-text"><?= htmlspecialchars($ann['text'] ?? '') ?></span>
            <?php if (!empty($ann['linkUrl'])): ?>
              <a href="<?= htmlspecialchars($ann['linkUrl']) ?>" class="ann-link" <?= (!empty($ann['openInNewTab'])) ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                <?= !empty($ann['linkText']) ? htmlspecialchars($ann['linkText']) : 'View Details' ?>
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </a>
            <?php endif; ?>
          </div>
          <span class="ann-separator">✦</span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

      <!-- Hero Section -->
      <section class="hero-section">
        <div class="hero-bg"></div>

        <div class="container hero-content">
          <span class="hero-badge">
            <i class="fa-solid fa-fire"></i>
            SEASON 2 • UP PRÓ HANDBALL
          </span>

          <h1>
            UTTAR PRADESH PRO
            <span>HANDBALL LEAGUE</span>
          </h1>

          <p>
            Experience the thrill, energy, and passion of the biggest handball
            tournament in Uttar Pradesh. Stay tuned for match updates, team
            stats, and registration details!
          </p>

          <div class="hero-actions">
            <a href="fixtures.php" class="btn btn-primary">
              View Fixtures
              <i class="fa-solid fa-arrow-right"></i>
            </a>

            <a href="league-details.php" class="btn btn-outline">
              League Details
            </a>
          </div>
        </div>

        <div class="hero-shape shape-one"></div>
        <div class="hero-shape shape-two"></div>
      </section>

      <!-- Stats Counter Section (Dynamic from Admin Data) -->
      <section class="stats-section">
        <div class="container">
          <div class="stats-grid">
            <?php foreach ($homeStats as $st): 
              $target = htmlspecialchars($st['target'] ?? '0');
              $suffix = htmlspecialchars($st['suffix'] ?? '');
              $decimals = (int)($st['decimals'] ?? 0);
              $label = htmlspecialchars($st['label'] ?? '');
            ?>
              <div class="stat-card">
                <div class="stat-number" data-target="<?= $target ?>" data-suffix="<?= $suffix ?>" data-decimals="<?= $decimals ?>">
                  <span class="counter-num">0</span><span class="counter-suffix"><?= $suffix ?></span>
                </div>
                <div class="stat-label"><?= $label ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- Leadership Messages -->
      <section class="president-section">
        <div class="container">
          <div class="president-cards-wrapper">
            <!-- Message 1: Mrs. Seema Dwivedi Ji -->
            <div class="president-card">
              <div class="president-img">
                <img src="assets/images/league-details/mrs-seema.jpeg" alt="Mrs. Seema Dwivedi Ji" />
              </div>
              <div class="president-quote">
                <blockquote>
                  "Empowering youth sports and athletic talent across Uttar Pradesh is our core priority. The UP Pro Handball League provides a vibrant platform for budding players to achieve state, national, and international recognition. We are proud to support this transformative sports initiative."
                </blockquote>
                <cite>MRS. SEEMA DWIVEDI JI</cite>
                <span>Rajya Sabha, MP</span>
                <span>Chief Patron – UP Pro Handball League</span>
              </div>
            </div>

            <!-- Message 2: Dr. Anandeshwar Pandey -->
            <div class="president-card">
              <div class="president-img">
                <img src="assets/images/league-details/dr-anandeshawar.jpeg" alt="Dr. Anandeshwar Pandey" />
              </div>
              <div class="president-quote">
                <blockquote>
                  "Handball holds immense potential in India, and Uttar Pradesh is leading the way with grassroots talent. UP Pro Handball League is committed to providing world-class infrastructure, professional exposure, and career pathways for dedicated handball athletes."
                </blockquote>
                <cite>DR. ANANDESHWAR PANDEY</cite>
                <span>Council Member – Asian Handball Federation</span>
                <span>CEO – UP Pro Handball League</span>
              </div>
            </div>

            <!-- Message 3: Amit Pandey -->
            <div class="president-card">
              <div class="president-img">
                <img src="assets/images/league-details/amit-pandey.jpeg" alt="Amit Pandey" />
              </div>
              <div class="president-quote">
                <blockquote>
                  "Uttar Pradesh is full of raw athletic talent. Our goal with the UP Pro Handball League is to create a pathway where local athletes can transform into national handball icons. We thank our franchise owners, sponsors, and handball fans for making this dream possible."
                </blockquote>
                <cite>AMIT PANDEY</cite>
                <span>Founder & Managing Director</span>
                <span>UP Pro Handball League</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- =========================================
           WINNER & RUNNER-UP GLORY SLIDER SECTION
      ========================================== -->
      <?php if (!empty($homeWinners)): ?>
      <section class="winner-runner-section" id="winners-runners">
        <div class="winner-slider-outer">
          <div class="winner-slider-container" id="winnerSliderContainer">
            <button class="carousel-btn winner-prev-btn" id="winnerPrevBtn" aria-label="Previous Slide">
              <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="winner-slider-viewport" id="winnerSliderViewport">
              <div class="winner-slider-track" id="winnerSliderTrack">
                <?php foreach ($homeWinners as $idx => $win): 
                    $cat = $win['category'] ?? 'Winner';
                    $isWin = strtolower($cat) === 'winner';
                    $imgUrl = htmlspecialchars($win['imageUrl'] ?? 'assets/images/celebration.jpg');
                    $title = htmlspecialchars($win['title'] ?? '');
                    $season = htmlspecialchars($win['season'] ?? 'Season 1');
                ?>
                  <div class="winner-slide-card" data-index="<?= $idx ?>">
                    <div class="winner-slide-media">
                      <img src="<?= $imgUrl ?>" alt="<?= $title ?>" loading="lazy" class="winner-slide-img" />
                      <div class="winner-slide-overlay"></div>
                      
                      <div class="winner-card-badges">
                        <?php if ($isWin): ?>
                          <span class="winner-trophy-badge gold-badge">
                            <i class="fa-solid fa-crown"></i> CHAMPION
                          </span>
                        <?php else: ?>
                          <span class="winner-trophy-badge silver-badge">
                            <i class="fa-solid fa-medal"></i> RUNNER-UP
                          </span>
                        <?php endif; ?>

                        <span class="winner-season-chip">
                          <i class="fa-solid fa-tag"></i> <?= $season ?>
                        </span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <button class="carousel-btn winner-next-btn" id="winnerNextBtn" aria-label="Next Slide">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- =========================================
           MEET OUR TEAMS AUTOSLIDER SECTION
      ========================================== -->
      <section class="meet-teams-section" id="meet-our-teams">
        <div class="container">
          <div class="section-header">
            <h2>Meet Our Teams</h2>
            <p>
              UPPHL's 6 franchise teams showcase peak talent and competition.
              Click on any team to view their full squad and management.
            </p>
          </div>

          <!-- 6 Team Autosliding Carousel -->
          <div class="teams-carousel-container">
            <button
              class="carousel-btn teams-prev-btn"
              id="teamsPrevBtn"
              aria-label="Previous Team">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="teams-carousel-viewport" id="teamsViewport">
              <div class="teams-carousel-track" id="teamsCarouselTrack">
                <!-- Dynamically populated 6 team cards linking to team-details.php?team=... -->
              </div>
            </div>
            <button
              class="carousel-btn teams-next-btn"
              id="teamsNextBtn"
              aria-label="Next Team">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>

          <div class="view-more-container" style="margin-top: 25px">
            <a href="team-details.php" class="btn btn-outline"
              >View All Team Squads <i class="fa-solid fa-arrow-right"></i
            ></a>
          </div>
        </div>
      </section>

      <!-- =========================================
           LEAGUE STANDINGS PREVIEW SECTION (Points Table Connected)
      ========================================== -->
      <section class="standings-preview-section" id="league-standings">
        <div class="container">
          <div class="section-header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 10px;">
              <span style="background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);">
                <i class="fa-solid fa-trophy"></i> <?= htmlspecialchars($activeSeasonForHome) ?>
              </span>
            </div>
            <h2>League Standings</h2>
            <p>Check out the current points table &amp; top team rankings for <?= htmlspecialchars($activeSeasonForHome) ?>.</p>
          </div>

          <div class="compact-table-wrapper">
            <table class="compact-table">
              <thead>
                <tr>
                  <th class="center-align" style="width: 70px">Pos</th>
                  <th>Team</th>
                  <th class="center-align" style="width: 80px">Played</th>
                  <th class="center-align" style="width: 70px">Won</th>
                  <th class="center-align" style="width: 70px">Lost</th>
                  <th class="center-align" style="width: 75px">GD</th>
                  <th class="center-align" style="width: 85px">Points</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($homeStandings)): ?>
                  <tr>
                    <td colspan="7" class="center-align" style="padding: 35px; color: #94a3b8;">
                      <i class="fa-solid fa-table-list" style="font-size: 28px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                      No standings data recorded for <?= htmlspecialchars($activeSeasonForHome) ?> yet.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($homeStandings as $idx => $stRow): 
                    $pos = $idx + 1;
                    $isTop4 = $pos <= 4;
                    $teamLogo = !empty($stRow['teamLogo']) ? $stRow['teamLogo'] : 'assets/images/teams/bhadohi-logo.jpeg';
                    $gdVal = (int)($stRow['gd'] ?? 0);
                  ?>
                    <tr class="<?= $isTop4 ? 'qualifier-row' : '' ?>">
                      <td class="center-align" style="font-weight: 700; color: <?= $pos === 1 ? '#ea580c' : ($isTop4 ? '#0284c7' : '#94a3b8') ?>;">
                        <?php if ($pos === 1): ?>
                          <i class="fa-solid fa-crown" style="color: #eab308; font-size: 11px; margin-right: 2px;"></i>
                        <?php endif; ?>
                        <?= $pos ?>
                      </td>
                      <td>
                        <div class="compact-team-meta" style="display: flex; align-items: center; gap: 10px;">
                          <?php if (!empty($stRow['teamLogo'])): ?>
                            <img src="<?= htmlspecialchars($stRow['teamLogo']) ?>" alt="<?= htmlspecialchars($stRow['teamName'] ?? '') ?>" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;" onerror="this.style.display='none'">
                          <?php endif; ?>
                          <span style="font-weight: 700; color: var(--black);"><?= htmlspecialchars($stRow['teamName'] ?? '') ?></span>
                        </div>
                      </td>
                      <td class="center-align" style="color: #475569; font-weight: 600;"><?= (int)($stRow['played'] ?? 0) ?></td>
                      <td class="center-align" style="color: #16a34a; font-weight: 600;"><?= (int)($stRow['won'] ?? 0) ?></td>
                      <td class="center-align" style="color: #dc2626; font-weight: 600;"><?= (int)($stRow['lost'] ?? 0) ?></td>
                      <td class="center-align" style="color: #64748b; font-weight: 600;"><?= ($gdVal > 0 ? '+' : '') . $gdVal ?></td>
                      <td class="center-align" style="font-weight: 800; font-size: 15px; color: var(--orange);"><?= (int)($stRow['pts'] ?? 0) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="view-more-container">
            <a href="point-table.php<?= !empty($activeSeasonForHome) ? '?season=' . urlencode($activeSeasonForHome) : '' ?>" class="btn btn-primary">
              View Full Standings &amp; Stats <i class="fa-solid fa-arrow-right" style="margin-left: 6px;"></i>
            </a>
          </div>
        </div>
      </section>

      <!-- News and Highlights -->
      <section class="news-section">
        <div class="container">
          <div class="section-header">
            <h2>Latest News & Updates</h2>
            <p>
              Catch the latest developments, draft summaries, and press
              releases.
            </p>
          </div>

          <div class="news-grid">
            <?php
            $homeNewsList = upphl_get_news(3, true);
            if (!empty($homeNewsList)):
              foreach ($homeNewsList as $newsItem):
                $newsImg = !empty($newsItem['imageUrl']) ? htmlspecialchars($newsItem['imageUrl']) : 'assets/images/ind1.jpg';
                $newsDate = htmlspecialchars($newsItem['date'] ?? '');
                $newsTitle = htmlspecialchars($newsItem['title'] ?? '');
                $newsDesc = htmlspecialchars($newsItem['description'] ?? '');
                $newsLink = !empty($newsItem['galleryLink']) ? htmlspecialchars($newsItem['galleryLink']) : 'gallery.php?cat=news';
            ?>
            <!-- Dynamic News Card -->
            <div class="news-card">
              <div class="news-image">
                <img src="<?php echo $newsImg; ?>" alt="<?php echo $newsTitle; ?>" onerror="this.src='assets/images/ind1.jpg'" />
              </div>
              <div class="news-content">
                <?php if (!empty($newsDate)): ?>
                  <span class="news-date"><?php echo $newsDate; ?></span>
                <?php endif; ?>
                <h3><?php echo $newsTitle; ?></h3>
                <p><?php echo $newsDesc; ?></p>
                <a href="<?php echo $newsLink; ?>" class="read-more-link"
                  >View Gallery <i class="fa-solid fa-arrow-right"></i
                ></a>
              </div>
            </div>
            <?php 
              endforeach;
            else:
            ?>
            <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #64748b;">
              <p>No news updates currently available.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- =========================================
           INSTAGRAM LIVE FEED SECTION (Elfsight & Showcase)
      ========================================== -->
      <section class="instagram-feed-section" id="instagram-feed">
        <div class="container">
          <div class="section-header">
            <span class="section-tag-pill"
              ><i class="fa-brands fa-instagram"></i> Live Feed</span
            >
            <h2>Follow @upprohandballleague</h2>
            <p>
              Catch exclusive behind-the-scenes, practice sessions, player
              spotlights, and live court action.
            </p>
          </div>

          <!-- 1. Elfsight Live Widget Embed Container -->
          <div class="elfsight-widget-wrapper" style="min-height: 200px;">
            <!-- Elfsight Instagram Feed | Untitled Instagram Feed -->
            <script src="https://elfsightcdn.com/platform.js" async></script>
            <div class="elfsight-app-fbba6e16-c06c-4f13-a04a-2b5bd6703448" data-elfsight-app-lazy></div>
          </div>
        </div>
      </section>

      <!-- =========================================
           ORGANIZATION & COLLABORATION SECTION
      ========================================== -->
      <section class="org-collaboration-section">
        <div class="container">
          <div class="section-header">
            <span class="section-tag-pill"><i class="fa-solid fa-medal"></i> Governance &amp; Sanction</span>
            <h2>Organization &amp; Affiliation</h2>
            <p>
              UP Pro Handball League is driven by dedicated sports institutions and the state's apex governing body.
            </p>
          </div>

          <div class="org-collaboration-grid">
            <!-- Card 1: Organized By -->
            <div class="org-card">
              <div class="org-card-badge organized-by">
                <i class="fa-solid fa-trophy"></i> Organized By
              </div>
              <div class="org-logo-wrapper">
                <img src="assets/images/sn-pandey-logo.jpeg" alt="S.N. Pandey Khel Sansthan Trust" class="org-logo-img" />
              </div>
              <div class="org-info">
                <h3>S.N. Pandey Khel Sansthan Trust</h3>
                <p>
                  Dedicated to empowering youth athletic talent, establishing top-tier tournament platforms, and promoting grassroots handball across Uttar Pradesh.
                </p>
              </div>
            </div>

            <!-- Card 2: Collaboration With -->
            <div class="org-card">
              <div class="org-card-badge collaboration-with">
                <i class="fa-solid fa-handshake"></i> In Collaboration With
              </div>
              <div class="org-logo-wrapper">
                <img src="assets/images/upha-logo.png" alt="U.P. Handball Association" class="org-logo-img" />
              </div>
              <div class="org-info">
                <h3>U.P. Handball Association</h3>
                <p>
                  The official apex governing body sanctioned to develop, regulate, and organize state-level championships and professional handball in Uttar Pradesh.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- =========================================
           OFFICIAL PARTNERS & SPONSORS AUTO-SLIDER
      ========================================== -->
      <section class="partners-section" id="partners">
        <div class="container">
          <div class="section-header">
            <span class="section-tag-pill"><i class="fa-solid fa-handshake"></i> Official Partners</span>
            <h2>Our Partners &amp; Sponsors</h2>
            <p>
              Proudly supported by leading national brands and sports organizations powering UP Pro Handball League Season-2.
            </p>
          </div>
        </div>

        <!-- Infinite Auto-Sliding Marquee Track -->
        <div class="partners-marquee-wrapper">
          <div class="partners-marquee-track">
            <?php
            $homePartners = upphl_get_partners(true);
            if (!empty($homePartners)):
              $repeatCount = count($homePartners) < 5 ? 3 : 2;
              for ($pass = 0; $pass < $repeatCount; $pass++):
                $isAriaHidden = $pass > 0 ? ' aria-hidden="true"' : '';
                foreach ($homePartners as $pt):
                  $pName = htmlspecialchars($pt['name'] ?? '');
                  $pType = htmlspecialchars($pt['partnerType'] ?? 'Official Partner');
                  $pLogo = !empty($pt['logoUrl']) ? htmlspecialchars($pt['logoUrl']) : '';
                  $pIcon = !empty($pt['iconClass']) ? htmlspecialchars($pt['iconClass']) : 'fa-solid fa-handshake';
                  $pColor = !empty($pt['iconColor']) ? htmlspecialchars($pt['iconColor']) : '#3b82f6';
            ?>
            <div class="partner-card"<?php echo $isAriaHidden; ?>>
              <span class="partner-type"><?php echo $pType; ?></span>
              <div class="partner-logo-box">
                <?php if (!empty($pLogo)): ?>
                  <img src="<?php echo $pLogo; ?>" alt="<?php echo $pName; ?>" loading="lazy" />
                <?php else: ?>
                  <i class="<?php echo $pIcon; ?> partner-icon" style="color: <?php echo $pColor; ?>;"></i>
                <?php endif; ?>
                <div class="partner-name"><?php echo $pName; ?></div>
              </div>
            </div>
            <?php 
                endforeach;
              endfor;
            endif;
            ?>
          </div>
        </div>
      </section>

      <!-- CTA Banner -->
      <section class="cta-section">
        <div class="container">
          <div class="cta-content">
            <h2>Be a part of UPPHL</h2>
            <p>
              Whether you're a player looking to compete, a brand wanting to
              sponsor, or a franchise owner, get in touch with us today.
            </p>
            <div class="cta-actions">
              <a href="contact.php" class="btn btn-white">Contact Us</a>
              <a href="league-details.php" class="btn btn-outline-white"
                >Register for Trials</a
              >
            </div>
          </div>
        </div>
      </section>
</main>

<script>
// ==========================================
// WINNERS & RUNNERS-UP GLORY SLIDER CONTROLLER (1 SLIDE AT A TIME)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('winnerSliderTrack');
    const viewport = document.getElementById('winnerSliderViewport');
    const prevBtn = document.getElementById('winnerPrevBtn');
    const nextBtn = document.getElementById('winnerNextBtn');
    const cards = document.querySelectorAll('#winnerSliderTrack .winner-slide-card');
    
    if (!track || !cards.length) return;

    let currentIndex = 0;
    const totalSlides = cards.length;
    let autoSlideTimer = null;

    function updateSliderPosition() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    function goToSlide(index) {
        currentIndex = (index + totalSlides) % totalSlides;
        updateSliderPosition();
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoSlide();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoSlide();
        });
    }

    function startAutoSlide() {
        stopAutoSlide();
        if (totalSlides > 1) {
            autoSlideTimer = setInterval(nextSlide, 4500);
        }
    }

    function stopAutoSlide() {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
    }

    function resetAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }

    const sliderContainer = document.getElementById('winnerSliderContainer');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }

    let startX = 0;
    let endX = 0;
    if (viewport) {
        viewport.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            stopAutoSlide();
        }, { passive: true });

        viewport.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            if (startX - endX > 40) {
                nextSlide();
            } else if (endX - startX > 40) {
                prevSlide();
            }
            startAutoSlide();
        }, { passive: true });
    }

    updateSliderPosition();
    startAutoSlide();
});
</script>

<script>
window.UPPHL_TEAMS_DATA = <?= json_encode(upphl_get_teams(null, true), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
