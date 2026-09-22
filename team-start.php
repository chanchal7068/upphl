<?php
// team-start.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Teams & Schedule - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/team-start.css',
);

// Load dynamic MVP Players
$mvpJsonPath = __DIR__ . '/uploads/mvp.json';
$frontendMvpPlayers = [];
if (file_exists($mvpJsonPath)) {
    $frontendMvpPlayers = json_decode(file_get_contents($mvpJsonPath), true) ?? [];
}

// Extract distinct seasons and days
$frontendMvpSeasons = [];
$frontendMvpDays = [];
foreach ($frontendMvpPlayers as $p) {
    $s = trim($p['season'] ?? '');
    if (!empty($s) && !in_array($s, $frontendMvpSeasons)) {
        $frontendMvpSeasons[] = $s;
    }
    $d = trim($p['matchDay'] ?? '');
    if (!empty($d) && !in_array($d, $frontendMvpDays)) {
        $frontendMvpDays[] = $d;
    }
}
rsort($frontendMvpSeasons);
natsort($frontendMvpDays);

// Helper for Initials
function getMvpInitials($name) {
    $parts = explode(' ', trim($name));
    $initials = '';
    foreach ($parts as $p) {
        if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
    }
    return substr($initials, 0, 2) ?: 'UP';
}

// Load dynamic Franchise Teams
$frontendTeams = upphl_get_teams(null, true);
$teamSeasons = upphl_get_team_seasons();

