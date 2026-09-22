<?php
// live.php
// UPPHL Live Stream, Official Broadcast Partners & Season Video Vault
$pageTitle = 'UPPHL Live Stream & Broadcast Hub — UP Pro Handball League';
$extraCss = [
  'assets/css/live.css'
];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/functions.php';

$pb = upphl_page_banner('live');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Watch UPPHL Live Action';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Experience every high-octane goal, rapid counter-attack, and dramatic finish live across our official digital streaming and national television broadcast partners.';
$heroBadge = !empty($pb['badgeText']) ? $pb['badgeText'] : 'Official Live Broadcast Hub';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';

// 1. Fetch Dynamic Live Partners
$livePartners = upphl_get_live_partners(true);

// 2. Fetch Dynamic Completed Season Videos & Unique Seasons
$videoSeasons = upphl_get_live_video_seasons();
if (empty($videoSeasons)) {
    $videoSeasons = ['Season 1'];
}
$activeSeasonFilter = $_GET['season'] ?? 'all';
$activeCategoryFilter = $_GET['cat'] ?? 'all';
$allVideos = upphl_get_live_videos(null, true);
?>

<main>
  <!-- Hero Section -->
  <section class="live-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
    <div class="container">
      <div class="live-badge-top">
        <span class="live-pulsing-dot"></span>
        <?= htmlspecialchars($heroBadge) ?>
      </div>
      <h1><?= htmlspecialchars($heroTitle) ?></h1>
      <p>
        <?= htmlspecialchars($heroSubtitle) ?>
      </p>
    </div>
  </section>

  <!-- 1. OFFICIAL STREAMING & TV PARTNERS SECTION -->
  <section class="broadcast-partners-section">
    <div class="container">
      <div class="section-header-center">
        <span class="section-tag">Broadcast Network</span>
        <h2>Official Streaming &amp; TV Partners</h2>
        <p>
          Tune in to UP Pro Handball League matches through any of our official broadcasting partners on TV, mobile app, and web.
        </p>
      </div>

      <div class="broadcast-grid">
        <?php if (!empty($livePartners)): ?>
          <?php foreach ($livePartners as $p): 
            $cStyle = !empty($p['cardStyle']) ? $p['cardStyle'] : 'custom-card';
            $btnCls = ($cStyle === 'youtube-card') ? 'btn-youtube' : (($cStyle === 'ddsports-card') ? 'btn-ddsports' : (($cStyle === 'fancode-card') ? 'btn-fancode' : 'btn-youtube'));
            $btnText = !empty($p['buttonText']) ? $p['buttonText'] : 'Watch Live';
            $watchUrl = !empty($p['watchUrl']) ? $p['watchUrl'] : '#';
            $icon = !empty($p['iconClass']) ? $p['iconClass'] : 'fa-solid fa-tower-broadcast';
          ?>
            <div class="broadcast-card <?= htmlspecialchars($cStyle) ?>">
              <div class="broadcast-top-row">
                <div class="broadcast-brand-group">
                  <div class="broadcast-icon-box">
                    <?php if (!empty($p['logoUrl'])): ?>
                      <img src="<?= htmlspecialchars($p['logoUrl']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; height: 100%; object-fit: contain; border-radius: 6px;">
                    <?php else: ?>
                      <i class="<?= htmlspecialchars($icon) ?>"></i>
                    <?php endif; ?>
                  </div>
                  <div>
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <?php if (!empty($p['platformType'])): ?>
                      <span class="broadcast-type"><?= htmlspecialchars($p['platformType']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <?php if (!empty($p['badgeText'])): ?>
                  <span class="broadcast-badge"><?= htmlspecialchars($p['badgeText']) ?></span>
                <?php endif; ?>
              </div>

              <?php if (!empty($p['description'])): ?>
                <p class="broadcast-desc">
                  <?= htmlspecialchars($p['description']) ?>
                </p>
              <?php endif; ?>

              <div class="broadcast-card-footer">
                <?php if (!empty($p['metaPill'])): ?>
                  <span class="broadcast-meta-pill">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> <?= htmlspecialchars($p['metaPill']) ?>
                  </span>
                <?php else: ?>
                  <span class="broadcast-meta-pill">
                    <i class="fa-solid fa-signal" style="color: #ef4444;"></i> Official Partner
                  </span>
                <?php endif; ?>

                <a href="<?= htmlspecialchars($watchUrl) ?>" target="_blank" class="broadcast-action-btn <?= $btnCls ?>">
                  <i class="<?= htmlspecialchars($icon) ?>"></i> <?= htmlspecialchars($btnText) ?>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; grid-column: 1/-1; color: #94a3b8; padding: 30px;">
            Broadcast partners details updating soon.
          </p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- 2. COMPLETED SEASON VIDEOS, MATCH REPLAYS & HIGHLIGHTS (SEASON-WISE) -->
  <section class="live-videos-section" id="seasonVideosSection">
    <div class="container">
      <div class="video-section-header">
        <span class="section-tag">Match Vault &amp; Replays</span>
        <h2>Completed Season Videos &amp; Highlights</h2>
        <p>
          Catch up on thrilling full match replays, championship climaxes, top goals, and exclusive behind-the-scenes moments season-wise.
        </p>
      </div>

      <!-- Season & Category Filter Controls -->
      <div class="video-filter-wrapper">
        <!-- Season Tabs -->
        <div class="season-tabs-bar" id="seasonTabsBar">
          <button type="button" class="season-tab-btn active" data-season="all" onclick="filterLiveVideos('all', currentVideoCat)">
            <i class="fa-solid fa-layer-group"></i> All Seasons
          </button>
          <?php foreach ($videoSeasons as $sName): ?>
            <button type="button" class="season-tab-btn" data-season="<?= htmlspecialchars($sName) ?>" onclick="filterLiveVideos('<?= htmlspecialchars($sName) ?>', currentVideoCat)">
              <i class="fa-solid fa-trophy"></i> <?= htmlspecialchars($sName) ?>
            </button>
          <?php endforeach; ?>
        </div>

        <!-- Category Filter Chips -->
        <div class="category-chips-bar" id="catChipsBar">
          <button type="button" class="cat-chip-btn active" data-cat="all" onclick="filterLiveVideos(currentVideoSeason, 'all')">
            All Videos
          </button>
          <button type="button" class="cat-chip-btn" data-cat="Full Match" onclick="filterLiveVideos(currentVideoSeason, 'Full Match')">
            Full Matches
          </button>
          <button type="button" class="cat-chip-btn" data-cat="Highlights" onclick="filterLiveVideos(currentVideoSeason, 'Highlights')">
            Highlights
          </button>
          <button type="button" class="cat-chip-btn" data-cat="Top Moments" onclick="filterLiveVideos(currentVideoSeason, 'Top Moments')">
            Top Moments
          </button>
          <button type="button" class="cat-chip-btn" data-cat="Ceremony" onclick="filterLiveVideos(currentVideoSeason, 'Ceremony')">
            Ceremonies
          </button>
        </div>
      </div>

      <!-- Videos Grid Container -->
      <div class="live-videos-grid" id="liveVideosGrid">
        <?php if (!empty($allVideos)): ?>
          <?php foreach ($allVideos as $v): 
            $embed = !empty($v['embedUrl']) ? $v['embedUrl'] : $v['videoUrl'];
            $thumb = !empty($v['thumbnailUrl']) ? $v['thumbnailUrl'] : 'assets/images/aboutus-banner.jpeg';
          ?>
            <div class="live-video-card" 
                 data-season="<?= htmlspecialchars(strtolower($v['season'] ?? 'season 1')) ?>" 
                 data-cat="<?= htmlspecialchars(strtolower($v['videoCategory'] ?? 'full match')) ?>"
                 onclick="openLiveVideoModal('<?= htmlspecialchars($embed, ENT_QUOTES) ?>', '<?= htmlspecialchars($v['title'], ENT_QUOTES) ?>')">
              
              <div class="video-thumb-wrap">
                <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($v['title']) ?>" loading="lazy">
                
                <div class="video-play-overlay">
                  <div class="play-circle-btn">
                    <i class="fa-solid fa-play" style="margin-left: 3px;"></i>
                  </div>
                </div>

                <span class="video-season-badge"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($v['season'] ?? 'Season 1') ?></span>
                
                <?php if (!empty($v['duration'])): ?>
                  <span class="video-duration-tag"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($v['duration']) ?></span>
                <?php endif; ?>
              </div>

              <div class="video-card-body">
                <div>
                  <div class="video-category-tag"><?= htmlspecialchars($v['videoCategory'] ?? 'Replay') ?></div>
                  <h3 class="video-card-title"><?= htmlspecialchars($v['title']) ?></h3>
                </div>

                <div class="video-card-meta">
                  <span><i class="fa-regular fa-calendar" style="color: #ea580c;"></i> <?= !empty($v['matchDate']) ? date('d M Y', strtotime($v['matchDate'])) : 'Official Video' ?></span>
                  <span style="color: #38bdf8; font-weight: 700;"><i class="fa-solid fa-circle-play"></i> Watch Video</span>
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p id="noVideosNotice" style="text-align: center; grid-column: 1/-1; color: #94a3b8; padding: 40px;">
            <i class="fa-solid fa-video" style="font-size: 32px; display: block; margin-bottom: 10px; color: #334155;"></i>
            No season videos uploaded yet. Check back soon for match replays.
          </p>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <!-- 3. DYNAMIC UPCOMING LIVE MATCH BROADCASTS SECTION -->
  <section class="broadcast-schedule-section">
    <div class="container">
      <div class="section-header-center">
        <span class="section-tag">Match Schedule Guide</span>
        <h2>Upcoming Live Match Broadcasts</h2>
        <p>
          Check real-time fixtures below to know when and where to catch your favorite franchise live in action. Matches auto-update as they start and finish.
        </p>
      </div>

      <div class="schedule-card-list" id="liveScheduleCardList">
        <!-- Dynamic Matches Loaded from API -->
        <div style="text-align: center; padding: 40px; color: #64748b;">
          <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Loading live match broadcast schedule...
        </div>
      </div>
    </div>
  </section>

  <!-- 4. BROADCAST FEATURES -->
  <section class="broadcast-features-section">
    <div class="container">
      <div class="features-grid-4">
        <div class="feature-box">
          <div class="feature-icon">
            <i class="fa-solid fa-video"></i>
          </div>
          <h4>Full HD 1080p</h4>
          <p>Crystal clear multi-camera production capturing every millisecond of action.</p>
        </div>

        <div class="feature-box">
          <div class="feature-icon">
            <i class="fa-solid fa-microphone-lines"></i>
          </div>
          <h4>Dual Commentary</h4>
          <p>Dynamic live Hindi and English match commentary by seasoned sports analysts.</p>
        </div>

        <div class="feature-box">
          <div class="feature-icon">
            <i class="fa-solid fa-bolt"></i>
          </div>
          <h4>Instant Highlights</h4>
          <p>Super fast goal clips, top saves, and post-match breakdowns on YouTube.</p>
        </div>

        <div class="feature-box">
          <div class="feature-icon">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <h4>Live Statistics</h4>
          <p>Real-time player tracking, shot conversions, and scoreboard analytics.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- POPUP VIDEO PLAYER MODAL -->
<div class="live-video-modal" id="liveVideoPlayerModal" onclick="handleVideoModalBackdropClick(event)">
  <div class="video-modal-dialog">
    <div class="video-modal-header">
      <h3 class="video-modal-title" id="videoModalTitle">UPPHL Match Replay</h3>
      <button class="video-modal-close" onclick="closeLiveVideoModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="video-modal-iframe-wrap">
      <iframe id="videoModalIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
  </div>
</div>

<script>
// ==========================================
// VIDEO MODAL HANDLER
// ==========================================
function openLiveVideoModal(embedUrl, title) {
    const modal = document.getElementById('liveVideoPlayerModal');
    const iframe = document.getElementById('videoModalIframe');
    const titleEl = document.getElementById('videoModalTitle');

    if (!modal || !iframe) return;

    // Ensure autoplay param for seamless playback
    let url = embedUrl;
    if (url.indexOf('autoplay=') === -1) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
    }

    iframe.src = url;
    if (titleEl) titleEl.textContent = title || 'UPPHL Match Replay';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeLiveVideoModal() {
    const modal = document.getElementById('liveVideoPlayerModal');
    const iframe = document.getElementById('videoModalIframe');

    if (iframe) iframe.src = '';
    if (modal) modal.classList.remove('show');
    document.body.style.overflow = '';
}

function handleVideoModalBackdropClick(e) {
    if (e.target.id === 'liveVideoPlayerModal') {
        closeLiveVideoModal();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLiveVideoModal();
    }
});

