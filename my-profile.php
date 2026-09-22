<?php
// my-profile.php
$pageTitle = 'My Profile — UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/players.css',
);
require_once __DIR__ . '/includes/header.php';
?>

<main class="my-profile-container" id="profileMainContainer">
<!-- Dynamic Profile Content Loaded via JS -->
</main>

<script>
let currentPlayerData = null;

function loadMyProfile() {
  const urlParams = new URLSearchParams(window.location.search);
  const targetId = urlParams.get('id');

  // Try reading active player from Session Storage or Local Storage
  let saved = sessionStorage.getItem('upphl_active_player') || localStorage.getItem('upphl_active_player');
  if (saved) {
    try {
      currentPlayerData = JSON.parse(saved);
    } catch(e) {}
  }

  // If target ID given in URL, fetch live data from server
  if (targetId && (!currentPlayerData || (currentPlayerData.playerId || '').toLowerCase() !== targetId.toLowerCase())) {
    fetch('api/get-approved-players.php')
      .then(r => r.json())
      .then(res => {
        const match = (res.players || []).find(p => (p.playerId || '').toLowerCase() === targetId.toLowerCase());
        if (match) {
          currentPlayerData = match;
          sessionStorage.setItem('upphl_active_player', JSON.stringify(match));
          localStorage.setItem('upphl_active_player', JSON.stringify(match));
          if (window.updateNavProfileState) window.updateNavProfileState(match);
        }
        renderProfileContent();
      })
      .catch(() => renderProfileContent());
    return;
  }

  renderProfileContent();
}

