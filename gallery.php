<?php
// gallery.php
$pageTitle = 'Gallery - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/gallery.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('gallery');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Media Gallery';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Explore high-energy match glimpses, trophy launch, auction highlights, trials and updates from UPPHL.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
      <!-- Hero -->
      <section class="gallery-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <h1><?= htmlspecialchars($heroTitle) ?></h1>
          <p><?= htmlspecialchars($heroSubtitle) ?></p>
        </div>
      </section>

      <!-- Gallery Grid and Filters -->
      <section class="gallery-section">
        <div class="container">
          
          <!-- Dynamic Season Selector -->
          <div class="season-selector-wrapper" id="seasonSelectorWrapper">
            <span class="season-selector-title"><i class="fa-solid fa-trophy"></i> Season:</span>
            <div class="season-pills" id="seasonPills">
              <button class="season-pill active" data-season="all">All Seasons</button>
              <button class="season-pill" data-season="Season 2">Season 2</button>
              <button class="season-pill" data-season="Season 1">Season 1</button>
            </div>
          </div>

          <!-- Category Filter Buttons -->
          <div class="filters-container" id="filtersContainer">
            <button class="filter-btn active" data-filter="all">All Photos</button>
            <button class="filter-btn" data-filter="announcement">Announcement Day</button>
            <button class="filter-btn" data-filter="trail">Trails</button>
            <button class="filter-btn" data-filter="trophy">Trophy Launch</button>
            <button class="filter-btn" data-filter="auction">Auction</button>
            <button class="filter-btn" data-filter="glimpses">Match Glimpses</button>
            <button class="filter-btn" data-filter="news">News</button>
          </div>

          <!-- Gallery Photos Grid -->
          <div class="gallery-grid" id="galleryGrid">
            
            <!-- Default Static Fallback Items -->
            <div class="gallery-item" data-category="announcement" data-season="Season 1">
              <img src="assets/images/ind1.jpg" alt="Gallery Photo" loading="lazy" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">Season 1</span>
                  <span class="gallery-tag">Announcement Day</span>
                </div>
              </div>
            </div>

            <div class="gallery-item" data-category="trail" data-season="Season 1">
              <img src="assets/images/ind2.jpg" alt="Gallery Photo" loading="lazy" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">Season 1</span>
                  <span class="gallery-tag">Trails</span>
                </div>
              </div>
            </div>

            <div class="gallery-item" data-category="trophy" data-season="Season 1">
              <img src="assets/images/index3.avif" alt="Gallery Photo" loading="lazy" onerror="this.src='assets/images/ind3.jpg'" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">Season 1</span>
                  <span class="gallery-tag">Trophy Launch</span>
                </div>
              </div>
            </div>

            <div class="gallery-item" data-category="auction" data-season="Season 1">
              <img src="assets/images/auctiong.jpg" alt="Gallery Photo" loading="lazy" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">Season 1</span>
                  <span class="gallery-tag">Auction</span>
                </div>
              </div>
            </div>

            <div class="gallery-item" data-category="glimpses" data-season="Season 1">
              <img src="assets/images/index4.avif" alt="Gallery Photo" loading="lazy" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">Season 1</span>
                  <span class="gallery-tag">Match Glimpses</span>
                </div>
              </div>
            </div>

            <div class="gallery-item" data-category="news" data-season="Season 1">
              <img src="assets/images/newg.jpg" alt="Gallery Photo" loading="lazy" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">Season 1</span>
                  <span class="gallery-tag">News</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- =====================================================
           FULLSCREEN GALLERY VIEWER LIGHTBOX MODAL
      ===================================================== -->
      <div class="gallery-lightbox" id="galleryLightbox" onclick="handleLightboxBackdropClick(event)">
        
        <!-- Top bar with counter, category badge, and close button -->
        <div class="lightbox-topbar">
          <div class="lightbox-counter" id="lightboxCounter">
            <i class="fa-solid fa-camera"></i> <span id="lightboxCountText">1 / 1</span>
            <span class="lightbox-category-badge" id="lightboxCategoryBadge">Gallery</span>
          </div>
          <button class="lightbox-close-btn" type="button" onclick="closeLightbox()" title="Close Viewer (Esc)">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Navigation Buttons -->
        <button class="lightbox-nav-btn lightbox-prev-btn" type="button" onclick="navigateLightbox(-1); event.stopPropagation();" title="Previous Photo (Left Arrow)">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        
        <button class="lightbox-nav-btn lightbox-next-btn" type="button" onclick="navigateLightbox(1); event.stopPropagation();" title="Next Photo (Right Arrow)">
          <i class="fa-solid fa-chevron-right"></i>
        </button>

        <!-- Main Image Stage -->
        <div class="lightbox-main-stage" onclick="event.stopPropagation()">
          <img class="lightbox-main-img" id="lightboxMainImg" src="" alt="Enlarged Gallery Photo" />
        </div>

        <!-- Bottom Thumbnail Strip -->
        <div class="lightbox-thumbnails-bar" id="lightboxThumbnailsBar" onclick="event.stopPropagation()">
          <!-- Populated dynamically via JS -->
        </div>

      </div>