// ==========================================
// CLIENT-SIDE SEASON & CATEGORY FILTER
// ==========================================
let currentVideoSeason = 'all';
let currentVideoCat = 'all';

function filterLiveVideos(season, cat) {
    currentVideoSeason = season;
    currentVideoCat = cat;

    // Update active tab buttons
    document.querySelectorAll('#seasonTabsBar .season-tab-btn').forEach(btn => {
        const bSeason = btn.getAttribute('data-season');
        if (bSeason.toLowerCase() === season.toLowerCase()) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    document.querySelectorAll('#catChipsBar .cat-chip-btn').forEach(btn => {
        const bCat = btn.getAttribute('data-cat');
        if (bCat.toLowerCase() === cat.toLowerCase()) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    const cards = document.querySelectorAll('.live-video-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const cardSeason = (card.getAttribute('data-season') || '').toLowerCase();
        const cardCat = (card.getAttribute('data-cat') || '').toLowerCase();

        const matchSeason = (season === 'all' || cardSeason === season.toLowerCase());
        const matchCat = (cat === 'all' || cardCat === cat.toLowerCase());

        if (matchSeason && matchCat) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const grid = document.getElementById('liveVideosGrid');
    let noMatchEl = document.getElementById('noFilteredVideosMsg');

    if (visibleCount === 0) {
        if (!noMatchEl) {
            noMatchEl = document.createElement('div');
            noMatchEl.id = 'noFilteredVideosMsg';
            noMatchEl.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 40px; color: #94a3b8;';
            noMatchEl.innerHTML = '<i class="fa-solid fa-video-slash" style="font-size: 32px; display: block; margin-bottom: 10px; color: #334155;"></i> No videos found matching the selected season/category.';
            grid.appendChild(noMatchEl);
        }
        noMatchEl.style.display = 'block';
    } else if (noMatchEl) {
        noMatchEl.style.display = 'none';
    }
}

// ==========================================
// DYNAMIC LIVE & UPCOMING MATCHES WITH AUTO TIME ROLLOVER
// ==========================================
function loadLiveMatchesSchedule() {
    fetch('api/fixtures.php')
      .then(r => r.json())
      .then(data => {
        const list = document.getElementById('liveScheduleCardList');
        if (!list) return;
        if (!data.success || !data.fixtures || data.fixtures.length === 0) {
          list.innerHTML = '<p style="text-align:center; color:#94a3b8; padding:30px;">No broadcast matches currently scheduled.</p>';
          return;
        }

        const now = new Date().getTime();

        // Evaluate dynamic status based on real-time client clock
        const processedFixtures = data.fixtures.map(f => {
            const dateStr = f.matchDate || '';
            const sTime = f.startTime || (f.matchTime || '18:00');
            const eTime = f.endTime || '';

            let startMs = 0;
            let endMs = 0;

            if (dateStr) {
                // Try parse Date + Time
                const startObj = new Date(`${dateStr} ${sTime}`);
                if (!isNaN(startObj.getTime())) {
                    startMs = startObj.getTime();
                } else {
                    startMs = new Date(dateStr).getTime();
                }

                if (eTime) {
                    const endObj = new Date(`${dateStr} ${eTime}`);
                    if (!isNaN(endObj.getTime())) {
                        endMs = endObj.getTime();
                    } else {
                        endMs = startMs + (2 * 3600 * 1000);
                    }
                } else {
                    endMs = startMs + (2 * 3600 * 1000); // 2 hrs window
                }
            }

            let dynamicStatus = f.computedStatus || 'Upcoming';
            if (startMs > 0 && endMs > 0) {
                if (now > endMs) {
                    dynamicStatus = 'Completed';
                } else if (now >= startMs && now <= endMs) {
                    dynamicStatus = 'Live';
                } else {
                    dynamicStatus = 'Upcoming';
                }
            }

            // Respect manual override if explicitly completed or live
            if (f.status && f.status.toLowerCase() === 'live') dynamicStatus = 'Live';

            return {
                ...f,
                dynamicStatus,
                startMs,
                endMs
            };
        });

        // 1. Separate Live and Upcoming matches
        const liveMatches = processedFixtures.filter(f => f.dynamicStatus === 'Live');
        const upcomingMatches = processedFixtures.filter(f => f.dynamicStatus === 'Upcoming');

        // Sort upcoming matches chronologically (soonest first)
        upcomingMatches.sort((a, b) => (a.startMs || 0) - (b.startMs || 0));

        // Combined active queue (Live first, then next upcoming)
        let activeMatches = [...liveMatches, ...upcomingMatches];

        if (activeMatches.length === 0) {
            list.innerHTML = `
                <div style="text-align: center; padding: 45px 20px; color: #94a3b8; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px;">
                    <i class="fa-regular fa-calendar-check" style="font-size: 36px; display: block; margin-bottom: 12px; color: #94a3b8;"></i>
                    <strong style="color: #1e293b; font-size: 16px; display: block; margin-bottom: 4px;">No Upcoming Live Matches Currently Scheduled</strong>
                    <p style="margin: 0; font-size: 13.5px; color: #64748b;">All previous tournament fixtures have concluded. New live match schedules will appear here automatically once announced by admin.</p>
                </div>
            `;
            return;
        }

        // Limit to 5 matches in the schedule guide
        const displayList = activeMatches.slice(0, 5);

        list.innerHTML = displayList.map(f => {
          const isLive = f.dynamicStatus === 'Live';
          const timeDisplay = isLive ? 'LIVE NOW' : (f.startTime || f.matchTime || '04:00 PM');
          const tagStyle = isLive ? 'background: #dc2626; color:#fff;' : '';
          const scoreDisplay = (f.team1Score !== null && f.team1Score !== '' && f.team2Score !== null && f.team2Score !== '') 
                               ? `${f.team1Score} - ${f.team2Score}` 
                               : 'VS';

          return `
            <div class="schedule-item" style="${isLive ? 'border-color: #ef4444; box-shadow: 0 4px 18px rgba(239, 68, 68, 0.18);' : ''}">
              <div class="schedule-time-box" style="${tagStyle}">
                <span class="schedule-time">
                  ${isLive ? '<span class="live-pulsing-dot" style="display:inline-block; margin-right:4px;"></span>' : ''}
                  ${timeDisplay}
                </span>
                <span class="schedule-date">${f.matchTitle || 'LEAGUE MATCH'} &bull; ${f.matchDate || ''}</span>
              </div>

              <div class="schedule-teams-wrapper">
                <div class="schedule-team-box team-left">
                  <span class="schedule-team-name">${f.team1Name}</span>
                  <div class="schedule-team-logo">
                    <img src="${f.team1Logo || 'assets/images/teams/bhadohi-logo.jpeg'}" alt="${f.team1Name}" />
                  </div>
                </div>

                <span class="schedule-vs-badge" style="${isLive ? 'background:#dc2626; color:#fff; font-weight:900;' : ''}">
                  ${scoreDisplay}
                </span>

                <div class="schedule-team-box team-right">
                  <div class="schedule-team-logo">
                    <img src="${f.team2Logo || 'assets/images/teams/ghaziabad-logo.jpeg'}" alt="${f.team2Name}" />
                  </div>
                  <span class="schedule-team-name">${f.team2Name}</span>
                </div>
              </div>

              <div class="schedule-channels">
                <a href="https://www.youtube.com/@sportscastindia" target="_blank" class="channel-tag tag-yt"><i class="fa-brands fa-youtube"></i> YouTube</a>
                <a href="https://prasarbharati.gov.in/dd-sports/" target="_blank" class="channel-tag tag-dd"><i class="fa-solid fa-tv"></i> DD Sports</a>
                <a href="https://www.fancode.com" target="_blank" class="channel-tag tag-fc"><i class="fa-solid fa-play"></i> FanCode</a>
              </div>
            </div>
          `;
        }).join('');
      })
      .catch(err => console.log('Live schedule load error:', err));
}

// Initial load and auto-refresh interval every 30 seconds for live time rollover
document.addEventListener('DOMContentLoaded', function() {
    loadLiveMatchesSchedule();
    setInterval(loadLiveMatchesSchedule, 30000);
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
