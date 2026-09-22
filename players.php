<?php
// players.php
$pageTitle = 'Player Profile - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/players.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('players');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Player Profile';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'View and manage your registered player information for the UP Pro Handball League.';
$heroBadge = !empty($pb['badgeText']) ? $pb['badgeText'] : 'UPPHL PLAYER PORTAL';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- HERO -->
<section class="profile-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>

  <div class="container">

    <span class="profile-small-title">
      <?= htmlspecialchars($heroBadge) ?>
    </span>

    <h1><?= htmlspecialchars($heroTitle) ?></h1>

    <p>
      <?= htmlspecialchars($heroSubtitle) ?>
    </p>

  </div>

</section>


<!-- PROFILE -->
<section class="profile-section">

  <div class="container">

    <!-- SEARCH & FILTER BAR -->
    <div class="directory-controls">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchPlayerInput" placeholder="Search registered players by Name, ID, or District..." oninput="filterPlayers()">
      </div>

      <select class="filter-select" id="filterPosSelect" onchange="filterPlayers()">
        <option value="">All Positions</option>
        <option value="Centre Back">Centre Back / Playmaker</option>
        <option value="Goalkeeper">Goalkeeper</option>
        <option value="Left Wing">Left Wing</option>
        <option value="Right Wing">Right Wing</option>
        <option value="Pivot">Pivot / Line Player</option>
        <option value="Left Back">Left Back</option>
        <option value="Right Back">Right Back</option>
      </select>

      <select class="filter-select" id="filterDistrictSelect" onchange="filterPlayers()">
        <option value="">All Districts</option>
        <option value="Varanasi">Varanasi</option>
        <option value="Lucknow">Lucknow</option>
        <option value="Kanpur">Kanpur</option>
        <option value="Prayagraj">Prayagraj</option>
        <option value="Gorakhpur">Gorakhpur</option>
      </select>
    </div>

    <!-- REGISTERED PLAYERS TABLE CARD -->
    <div class="players-table-card">
      <div class="players-table-header">
        <h2>
          <i class="fa-solid fa-users" style="color: var(--profile-orange);"></i>
          Registered Players Directory
        </h2>
        <div style="display: flex; align-items: center; gap: 12px;">
          <button type="button" onclick="openPlayerLoginModal(event)" style="background: var(--profile-orange); color: #ffffff; border: none; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-right-to-bracket"></i> Player Login
          </button>
          <span class="players-count-badge" id="playersCountBadge">0 Registered Players</span>
        </div>
      </div>

      <div class="players-table-wrapper">
        <table class="players-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Photo</th>
              <th>Player ID</th>
              <th>Full Name</th>
              <th>Date of Birth</th>
              <th>Age</th>
              <th>Gender</th>
              <th>Position</th>
              <th>District</th>
            </tr>
          </thead>
          <tbody id="playersTableBody">
            <!-- Rendered dynamically by JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- 10-PER-PAGE AUTOMATIC PAGINATION BAR -->
      <div class="pagination-wrapper" id="playersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
        <span id="playersPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 players</span>
        <div id="playersPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
      </div>
    </div>

    </div>
  </section>
</main>

<script>
let allPlayersData = [];

function initDirectory() {
  // Fetch ONLY live approved players dynamically from backend API
  fetch('api/get-approved-players.php')
    .then(r => r.json())
    .then(res => {
      if (res.success && Array.isArray(res.players)) {
        allPlayersData = res.players;
      } else {
        allPlayersData = [];
      }
      renderPlayerTable(allPlayersData);
    })
    .catch(err => {
      allPlayersData = [];
      renderPlayerTable(allPlayersData);
    });
}

let currentPublicPage = 1;
const PUBLIC_PER_PAGE = 10;
let currentFilteredList = [];