require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('team-start');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Teams & Player Stats';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Explore official franchise teams and standout MVP players of the UP Pro Handball League.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
  <!-- Hero -->
  <section class="teams-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
    <div class="container">
      <h1 id="teamsHeroTitle"><?= htmlspecialchars($heroTitle) ?></h1>
      <p id="teamsHeroDesc"><?= htmlspecialchars($heroSubtitle) ?></p>

      <!-- Top View Switcher: Franchise Teams vs Player Stats / MVPs -->
      <div class="view-switch-container">
        <button class="view-switch-btn active" id="tabTeamsBtn" onclick="switchTeamStartTab('teams')">
          <i class="fa-solid fa-shield-halved"></i> Franchise Teams (<span id="teamsCountBadge"><?= count($frontendTeams) ?></span>)
        </button>
        <button class="view-switch-btn" id="tabPlayersBtn" onclick="switchTeamStartTab('players')">
          <i class="fa-solid fa-trophy"></i> Player Stats & MVPs
        </button>
      </div>
    </div>
  </section>

  <!-- Main Teams & Stats Section -->
  <section class="teams-section">
    <div class="container">
      
      <!-- =========================================
           TAB 1: FRANCHISE TEAMS VIEW (DYNAMIC)
      ========================================== -->
      <div id="teamsView" class="tab-content active">
        <!-- Season Filter Bar for Teams if multiple seasons -->
        <?php if (!empty($teamSeasons)): ?>
        <div class="mvp-season-bar" id="teamSeasonBar" style="margin-bottom: 25px; justify-content: center;">
          <button type="button" class="mvp-season-btn active" onclick="filterFrontendTeamSeason('all', this)">
            <i class="fa-solid fa-layer-group"></i> All Seasons
          </button>
          <?php foreach ($teamSeasons as $sName): ?>
            <button type="button" class="mvp-season-btn" onclick="filterFrontendTeamSeason('<?= htmlspecialchars($sName, ENT_QUOTES) ?>', this)">
              <?= htmlspecialchars($sName) ?>
            </button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="teams-grid" id="teamsGrid">
          <?php if (!empty($frontendTeams)): ?>
            <?php foreach ($frontendTeams as $t): 
                $tId = htmlspecialchars($t['id'] ?? '');
                $tName = htmlspecialchars($t['name'] ?? '');
                $tSeason = htmlspecialchars($t['season'] ?? 'Season 1');
                $tPoster = !empty($t['posterImage']) ? htmlspecialchars($t['posterImage']) : (!empty($t['logo']) ? htmlspecialchars($t['logo']) : 'assets/images/teams/bhadohi-logo.jpeg');
            ?>
              <a href="team-details.php?team=<?= $tId ?>" class="team-card team-poster-card" data-season="<?= $tSeason ?>" title="<?= $tName ?>">
                <img src="<?= $tPoster ?>" alt="<?= $tName ?>" class="team-poster-img" />
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #64748b;">
              <i class="fa-solid fa-shield-halved" style="font-size: 3rem; margin-bottom: 12px; color: #cbd5e1; display: block;"></i>
              <p>No franchise teams listed yet. Check back soon!</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- =========================================
           TAB 2: PLAYER STATS & MVP STARS VIEW
      ========================================== -->
      <div id="playersView" class="tab-content">
        <div class="mvp-showcase-header">
          <h2><i class="fa-solid fa-award" style="color: var(--primary-orange);"></i> Top MVP Players & Stars</h2>
          <p>Standout match-winning performers and award winners from across UP Pro Handball League.</p>
        </div>

        <!-- MVP Filter Bar (Seasons + Days) -->
        <div class="mvp-filters-wrapper">
          <!-- Season Bar -->
          <div class="mvp-season-bar" id="mvpSeasonBar">
            <button type="button" class="mvp-season-btn active" onclick="filterFrontendMvpSeason('all', this)">
              <i class="fa-solid fa-layer-group"></i> All Seasons
            </button>
            <?php foreach ($frontendMvpSeasons as $seasonName): ?>
              <button type="button" class="mvp-season-btn" onclick="filterFrontendMvpSeason('<?= htmlspecialchars($seasonName, ENT_QUOTES) ?>', this)">
                <?= htmlspecialchars($seasonName) ?>
              </button>
            <?php endforeach; ?>
          </div>

          <!-- Day Filter Pills -->
          <div class="mvp-day-bar" id="mvpDayBar">
            <button type="button" class="mvp-day-pill active" onclick="filterFrontendMvpDay('all', this)">
              All Days
            </button>
            <?php foreach ($frontendMvpDays as $dayName): ?>
              <button type="button" class="mvp-day-pill" onclick="filterFrontendMvpDay('<?= htmlspecialchars($dayName, ENT_QUOTES) ?>', this)">
                <?= htmlspecialchars($dayName) ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- MVP Players Grid -->
        <div class="mvp-players-grid" id="mvpPlayersGrid">
          <?php if (!empty($frontendMvpPlayers)): ?>
            <?php foreach ($frontendMvpPlayers as $p): 
                $seasonVal   = htmlspecialchars($p['season'] ?? 'Season 1');
                $dayVal      = htmlspecialchars($p['matchDay'] ?? 'Day 1');
                $matchVal    = htmlspecialchars($p['matchNumber'] ?? 'Match 1');
                $pName       = htmlspecialchars($p['playerName'] ?? '');
                $tName       = htmlspecialchars($p['teamName'] ?? '');
                $tLogo       = htmlspecialchars($p['teamLogo'] ?? 'assets/images/teams/bhadohi-logo.jpeg');
                $tSlug       = htmlspecialchars($p['teamSlug'] ?? 'barbarik-warriors');
                $award       = htmlspecialchars($p['awardTitle'] ?? 'Player of the Match');
                $jersey      = htmlspecialchars($p['jerseyNumber'] ?? '');
                $position    = htmlspecialchars($p['position'] ?? 'Player');
                $goals       = htmlspecialchars($p['goals'] ?? '');
                $rating      = htmlspecialchars($p['rating'] ?? '');
                $photo       = $p['playerPhoto'] ?? '';
                $initials    = getMvpInitials($pName);
            ?>
              <div class="mvp-player-card" data-season="<?= strtolower($seasonVal) ?>" data-day="<?= strtolower($dayVal) ?>">
                <div class="mvp-card-top">
                  <div class="mvp-match-chip">
                    <i class="fa-solid fa-calendar-day" style="color: var(--primary-orange);"></i> <?= $dayVal ?> &bull; <?= $matchVal ?>
                  </div>
                  <?php if (!empty($jersey)): ?>
                    <div class="mvp-jersey"><?= (strpos($jersey, '#') === 0) ? $jersey : '#' . $jersey ?></div>
                  <?php endif; ?>
                </div>

                <div style="margin-bottom: 12px; text-align: center;">
                  <span class="mvp-award-badge badge-gold"><i class="fa-solid fa-trophy"></i> <?= $award ?></span>
                </div>

                <div class="mvp-avatar-wrap">
                  <div class="mvp-avatar-circle" style="border-color: #ea580c;">
                    <?php if (!empty($photo) && file_exists(__DIR__ . '/' . $photo)): ?>
                      <img src="<?= htmlspecialchars($photo) ?>" alt="<?= $pName ?>" />
                    <?php else: ?>
                      <svg viewBox="0 0 80 80" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="38" fill="#ea580c"/>
                        <text x="40" y="47" text-anchor="middle" fill="#ffffff" font-family="'Outfit', sans-serif" font-weight="800" font-size="24"><?= $initials ?></text>
                      </svg>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="mvp-player-info">
                  <h3><?= $pName ?></h3>
                  <span class="mvp-player-pos"><?= $position ?></span>
                  
                  <div class="mvp-team-pill" style="border-color: rgba(234, 88, 12, 0.35); background: rgba(234, 88, 12, 0.06);">
                    <img src="<?= $tLogo ?>" alt="<?= $tName ?>" /> <?= $tName ?>
                  </div>

                  <?php if (!empty($goals) || !empty($rating)): ?>
                    <div class="mvp-stats-row">
                      <?php if (!empty($goals)): ?>
                        <div class="mvp-stat-box">
                          <span class="stat-num"><?= $goals ?></span>
                          <span class="stat-lbl">Performance</span>
                        </div>
                      <?php endif; ?>
                      <?php if (!empty($rating)): ?>
                        <div class="mvp-stat-box">
                          <span class="stat-num"><?= $rating ?></span>
                          <span class="stat-lbl">Match Rating</span>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <a href="team-details.php?team=<?= $tSlug ?>" class="mvp-link-btn">
                    View Team Profile <i class="fa-solid fa-chevron-right"></i>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Empty State -->
        <div id="mvpEmptyState" style="display: <?= empty($frontendMvpPlayers) ? 'block' : 'none' ?>; text-align: center; padding: 50px 20px; background: #ffffff; border-radius: 16px; border: 1px dashed var(--border); margin-top: 20px;">
          <i class="fa-solid fa-trophy" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
          <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--navy); margin-bottom: 6px;">No MVP Players Found</h3>
          <p style="color: var(--text-light); font-size: 0.95rem;">No standout players have been awarded for the selected season or match day yet.</p>
        </div>

      </div>

    </div>
  </section>

  <!-- Start Schedule Timeline -->
  <section class="start-section">
    <div class="container">
      <h2 style="text-align: center; font-size: 2rem; color: var(--text-dark);">Season Launch Schedule</h2>
      
      <div class="timeline">
        
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <span>1st August 2026</span>
            <h4>Official Press Meet & Announcement Day</h4>
            <p>League board announces franchises, venues, and media partners in Lucknow.</p>
          </div>
        </div>

        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <span>4th August 2026</span>
            <h4>Player Trails & Scouting</h4>
            <p>Athletes from various districts participate in core fitness and skill trials.</p>
          </div>
        </div>

        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <span>6th August 2026</span>
            <h4>Player Auctions</h4>
            <p>Franchises bid and form official squads from the registered player drafts.</p>
          </div>
        </div>

        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <span>10th August 2026</span>
            <h4>Opening Match Kick-Off</h4>
            <p>Tournament opens with Barbarik Warriors taking on Ghaziabad Panthers.</p>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<script>