</main>

<script>
      let currentSeason = 'all';
      let currentCategory = 'all';
      let allPhotosData = [];
      let currentFilteredPhotos = [];
      let currentPhotoIndex = 0;

      // Category display mapping
      const categoryNames = {
        'all': 'All Photos',
        'announcement': 'Announcement Day',
        'trail': 'Trails',
        'trophy': 'Trophy Launch',
        'auction': 'Auction',
        'glimpses': 'Match Glimpses',
        'news': 'News'
      };

      // ==========================================
      // LIGHTBOX GALLERY VIEWER CONTROLS
      // ==========================================
      function openLightbox(index) {
        if (!currentFilteredPhotos || currentFilteredPhotos.length === 0) return;
        
        currentPhotoIndex = (index >= 0 && index < currentFilteredPhotos.length) ? index : 0;
        updateLightboxView();

        const lb = document.getElementById('galleryLightbox');
        if (lb) {
          lb.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      }

      function updateLightboxView() {
        if (!currentFilteredPhotos || currentFilteredPhotos.length === 0) return;

        const photo = currentFilteredPhotos[currentPhotoIndex];
        if (!photo) return;

        const imgEl = document.getElementById('lightboxMainImg');
        const countEl = document.getElementById('lightboxCountText');
        const catBadge = document.getElementById('lightboxCategoryBadge');
        const thumbsBar = document.getElementById('lightboxThumbnailsBar');

        if (imgEl) {
          imgEl.style.opacity = '0.4';
          imgEl.style.transform = 'scale(0.97)';
          imgEl.src = photo.imageUrl || 'assets/images/ind1.jpg';
          setTimeout(() => {
            imgEl.style.opacity = '1';
            imgEl.style.transform = 'scale(1)';
          }, 50);
        }

        if (countEl) {
          countEl.textContent = `${currentPhotoIndex + 1} / ${currentFilteredPhotos.length}`;
        }

        if (catBadge) {
          const seasonText = photo.season ? `${photo.season} • ` : '';
          const catText = photo.categoryLabel || categoryNames[photo.category] || photo.category || 'Gallery';
          catBadge.textContent = `${seasonText}${catText}`;
        }

        // Render & update thumbnail strip
        if (thumbsBar) {
          if (currentFilteredPhotos.length > 1) {
            thumbsBar.style.display = 'flex';
            thumbsBar.innerHTML = currentFilteredPhotos.map((p, idx) => {
              const isActive = (idx === currentPhotoIndex) ? 'active' : '';
              return `
                <div class="lightbox-thumb-item ${isActive}" onclick="openLightbox(${idx})">
                  <img src="${p.imageUrl}" alt="thumb" />
                </div>
              `;
            }).join('');

            // Auto-scroll active thumbnail into view
            const activeThumb = thumbsBar.querySelector('.lightbox-thumb-item.active');
            if (activeThumb) {
              activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
          } else {
            thumbsBar.style.display = 'none';
          }
        }
      }

      function navigateLightbox(dir) {
        if (!currentFilteredPhotos || currentFilteredPhotos.length <= 1) return;
        currentPhotoIndex = (currentPhotoIndex + dir + currentFilteredPhotos.length) % currentFilteredPhotos.length;
        updateLightboxView();
      }

      function closeLightbox() {
        const lb = document.getElementById('galleryLightbox');
        if (lb) lb.classList.remove('active');
        document.body.style.overflow = '';
      }

      function handleLightboxBackdropClick(e) {
        if (e.target.id === 'galleryLightbox') {
          closeLightbox();
        }
      }

      // Keyboard Controls
      document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('galleryLightbox');
        if (!lb || !lb.classList.contains('active')) return;

        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
      });

      // Touch Swipe Support for Mobile Lightbox
      let touchStartX = 0;
      let touchEndX = 0;

      document.getElementById('galleryLightbox').addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });

      document.getElementById('galleryLightbox').addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
      }, { passive: true });

      function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
          navigateLightbox(1); // Swipe left -> Next
        }
        if (touchEndX > touchStartX + swipeThreshold) {
          navigateLightbox(-1); // Swipe right -> Previous
        }
      }

      // ==========================================
      // GALLERY RENDERING & FILTERING
      // ==========================================
      function renderGallery(photos) {
        const grid = document.getElementById('galleryGrid');
        if (!grid) return;

        currentFilteredPhotos = photos.filter(p => {
          const matchSeason = (currentSeason === 'all' || (p.season || '').toLowerCase() === currentSeason.toLowerCase());
          const matchCategory = (currentCategory === 'all' || (p.category || '').toLowerCase() === currentCategory.toLowerCase());
          return matchSeason && matchCategory;
        });

        if (currentFilteredPhotos.length === 0) {
          grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align:center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
              <i class="fa-solid fa-camera" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px; display:block;"></i>
              <h3 style="font-size: 1.2rem; color: #475569; margin-bottom: 6px;">No Photos Found</h3>
              <p style="color: #64748b; font-size: 0.9rem;">No photos match the selected season and category filter.</p>
            </div>
          `;
          return;
        }

        grid.innerHTML = currentFilteredPhotos.map((p, idx) => {
          const catLabel = p.categoryLabel || categoryNames[p.category] || p.category || 'Gallery';
          const season = p.season || 'Season 1';
          const imgUrl = p.imageUrl || 'assets/images/ind1.jpg';

          return `
            <div class="gallery-item" data-category="${p.category || 'all'}" data-season="${season}" onclick="openLightbox(${idx})">
              <img src="${imgUrl}" alt="Gallery Photo" loading="lazy" onerror="this.src='assets/images/ind1.jpg'" />
              <div class="gallery-zoom-badge"><i class="fa-solid fa-expand"></i></div>
              <div class="gallery-item-overlay">
                <div class="gallery-item-tags">
                  <span class="gallery-season-tag">${season}</span>
                  <span class="gallery-tag">${catLabel}</span>
                </div>
              </div>
            </div>
          `;
        }).join('');
      }

      function updateSeasonPills(seasons) {
        const pillsContainer = document.getElementById('seasonPills');
        if (!pillsContainer) return;

        let pillsHtml = `<button class="season-pill ${currentSeason === 'all' ? 'active' : ''}" data-season="all">All Seasons</button>`;
        seasons.forEach(s => {
          const isActive = (currentSeason.toLowerCase() === s.toLowerCase());
          pillsHtml += `<button class="season-pill ${isActive ? 'active' : ''}" data-season="${s}">${s}</button>`;
        });
        pillsContainer.innerHTML = pillsHtml;

        // Bind clicks
        pillsContainer.querySelectorAll('.season-pill').forEach(pill => {
          pill.addEventListener('click', () => {
            pillsContainer.querySelectorAll('.season-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            currentSeason = pill.getAttribute('data-season') || 'all';
            if (allPhotosData && allPhotosData.length > 0) {
              renderGallery(allPhotosData);
            }
          });
        });
      }

      function parseStaticGalleryItems() {
        const items = document.querySelectorAll('#galleryGrid .gallery-item');
        const list = [];
        items.forEach((item, i) => {
          const img = item.querySelector('img');
          const cat = item.getAttribute('data-category') || 'glimpses';
          const season = item.getAttribute('data-season') || 'Season 1';
          list.push({
            id: 'static_' + i,
            imageUrl: img ? img.getAttribute('src') : '',
            category: cat,
            categoryLabel: categoryNames[cat] || cat,
            season: season
          });
        });
        return list;
      }

      document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const paramSeason = urlParams.get('season');
        const paramCat = urlParams.get('cat') || urlParams.get('category') || urlParams.get('tab') || urlParams.get('filter');

        if (paramSeason) currentSeason = paramSeason;
        if (paramCat) currentCategory = paramCat;

        // Category filter buttons
        const filterBtns = document.querySelectorAll('.filters-container .filter-btn');
        filterBtns.forEach(btn => {
          const f = btn.getAttribute('data-filter');
          if (paramCat && f.toLowerCase() === paramCat.toLowerCase()) {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
          }

          btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.getAttribute('data-filter') || 'all';
            if (allPhotosData && allPhotosData.length > 0) {
              renderGallery(allPhotosData);
            }
          });
        });

        // Initialize with static items first as instant display
        allPhotosData = parseStaticGalleryItems();
        currentFilteredPhotos = allPhotosData;
        renderGallery(allPhotosData);

        // Fetch dynamic items from API
        fetch('api/gallery.php')
          .then(r => r.json())
          .then(data => {
            if (data.success && data.photos && data.photos.length > 0) {
              allPhotosData = data.photos;
              if (data.seasons && data.seasons.length > 0) {
                updateSeasonPills(data.seasons);
              }
              renderGallery(allPhotosData);
            }
          })
          .catch(err => {
            console.log('Using static gallery fallback', err);
          });
      });
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