function renderPlayerTable(players, page = 1) {
  currentPublicPage = page;
  currentFilteredList = players;
  const tbody = document.getElementById('playersTableBody');
  const countBadge = document.getElementById('playersCountBadge');
  const pageInfo = document.getElementById('playersPageInfoText');
  const paginButtons = document.getElementById('playersPaginationButtons');
  if (!tbody) return;

  if (countBadge) {
    countBadge.textContent = players.length + (players.length === 1 ? ' Registered Player' : ' Registered Players');
  }

  if (!players || players.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="9" style="text-align: center; padding: 35px; color: #64748b;">
          <i class="fa-solid fa-user-slash" style="font-size: 28px; color: #94a3b8; margin-bottom: 8px; display: block;"></i>
          No registered players found matching your filter criteria.
        </td>
      </tr>
    `;
    if (pageInfo) pageInfo.textContent = 'Showing 0 players';
    if (paginButtons) paginButtons.innerHTML = '';
    return;
  }

  const totalPages = Math.ceil(players.length / PUBLIC_PER_PAGE) || 1;
  if (currentPublicPage > totalPages) currentPublicPage = totalPages;
  if (currentPublicPage < 1) currentPublicPage = 1;

  const startIndex = (currentPublicPage - 1) * PUBLIC_PER_PAGE;
  const endIndex = Math.min(startIndex + PUBLIC_PER_PAGE, players.length);
  const pageSlice = players.slice(startIndex, endIndex);

  tbody.innerHTML = pageSlice.map((p, idx) => {
    return `
      <tr>
        <td><strong>${startIndex + idx + 1}</strong></td>
        <td>
          <img src="${p.photoUrl || 'assets/images/default-player.png'}" alt="${p.fullName || 'Player Photo'}" class="table-avatar-img">
        </td>
        <td><span class="table-player-id">${p.playerId || 'UPPHL-S2-001'}</span></td>
        <td><span class="table-player-name">${p.fullName || 'Player Name'}</span></td>
        <td>${p.dob || '01 Jan 2000'}</td>
        <td>${p.age || '25 Years'}</td>
        <td>${p.gender || 'Male'}</td>
        <td><span class="table-position-chip">${p.primaryPos || 'Player'}</span></td>
        <td>${p.district || 'Uttar Pradesh'}</td>
      </tr>
    `;
  }).join('');

  // Update Page Info
  if (pageInfo) {
    pageInfo.textContent = `Showing ${startIndex + 1}–${endIndex} of ${players.length} registered players (Page ${currentPublicPage} of ${totalPages})`;
  }

  // Render Page Buttons (Smart 5-page window)
  if (paginButtons) {
    if (totalPages <= 1) {
      paginButtons.innerHTML = '';
      return;
    }
    let btns = '';
    btns += `<button type="button" onclick="goToPublicPage(${currentPublicPage - 1})" ${currentPublicPage === 1 ? 'disabled' : ''} class="pagin-btn"><i class="fa-solid fa-chevron-left"></i> Prev</button>`;

    // Smart sliding window: max 5 page numbers
    const MAX_PAGES = 5;
    let startPage = Math.max(1, currentPublicPage - Math.floor(MAX_PAGES / 2));
    let endPage = startPage + MAX_PAGES - 1;

    if (endPage > totalPages) {
      endPage = totalPages;
      startPage = Math.max(1, endPage - MAX_PAGES + 1);
    }

    if (startPage > 1) {
      btns += `<button type="button" onclick="goToPublicPage(1)" class="pagin-btn">1</button>`;
      if (startPage > 2) {
        btns += `<span style="padding: 0 4px; color: #94a3b8; font-weight: 700; user-select: none;">...</span>`;
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      btns += `<button type="button" onclick="goToPublicPage(${i})" class="pagin-btn ${i === currentPublicPage ? 'active' : ''}">${i}</button>`;
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        btns += `<span style="padding: 0 4px; color: #94a3b8; font-weight: 700; user-select: none;">...</span>`;
      }
      btns += `<button type="button" onclick="goToPublicPage(${totalPages})" class="pagin-btn">${totalPages}</button>`;
    }

    btns += `<button type="button" onclick="goToPublicPage(${currentPublicPage + 1})" ${currentPublicPage === totalPages ? 'disabled' : ''} class="pagin-btn">Next <i class="fa-solid fa-chevron-right"></i></button>`;
    paginButtons.innerHTML = btns;
  }
}

function goToPublicPage(page) {
  renderPlayerTable(currentFilteredList, page);
}

function filterPlayers() {
  const query = (document.getElementById('searchPlayerInput').value || '').toLowerCase().trim();
  const posFilter = (document.getElementById('filterPosSelect').value || '').toLowerCase();
  const distFilter = (document.getElementById('filterDistrictSelect').value || '').toLowerCase();

  const filtered = allPlayersData.filter(p => {
    const nameMatch = (p.fullName || '').toLowerCase().includes(query) || (p.playerId || '').toLowerCase().includes(query) || (p.district || '').toLowerCase().includes(query);
    const posMatch = !posFilter || (p.primaryPos || '').toLowerCase().includes(posFilter);
    const distMatch = !distFilter || (p.district || '').toLowerCase().includes(distFilter);
    return nameMatch && posMatch && distMatch;
  });

  renderPlayerTable(filtered, 1);
}

/* =========================================
   LOGIN MODAL HANDLERS
========================================= */
window.openMyProfileModal = function(e) {
  if (e) e.preventDefault();
  window.openPlayerLoginModal();
};

window.openPlayerLoginModal = function(e) {
  if (e && e.preventDefault) e.preventDefault();
  const modal = document.getElementById('playerLoginModal');
  if (modal) {
    const err = document.getElementById('loginErrorMsg');
    if (err) err.style.display = 'none';
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
};

window.closePlayerLoginModal = function() {
  const modal = document.getElementById('playerLoginModal');
  if (modal) {
    modal.classList.remove('open');
    document.body.style.overflow = '';
  }
};

window.handlePlayerLoginSubmit = function(e) {
  e.preventDefault();
  const inputVal = (document.getElementById('loginPlayerIdInput').value || '').trim().toLowerCase();
  const passVal  = (document.getElementById('loginPasswordInput')?.value || '').trim();
  const err      = document.getElementById('loginErrorMsg');

  const match = allPlayersData.find(p => {
    const idOrMobileMatch = (p.playerId || '').toLowerCase() === inputVal || 
                            (p.mobile || '').toLowerCase() === inputVal ||
                            (p.fullName || '').toLowerCase() === inputVal;
    const passMatch = !p.password || p.password === passVal;
    return idOrMobileMatch && passMatch;
  });

  if (match) {
    window.currentLoggedInPlayer = match;
    sessionStorage.setItem('upphl_active_player', JSON.stringify(match));
    localStorage.setItem('upphl_active_player', JSON.stringify(match));
    if (window.updateNavProfileState) {
      window.updateNavProfileState(match);
    }
    closePlayerLoginModal();
    window.location.href = 'my-profile.php?id=' + encodeURIComponent(match.playerId);
  } else {
    if (err) {
      err.style.display = 'block';
      err.textContent = 'Invalid credentials. Please enter a valid Player ID/Mobile & Password.';
    }
  }
};

document.addEventListener('DOMContentLoaded', function() {
  initDirectory();
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('openLogin') === '1') {
    setTimeout(function() {
      if (window.openPlayerLoginModal) window.openPlayerLoginModal();
    }, 200);
  }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