function renderProfileContent() {
  const container = document.getElementById('profileMainContainer');
  if (!container) return;

  // If no logged in player found, show login prompt message
  if (!currentPlayerData) {
    container.innerHTML = `
      <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; margin: 40px auto; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
        <i class="fa-solid fa-user-lock" style="font-size: 48px; color: var(--profile-orange); margin-bottom: 16px;"></i>
        <h2 style="font-size: 22px; color: #1e293b; margin-bottom: 8px;">No Active Profile Session</h2>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">Please login with your Player ID & Password to view your verified profile and UTR details.</p>
        <button type="button" onclick="openPlayerLoginModal(event)" style="background: var(--profile-orange); color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px;">
          <i class="fa-solid fa-right-to-bracket"></i> Login Now
        </button>
      </div>
    `;
    return;
  }

  const p = currentPlayerData;
  const isPaid = (p.paymentStatus && String(p.paymentStatus).toLowerCase() === 'paid') || (p.paymentId && String(p.paymentId).trim() !== '') || (p.status && String(p.status).toLowerCase() === 'approved');

  container.innerHTML = `
    <form id="profileEditForm" onsubmit="saveProfileChanges(event)">
      <!-- HERO CARD -->
      <div class="profile-hero-card">
        <div class="profile-hero-left">
          <div class="avatar-wrapper">
            <img src="${p.photoUrl || 'assets/images/default-player.png'}" id="profileAvatarImg" alt="${p.fullName}">
          </div>
          <div class="hero-info">
            <h1 id="heroName">${p.fullName || 'Player Name'}</h1>
            <div class="hero-badges">
              <span class="hero-chip"><i class="fa-solid fa-person-running"></i> ${p.primaryPos || 'Player'}</span>
              <span class="hero-chip id-chip"><i class="fa-solid fa-id-card"></i> ${p.playerId || 'UPPHL-S2-001'}</span>
              <span class="hero-chip id-chip"><i class="fa-solid fa-location-dot"></i> ${p.district || 'Uttar Pradesh'}</span>
              ${isPaid 
                ? `<span class="hero-chip" style="background: #dcfce7; color: #15803d; border-color: #bbf7d0;"><i class="fa-solid fa-circle-check"></i> Bank Verified (Paid)</span>` 
                : `<span class="hero-chip" style="background: #fee2e2; color: #b91c1c; border-color: #fecaca;"><i class="fa-solid fa-clock"></i> Unpaid / Pending</span>`}
            </div>
          </div>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
          <button type="button" id="toggleEditBtn" class="edit-profile-btn" onclick="toggleEditMode()" style="background: var(--profile-orange); color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 13px;">
            <i class="fa-solid fa-pen-to-square"></i> Edit Profile
          </button>
        </div>
      </div>

      <!-- SECTIONS -->
      <div class="profile-sections-grid" id="sectionsWrapper">
        
        <!-- 00 Bank Payment & Verification Details -->
        <div class="detail-card" style="grid-column: 1 / -1; border: 1.5px solid #bbf7d0; background: #f0fdf4;">
          <div class="detail-card-head" style="border-bottom-color: #bbf7d0;">
            <h3 style="color: #15803d;"><i class="fa-solid fa-building-columns"></i> Bank Payment &amp; UTR Verification</h3>
            <span style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 4px 10px; border-radius: 6px; font-size: 12px; border: 1px solid #86efac;">
              <i class="fa-solid fa-shield-check"></i> Verified Registration
            </span>
          </div>
          <div class="fields-grid">
            <div class="field-box" style="background: #fff;">
              <label>Bank UTR / Transaction No.</label>
              <p style="font-family: monospace; font-weight: 800; font-size: 15px; color: #0f172a;"><i class="fa-solid fa-receipt" style="color: var(--profile-orange);"></i> ${p.paymentId || '4000003205450'}</p>
            </div>
            <div class="field-box" style="background: #fff;">
              <label>Registration Fee</label>
              <p style="font-weight: 800; color: #16a34a;">${p.amountPaid || '₹1,000.00'}</p>
            </div>
            <div class="field-box" style="background: #fff;">
              <label>Payment Method</label>
              <p style="font-weight: 700; color: #334155;">${p.paymentMethod || 'UPI / ICICI Orange PG'}</p>
            </div>
            <div class="field-box" style="background: #fff;">
              <label>Payment / Registration Date</label>
              <p style="color: #475569; font-weight: 600;">${p.paidAt || p.submittedAt || '2026-08-31 07:20:21'}</p>
            </div>
          </div>
        </div>

        <!-- 01 Basic Details -->
        <div class="detail-card">
          <div class="detail-card-head">
            <h3><i class="fa-solid fa-user" style="color: var(--profile-orange);"></i> Basic Information</h3>
          </div>
          <div class="fields-grid">
            <div class="field-box">
              <label>Full Name</label>
              <p>${p.fullName || '-'}</p>
              <input type="text" id="inpFullName" value="${p.fullName || ''}">
            </div>
            <div class="field-box">
              <label>Date of Birth</label>
              <p>${p.dob || '-'}</p>
              <input type="date" id="inpDob" value="${p.dob || ''}">
            </div>
            <div class="field-box">
              <label>Age</label>
              <p>${p.age || '-'}</p>
              <input type="text" id="inpAge" value="${p.age || ''}">
            </div>
            <div class="field-box">
              <label>Gender</label>
              <p>${p.gender || '-'}</p>
              <select id="inpGender">
                <option value="Male" ${p.gender==='Male'?'selected':''}>Male</option>
                <option value="Female" ${p.gender==='Female'?'selected':''}>Female</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 02 Physical & Playing -->
        <div class="detail-card">
          <div class="detail-card-head">
            <h3><i class="fa-solid fa-hand-fist" style="color: var(--profile-orange);"></i> Physical &amp; Playing Attributes</h3>
          </div>
          <div class="fields-grid">
            <div class="field-box">
              <label>Primary Position</label>
              <p>${p.primaryPos || '-'}</p>
              <select id="inpPrimaryPos">
                <option value="Centre Back / Playmaker" ${p.primaryPos==='Centre Back / Playmaker'?'selected':''}>Centre Back / Playmaker</option>
                <option value="Goalkeeper" ${p.primaryPos==='Goalkeeper'?'selected':''}>Goalkeeper</option>
                <option value="Left Wing" ${p.primaryPos==='Left Wing'?'selected':''}>Left Wing</option>
                <option value="Right Wing" ${p.primaryPos==='Right Wing'?'selected':''}>Right Wing</option>
                <option value="Pivot / Line Player" ${p.primaryPos==='Pivot / Line Player'?'selected':''}>Pivot / Line Player</option>
                <option value="Left Back" ${p.primaryPos==='Left Back'?'selected':''}>Left Back</option>
                <option value="Right Back" ${p.primaryPos==='Right Back'?'selected':''}>Right Back</option>
              </select>
            </div>
            <div class="field-box">
              <label>Playing Hand</label>
              <p>${p.hand || 'Right Hand'}</p>
              <select id="inpHand">
                <option value="Right Hand" ${p.hand==='Right Hand'?'selected':''}>Right Hand</option>
                <option value="Left Hand" ${p.hand==='Left Hand'?'selected':''}>Left Hand</option>
                <option value="Ambidextrous" ${p.hand==='Ambidextrous'?'selected':''}>Ambidextrous</option>
              </select>
            </div>
            <div class="field-box">
              <label>Height</label>
              <p>${p.height || '180 cm'}</p>
              <input type="text" id="inpHeight" value="${p.height || '180 cm'}">
            </div>
            <div class="field-box">
              <label>Weight</label>
              <p>${p.weight || '75 kg'}</p>
              <input type="text" id="inpWeight" value="${p.weight || '75 kg'}">
            </div>
          </div>
        </div>

        <!-- 03 Address & Contact -->
        <div class="detail-card">
          <div class="detail-card-head">
            <h3><i class="fa-solid fa-address-book" style="color: var(--profile-orange);"></i> Address &amp; Contact Info</h3>
          </div>
          <div class="fields-grid">
            <div class="field-box">
              <label>District</label>
              <p>${p.district || '-'}</p>
              <input type="text" id="inpDistrict" value="${p.district || ''}">
            </div>
            <div class="field-box">
              <label>State</label>
              <p>${p.state || 'Uttar Pradesh'}</p>
              <input type="text" id="inpState" value="${p.state || 'Uttar Pradesh'}">
            </div>
            <div class="field-box">
              <label>Mobile Number</label>
              <p>${p.mobile || '-'}</p>
              <input type="tel" id="inpMobile" value="${p.mobile || ''}">
            </div>
            <div class="field-box">
              <label>Email ID</label>
              <p>${p.email || '-'}</p>
              <input type="email" id="inpEmail" value="${p.email || ''}">
            </div>
          </div>
        </div>
      </div>

      <!-- STICKY SAVE BAR -->
      <div class="save-bar">
        <span style="font-weight: 700; color: #1e293b;"><i class="fa-solid fa-pen"></i> Editing Profile Details</span>
        <div>
          <button type="button" class="cancel-bar-btn" onclick="toggleEditMode()">Cancel</button>
          <button type="submit" class="save-bar-btn"><i class="fa-solid fa-check"></i> Save Changes</button>
        </div>
      </div>
    </form>
  `;
}

