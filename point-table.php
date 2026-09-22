<?php
// point-table.php
$pageTitle = 'Point Table - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/point-table.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('point-table');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Point Table';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Check the latest league standings, points, goals difference, and team forms for the UP Pro Handball League.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
  <!-- Point Table Hero Banner -->
  <section class="points-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
    <div class="container">
      <h1><?= htmlspecialchars($heroTitle) ?></h1>
      <p><?= htmlspecialchars($heroSubtitle) ?></p>
    </div>
  </section>

  <!-- Main Standings Section -->
  <section class="standings-section">
    <div class="container">
      
      <!-- Season Selector Tabs (Auto-populated from API) -->
      <div class="tabs-container" id="seasonTabsContainer">
        <!-- Rendered dynamically by JavaScript -->
      </div>

      <!-- Standings Explanatory Note -->
      <div class="table-meta-note">
        <p><span class="qualifier-dot"></span> Top 4 teams qualify for the semi-finals & playoffs.</p>
      </div>

      <!-- Standings Table -->
      <div class="table-responsive">
        <table class="standings-table">
          <thead>
            <tr>
              <th class="col-rank">Rank</th>
              <th class="col-team">Team</th>
              <th class="col-stat">P</th>
              <th class="col-stat">W</th>
              <th class="col-stat">D</th>
              <th class="col-stat">L</th>
              <th class="col-stat">GF</th>
              <th class="col-stat">GA</th>
              <th class="col-stat">GD</th>
              <th class="col-stat pts">PTS</th>
              <th class="col-form">Form</th>
            </tr>
          </thead>
          <tbody id="standingsTableBody">
            <!-- Dynamic Standings Rows Loaded via JS -->
            <tr>
              <td colspan="11" style="text-align:center; padding:40px; color:#6b7280;">
                <i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i> Loading standings...
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let allStandingsData = [];
  let availableSeasons = [];
  let currentSeason = '';

  // 1. Fetch standings data from API
  async function loadStandings() {
    try {
      const res = await fetch('api/standings.php');
      const data = await res.json();
      
      if (data.success) {
        allStandingsData = data.standings || [];
        availableSeasons = data.seasons || [];

        // Check if a season is requested in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const urlSeason = urlParams.get('season');

        if (urlSeason && availableSeasons.map(s => s.toLowerCase()).includes(urlSeason.toLowerCase())) {
          const match = availableSeasons.find(s => s.toLowerCase() === urlSeason.toLowerCase());
          currentSeason = match || urlSeason;
        } else if (availableSeasons.length > 0) {
          // Default to highest/latest season (e.g. Season 2)
          currentSeason = availableSeasons[0];
        } else {
          currentSeason = 'Season 2';
        }

        renderSeasonTabs();
        renderTableRows(currentSeason);
      } else {
        renderError('Failed to load points table.');
      }
    } catch (err) {
      console.error('Error fetching standings:', err);
      renderError('Unable to connect to server.');
    }
  }

  // 2. Render Season Selector Tabs
  function renderSeasonTabs() {
    const container = document.getElementById('seasonTabsContainer');
    if (!container) return;

    if (availableSeasons.length === 0) {
      container.style.display = 'none';
      return;
    }

    container.style.display = 'flex';
    container.innerHTML = availableSeasons.map(season => {
      const isActive = season.toLowerCase() === currentSeason.toLowerCase();
      return `<button type="button" class="tab-btn ${isActive ? 'active' : ''}" data-season="${season}">
        ${season}
      </button>`;
    }).join('');

    // Attach click handlers
    container.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const selectedSeason = this.getAttribute('data-season');
        if (selectedSeason !== currentSeason) {
          currentSeason = selectedSeason;
          container.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          
          // Update URL without refresh
          const url = new URL(window.location);
          url.searchParams.set('season', currentSeason);
          window.history.pushState({}, '', url);

          renderTableRows(currentSeason);
        }
      });
    });
  }

  // 3. Render Table Rows for Active Season
  function renderTableRows(season) {
    const tbody = document.getElementById('standingsTableBody');
    if (!tbody) return;

    // Filter standings by season
    const rows = allStandingsData.filter(item => 
      !season || (item.season && item.season.toLowerCase() === season.toLowerCase())
    );

    // Sort by PTS desc, GD desc, GF desc
    rows.sort((a, b) => {
      if ((b.pts || 0) !== (a.pts || 0)) return (b.pts || 0) - (a.pts || 0);
      if ((b.gd || 0) !== (a.gd || 0)) return (b.gd || 0) - (a.gd || 0);
      return (b.gf || 0) - (a.gf || 0);
    });

    if (rows.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="11" style="text-align:center; padding:50px 20px; color:#6b7280; font-size:15px;">
            <i class="fas fa-table" style="font-size:32px; display:block; margin-bottom:12px; color:#cbd5e1;"></i>
            No standings data available for <strong>${season}</strong> yet.
          </td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = rows.map((team, idx) => {
      const rank = idx + 1;
      const isQualifying = rank <= 4;
      const gd = parseInt(team.gd) || 0;
      const gdFormatted = gd > 0 ? `+${gd}` : gd;
      const gdClass = gd > 0 ? 'positive' : (gd < 0 ? 'negative' : '');
      
      // Form Badges (e.g. "W,W,D,L,W" or "W,D,L")
      const formItems = (team.form || 'W,W,W,D,W').split(',').map(s => s.trim().toUpperCase());
      const formHtml = formItems.map(f => {
        let badgeClass = 'win';
        if (f === 'D') badgeClass = 'draw';
        else if (f === 'L') badgeClass = 'loss';
        return `<span class="form-badge ${badgeClass}">${f}</span>`;
      }).join('');

      return `
        <tr class="${isQualifying ? 'qualifying-row' : ''}">
          <td class="col-rank">${rank}</td>
          <td class="col-team">
            <div class="team-meta">
              <span class="team-name">${escapeHtml(team.teamName || 'Unknown Team')}</span>
            </div>
          </td>
          <td class="col-stat">${team.played || 0}</td>
          <td class="col-stat">${team.won || 0}</td>
          <td class="col-stat">${team.draw || 0}</td>
          <td class="col-stat">${team.lost || 0}</td>
          <td class="col-stat">${team.gf || 0}</td>
          <td class="col-stat">${team.ga || 0}</td>
          <td class="col-stat ${gdClass}">${gdFormatted}</td>
          <td class="col-stat pts">${team.pts || 0}</td>
          <td class="col-form">
            ${formHtml}
          </td>
        </tr>
      `;
    }).join('');
  }

  function renderError(msg) {
    const tbody = document.getElementById('standingsTableBody');
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="11" style="text-align:center; padding:40px; color:#ef4444;">
            <i class="fas fa-exclamation-circle" style="margin-right:8px;"></i> ${msg}
          </td>
        </tr>
      `;
    }
  }

  function escapeHtml(text) {
    const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
    return String(text).replace(/[&<>"']/g, m => map[m]);
  }

  // Initial load
  loadStandings();
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