// Client-Side Team Season Filter
function filterFrontendTeamSeason(season, btn) {
  const s = season.toLowerCase();
  document.querySelectorAll('#teamSeasonBar .mvp-season-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  const cards = document.querySelectorAll('#teamsGrid .team-poster-card');
  let count = 0;
  cards.forEach(card => {
    const cSeason = (card.getAttribute('data-season') || '').toLowerCase();
    if (s === 'all' || cSeason === s) {
      card.style.display = 'block';
      count++;
    } else {
      card.style.display = 'none';
    }
  });
  const countBadge = document.getElementById('teamsCountBadge');
  if (countBadge) countBadge.textContent = count;
}

// Client-Side MVP Filter Handler
let currentMvpSeason = 'all';
let currentMvpDay = 'all';

function filterFrontendMvpSeason(season, btn) {
  currentMvpSeason = season.toLowerCase();
  document.querySelectorAll('#mvpSeasonBar .mvp-season-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  applyFrontendMvpFilters();
}

function filterFrontendMvpDay(day, btn) {
  currentMvpDay = day.toLowerCase();
  document.querySelectorAll('#mvpDayBar .mvp-day-pill').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  applyFrontendMvpFilters();
}

function applyFrontendMvpFilters() {
  const cards = document.querySelectorAll('#mvpPlayersGrid .mvp-player-card');
  let visibleCount = 0;
  cards.forEach(card => {
    const s = (card.getAttribute('data-season') || '').toLowerCase();
    const d = (card.getAttribute('data-day') || '').toLowerCase();
    
    const seasonMatch = (currentMvpSeason === 'all' || s === currentMvpSeason);
    const dayMatch = (currentMvpDay === 'all' || d === currentMvpDay);
    
    if (seasonMatch && dayMatch) {
      card.style.display = 'flex';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  const emptyState = document.getElementById('mvpEmptyState');
  if (emptyState) {
    emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
  }
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