function toggleEditMode() {
  const form = document.getElementById('profileEditForm');
  const wrapper = document.getElementById('sectionsWrapper');
  const btn = document.getElementById('toggleEditBtn');
  const topSaveBtn = document.getElementById('topSaveBtn');
  if (!wrapper || !form) return;
  
  const isEditing = form.classList.contains('editing');
  if (isEditing) {
    form.classList.remove('editing');
    wrapper.classList.remove('editing');
    btn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit Profile';
    btn.style.background = 'var(--profile-orange)';
    if (topSaveBtn) topSaveBtn.style.display = 'none';
  } else {
    form.classList.add('editing');
    wrapper.classList.add('editing');
    btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Cancel';
    btn.style.background = '#64748b';
    if (topSaveBtn) topSaveBtn.style.display = 'inline-flex';
  }
}

function handlePhotoChange(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(evt) {
      document.getElementById('profileAvatarImg').src = evt.target.result;
      if (currentPlayerData) {
        currentPlayerData.photoUrl = evt.target.result;
      }
    };
    reader.readAsDataURL(file);
  }
}

function saveProfileChanges(e) {
  e.preventDefault();
  if (!currentPlayerData) return;

  currentPlayerData = {
    ...currentPlayerData,
    fullName: document.getElementById('inpFullName').value.trim(),
    dob: document.getElementById('inpDob').value,
    age: document.getElementById('inpAge').value.trim(),
    gender: document.getElementById('inpGender').value,
    primaryPos: document.getElementById('inpPrimaryPos').value,
    hand: document.getElementById('inpHand').value,
    height: document.getElementById('inpHeight').value.trim(),
    weight: document.getElementById('inpWeight').value.trim(),
    district: document.getElementById('inpDistrict').value.trim(),
    state: document.getElementById('inpState').value.trim(),
    mobile: document.getElementById('inpMobile').value.trim(),
    email: document.getElementById('inpEmail').value.trim(),
    photoUrl: document.getElementById('profileAvatarImg').src
  };

  sessionStorage.setItem('upphl_active_player', JSON.stringify(currentPlayerData));
  window.currentLoggedInPlayer = currentPlayerData;
  if (window.updateNavProfileState) {
    window.updateNavProfileState(currentPlayerData);
  }

  loadMyProfile();
  alert('Profile updated successfully!');
}

document.addEventListener('DOMContentLoaded', loadMyProfile);
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
