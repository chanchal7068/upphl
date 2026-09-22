<?php
// fixtures.php
$pageTitle = 'Match Fixtures - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/fixtures.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('fixtures');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Match Fixtures';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Stay updated with official match fixtures, match days, starting times, venue locations, live statuses, and historical scores.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
  <!-- Hero -->
  <section class="fixtures-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
    <div class="container">
      <h1><?= htmlspecialchars($heroTitle) ?></h1>
      <p><?= htmlspecialchars($heroSubtitle) ?></p>
    </div>
  </section>

  <!-- Matches Section -->
  <section class="fixtures-section">
    <div class="container">
      
      <!-- Filter Bar: Seasons & Statuses -->
      <div class="fixtures-filter-bar">
        <!-- Dynamic Season Tabs -->
        <div class="fixtures-season-tabs" id="fixturesSeasonTabs">
          <!-- Rendered dynamically via JS -->
        </div>

        <!-- Status Filter Pills -->
        <div class="fixtures-status-pills" id="fixturesStatusPills">
          <button type="button" class="status-pill-btn active" data-status="all">All Matches</button>
          <button type="button" class="status-pill-btn" data-status="upcoming">Upcoming</button>
          <button type="button" class="status-pill-btn" data-status="live">Live Matches</button>
          <button type="button" class="status-pill-btn" data-status="completed">Completed</button>
        </div>
      </div>

      <div class="fixtures-container" id="fixturesContainer">
        <!-- Dynamic Day-wise Fixture Cards Loaded via JS -->
        <div style="text-align: center; padding: 50px 20px; color: #6b7280;">
          <i class="fas fa-spinner fa-spin" style="font-size: 28px; color: var(--orange); margin-bottom: 12px; display: block;"></i>
          Loading match schedule...
        </div>
      </div>

    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let allFixtures = [];
  let availableSeasons = [];
  let activeSeason = '';
  let activeStatus = 'all';

  // 1. Fetch Fixtures from API
  async function loadFixtures() {
    try {
      const res = await fetch('api/fixtures.php');
      const data = await res.json();

      if (data.success) {
        allFixtures = data.fixtures || [];
        availableSeasons = data.seasons || [];

        // Check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const urlSeason = urlParams.get('season');
        const urlStatus = urlParams.get('status');

        if (urlSeason && availableSeasons.map(s => s.toLowerCase()).includes(urlSeason.toLowerCase())) {
          const match = availableSeasons.find(s => s.toLowerCase() === urlSeason.toLowerCase());
          activeSeason = match || urlSeason;
        } else if (availableSeasons.length > 0) {
          activeSeason = availableSeasons[0];
        } else {
          activeSeason = 'Season 2';
        }

        if (urlStatus && ['all', 'upcoming', 'live', 'completed'].includes(urlStatus.toLowerCase())) {
          activeStatus = urlStatus.toLowerCase();
        }

        renderSeasonTabs();
        setupStatusPills();
        renderFixtures();
      } else {
        renderError('Failed to load match fixtures.');
      }
    } catch (err) {
      console.error('Error fetching fixtures:', err);
      renderError('Unable to connect to match fixtures server.');
    }
  }

  // 2. Render Season Tabs
  function renderSeasonTabs() {
    const container = document.getElementById('fixturesSeasonTabs');
    if (!container) return;

    if (availableSeasons.length === 0) {
      container.style.display = 'none';
      return;
    }

    container.style.display = 'flex';
    container.innerHTML = availableSeasons.map(season => {
      const isActive = season.toLowerCase() === activeSeason.toLowerCase();
      return `<button type="button" class="fixtures-tab-btn ${isActive ? 'active' : ''}" data-season="${season}">
        ${season}
      </button>`;
    }).join('');

    container.querySelectorAll('.fixtures-tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const s = this.getAttribute('data-season');
        if (s !== activeSeason) {
          activeSeason = s;
          container.querySelectorAll('.fixtures-tab-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          updateUrl();
          renderFixtures();
        }
      });
    });
  }

  // 3. Setup Status Pills
  function setupStatusPills() {
    const pillsContainer = document.getElementById('fixturesStatusPills');
    if (!pillsContainer) return;

    pillsContainer.querySelectorAll('.status-pill-btn').forEach(btn => {
      if (btn.getAttribute('data-status') === activeStatus) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }

      btn.addEventListener('click', function() {
        const stat = this.getAttribute('data-status');
        if (stat !== activeStatus) {
          activeStatus = stat;
          pillsContainer.querySelectorAll('.status-pill-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          updateUrl();
          renderFixtures();
        }
      });
    });
  }

  function updateUrl() {
    const url = new URL(window.location);
    url.searchParams.set('season', activeSeason);
    if (activeStatus !== 'all') {
      url.searchParams.set('status', activeStatus);
    } else {
      url.searchParams.delete('status');
    }
    window.history.pushState({}, '', url);
  }

  // 4. Render Day-wise Grouped Fixtures
  function renderFixtures() {
    const container = document.getElementById('fixturesContainer');
    if (!container) return;

    // Filter by season
    let matches = allFixtures.filter(f => 
      !activeSeason || (f.season && f.season.toLowerCase() === activeSeason.toLowerCase())
    );

    // Filter by status
    if (activeStatus !== 'all') {
      matches = matches.filter(f => 
        (f.computedStatus && f.computedStatus.toLowerCase() === activeStatus)
      );
    }

    if (matches.length === 0) {
      container.innerHTML = `
        <div style="text-align: center; padding: 60px 20px; background: rgba(255,255,255,0.9); border-radius: 16px; border: 1px dashed #cbd5e1; margin: 20px auto; max-width: 600px;">
          <i class="fa-regular fa-calendar-xmark" style="font-size: 38px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
          <h3 style="margin: 0 0 6px; color: #1e293b; font-size: 18px;">No matches found</h3>
          <p style="color: #64748b; margin: 0; font-size: 14px;">No ${activeStatus !== 'all' ? activeStatus : ''} matches scheduled for ${escapeHtml(activeSeason)}.</p>
        </div>
      `;
      return;
    }

    // Group matches by Day & Date
    const dayGroups = {};
    matches.forEach(f => {
      const dayKey = (f.matchDay && f.matchDay.trim() !== '') ? f.matchDay.trim() : (f.matchDate || 'Upcoming');
      if (!dayGroups[dayKey]) {
        dayGroups[dayKey] = {
          dayTitle: f.matchDay || 'Day',
          matchDate: f.matchDate,
          matches: []
        };
      }
      dayGroups[dayKey].matches.push(f);
    });

    let html = '';

    Object.keys(dayGroups).forEach(dayKey => {
      const group = dayGroups[dayKey];
      const formattedDate = formatDateHeading(group.matchDate);
      const matchCount = group.matches.length;

      html += `
        <div class="fixture-day-group">
          <!-- Day Header Block -->
          <div class="fixture-day-header">
            <div class="day-header-left">
              <span class="day-badge"><i class="fa-solid fa-calendar-day"></i> ${escapeHtml(group.dayTitle)}</span>
              <span class="day-date-text"><i class="fa-regular fa-calendar" style="color: var(--orange); margin-right: 6px;"></i> ${formattedDate}</span>
            </div>
            <div class="day-header-right">
              <span class="day-match-count">${matchCount} ${matchCount === 1 ? 'Match' : 'Matches'}</span>
            </div>
          </div>

          <!-- Matches within this Day -->
          <div class="day-matches-list">
      `;

      group.matches.forEach((f, idx) => {
        const cStatus = f.computedStatus || 'Upcoming';
        const matchNumLabel = f.matchNumber || `Match ${idx + 1}`;
        const matchTitleText = f.matchTitle ? `${matchNumLabel} · ${f.matchTitle}` : matchNumLabel;

        const startTime = f.startTime || f.matchTime || '04:00 PM';
        const endTime = f.endTime || '';
        const timeRange = endTime ? `${startTime} – ${endTime}` : startTime;

        // Status Badge
        let statusBadgeHtml = '';
        if (cStatus === 'Completed') {
          statusBadgeHtml = `<span class="fixture-status-tag completed"><i class="fa-solid fa-circle-check"></i> Completed</span>`;
        } else if (cStatus === 'Live') {
          statusBadgeHtml = `<span class="fixture-status-tag live"><span class="pulse-dot"></span> LIVE</span>`;
        } else {
          statusBadgeHtml = `<span class="fixture-status-tag upcoming"><i class="fa-regular fa-clock"></i> Upcoming</span>`;
        }

        // Center Area (VS / Live Score / Final Score)
        let centerHtml = '';
        if (cStatus === 'Completed') {
          const scoreText = (f.team1Score !== null && f.team1Score !== '' && f.team2Score !== null && f.team2Score !== '')
            ? `${f.team1Score} - ${f.team2Score}`
            : 'Completed';
          
          centerHtml = `
            <div class="match-versus">
              <span class="match-score">${scoreText}</span>
              <span class="score-badge">Final Score</span>
              <span class="match-venue"><i class="fa-solid fa-location-dot" style="color: var(--orange); margin-right: 4px;"></i> ${escapeHtml(f.stadium || 'K.D. Singh Babu Stadium, Lucknow')}</span>
            </div>
          `;
        } else if (cStatus === 'Live') {
          const liveScoreText = (f.team1Score !== null && f.team1Score !== '' && f.team2Score !== null && f.team2Score !== '')
            ? `${f.team1Score} - ${f.team2Score}`
            : 'LIVE';
          
          centerHtml = `
            <div class="match-versus">
              <span class="match-score" style="color: #dc2626;">${liveScoreText}</span>
              <span class="badge-live-pulse" style="margin-bottom: 8px;"><span class="pulse-dot"></span> In Progress</span>
              <span class="match-venue"><i class="fa-solid fa-location-dot" style="color: var(--orange); margin-right: 4px;"></i> ${escapeHtml(f.stadium || 'K.D. Singh Babu Stadium, Lucknow')}</span>
            </div>
          `;
        } else {
          centerHtml = `
            <div class="match-versus">
              <span class="vs-badge">VS</span>
              <span class="match-time"><i class="fa-regular fa-clock" style="color: var(--orange); margin-right: 6px; font-size: 0.95em;"></i> ${escapeHtml(timeRange)}</span>
              <span class="match-venue"><i class="fa-solid fa-location-dot" style="color: var(--orange); margin-right: 4px;"></i> ${escapeHtml(f.stadium || 'K.D. Singh Babu Stadium, Lucknow')}</span>
            </div>
          `;
        }

        html += `
          <div class="fixture-match-wrapper">
            <!-- Sub header inside match card -->
            <div class="match-top-bar">
              <div class="match-stage-info">
                <span class="match-num-pill">${escapeHtml(matchNumLabel)}</span>
                <span class="match-stage-title">${escapeHtml(f.matchTitle || 'League Stage')}</span>
              </div>
              <div class="match-top-right">
                <span class="match-time-chip"><i class="fa-solid fa-clock"></i> ${escapeHtml(timeRange)}</span>
                ${statusBadgeHtml}
              </div>
            </div>

            <!-- Match Clash Card -->
            <div class="match-card">
              <div class="match-team team-left">
                <div class="fixture-team-logo">
                  <img src="${escapeHtml(f.team1Logo || 'assets/images/teams/bhadohi-logo.jpeg')}" alt="${escapeHtml(f.team1Name)}" onerror="this.src='assets/images/teams/bhadohi-logo.jpeg'" />
                </div>
                <span class="fixture-team-name">${escapeHtml(f.team1Name)}</span>
              </div>

              ${centerHtml}

              <div class="match-team team-right">
                <div class="fixture-team-logo">
                  <img src="${escapeHtml(f.team2Logo || 'assets/images/teams/ghaziabad-logo.jpeg')}" alt="${escapeHtml(f.team2Name)}" onerror="this.src='assets/images/teams/ghaziabad-logo.jpeg'" />
                </div>
                <span class="fixture-team-name">${escapeHtml(f.team2Name)}</span>
              </div>
            </div>
          </div>
        `;
      });

      html += `
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  function formatDateHeading(dateStr) {
    if (!dateStr) return 'Date To Be Announced';
    const d = new Date(dateStr + 'T00:00:00');
    if (isNaN(d.getTime())) return dateStr;
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    return d.toLocaleDateString('en-US', options);
  }

  function renderError(msg) {
    const container = document.getElementById('fixturesContainer');
    if (container) {
      container.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #ef4444; background: #fef2f2; border-radius: 12px; border: 1px solid #fecaca;">
          <i class="fas fa-exclamation-circle" style="margin-right:8px;"></i> ${msg}
        </div>
      `;
    }
  }

  function escapeHtml(text) {
    const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
    return String(text || '').replace(/[&<>"']/g, m => map[m]);
  }

  // Load fixtures
  loadFixtures();
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
