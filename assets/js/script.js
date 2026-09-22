document.addEventListener("DOMContentLoaded", () => {
  const mobileToggle = document.getElementById("mobile-toggle");
  const navMenu = document.getElementById("nav-menu");

  // Toggle Mobile Navigation Drawer
  if (mobileToggle && navMenu) {
    const mobileToggleIcon = mobileToggle.querySelector("i");

    mobileToggle.addEventListener("click", (e) => {
      e.stopPropagation();
      navMenu.classList.toggle("active");

      if (mobileToggleIcon) {
        if (navMenu.classList.contains("active")) {
          mobileToggleIcon.className = "fa-solid fa-xmark";
        } else {
          mobileToggleIcon.className = "fa-solid fa-bars";
        }
      }
    });

    // Close menu when clicking outside
    document.addEventListener("click", (e) => {
      if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
        navMenu.classList.remove("active");
        if (mobileToggleIcon) {
          mobileToggleIcon.className = "fa-solid fa-bars";
        }
      }
    });
  }

  // Accordion Dropdowns on Mobile
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
  const nestedToggles = document.querySelectorAll(".nested-toggle");

  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
      if (window.innerWidth <= 1024) {
        e.preventDefault();
        e.stopPropagation();
        const parent = toggle.parentElement;
        parent.classList.toggle("open");

        // Toggle rotation icon if needed
        const arrow = toggle.querySelector(".nav-arrow");
        if (arrow) {
          if (parent.classList.contains("open")) {
            arrow.style.transform = "rotate(180deg)";
          } else {
            arrow.style.transform = "rotate(0deg)";
          }
        }
      }
    });
  });

  nestedToggles.forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
      if (window.innerWidth <= 1024) {
        e.preventDefault();
        e.stopPropagation();
        const parent = toggle.parentElement;
        parent.classList.toggle("open");

        const arrow = toggle.querySelector(".nav-arrow");
        if (arrow) {
          if (parent.classList.contains("open")) {
            arrow.style.transform = "rotate(90deg)";
          } else {
            arrow.style.transform = "rotate(0deg)";
          }
        }
      }
    });
  });

  // Standings Tab Switching Logic
  const tabButtons = document.querySelectorAll(".tabs-container .tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-target");

      // Remove active state from all buttons
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      // Add active state to clicked button
      button.classList.add("active");

      // Hide all tab contents
      tabContents.forEach((content) => content.classList.remove("active"));
      // Show target tab content
      const targetContent = document.getElementById(targetId);
      if (targetContent) {
        targetContent.classList.add("active");
      }
    });
  });

  // Stats Count-up Counter Logic (Smooth Easing Counter Animation)
  const counters = document.querySelectorAll(".stat-number");

  const animateCounter = (counter) => {
    const rawTarget = counter.getAttribute("data-target");
    const target = parseFloat(rawTarget);
    if (isNaN(target)) return;

    const suffix = counter.getAttribute("data-suffix") || "";
    const decimals = parseInt(counter.getAttribute("data-decimals") || "0");
    const duration = 1800; // 1.8s smooth animation
    const startTime = performance.now();

    const numSpan = counter.querySelector(".counter-num");
    const suffixSpan = counter.querySelector(".counter-suffix");

    const easeOutQuad = (t) => t * (2 - t);

    const step = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = easeOutQuad(progress);
      const currentVal = easedProgress * target;

      const formattedVal = decimals > 0 ? currentVal.toFixed(decimals) : Math.floor(currentVal).toString();

      if (numSpan) {
        numSpan.innerText = formattedVal;
        if (suffixSpan && suffixSpan.innerText !== suffix) {
          suffixSpan.innerText = suffix;
        }
      } else {
        counter.innerText = formattedVal + suffix;
      }

      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        const finalVal = decimals > 0 ? target.toFixed(decimals) : target.toString();
        if (numSpan) {
          numSpan.innerText = finalVal;
        } else {
          counter.innerText = finalVal + suffix;
        }
      }
    };

    requestAnimationFrame(step);
  };

  if (counters.length > 0) {
    if ('IntersectionObserver' in window) {
      const statsObserver = new IntersectionObserver(
        (entries, observer) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              animateCounter(entry.target);
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.25 }
      );
      counters.forEach((counter) => statsObserver.observe(counter));
    } else {
      counters.forEach((counter) => animateCounter(counter));
    }
  }

  // Upcoming Matches Carousel Slider Logic
  const track = document.querySelector(".carousel-track");
  const cards = document.querySelectorAll(".carousel-match-card");
  const prevBtn = document.querySelector(".prev-btn");
  const nextBtn = document.querySelector(".next-btn");

  if (track && cards.length > 0) {
    let currentIndex = 0;

    function getVisibleCards() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }

    function slideCarousel() {
      const visibleCount = getVisibleCards();
      const maxIndex = Math.max(0, cards.length - visibleCount);

      if (currentIndex > maxIndex) currentIndex = maxIndex;
      if (currentIndex < 0) currentIndex = 0;

      const firstCard = cards[0];
      const cardWidth = firstCard.offsetWidth;
      const gap = parseFloat(window.getComputedStyle(track).gap) || 20;
      const translateOffset = currentIndex * (cardWidth + gap);

      track.style.transform = `translateX(-${translateOffset}px)`;

      // Update button states
      if (prevBtn) {
        prevBtn.disabled = currentIndex === 0;
        prevBtn.style.opacity = currentIndex === 0 ? "0.4" : "1";
        prevBtn.style.cursor = currentIndex === 0 ? "not-allowed" : "pointer";
      }
      if (nextBtn) {
        nextBtn.disabled = currentIndex >= maxIndex;
        nextBtn.style.opacity = currentIndex >= maxIndex ? "0.4" : "1";
        nextBtn.style.cursor =
          currentIndex >= maxIndex ? "not-allowed" : "pointer";
      }
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const visibleCount = getVisibleCards();
        const maxIndex = Math.max(0, cards.length - visibleCount);
        if (currentIndex < maxIndex) {
          currentIndex++;
          slideCarousel();
        }
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener("click", (e) => {
        e.preventDefault();
        if (currentIndex > 0) {
          currentIndex--;
          slideCarousel();
        }
      });
    }

    // Touch swipe support for mobile/touchscreens
    let touchStartX = 0;
    let touchEndX = 0;
    let isTouching = false;

    track.addEventListener(
      "touchstart",
      (e) => {
        touchStartX = e.touches[0].clientX;
        touchEndX = touchStartX;
        isTouching = true;
      },
      { passive: true },
    );

    track.addEventListener(
      "touchmove",
      (e) => {
        if (!isTouching) return;
        touchEndX = e.touches[0].clientX;
      },
      { passive: true },
    );

    track.addEventListener(
      "touchend",
      () => {
        if (!isTouching) return;
        isTouching = false;
        const diffX = touchStartX - touchEndX;
        const visibleCount = getVisibleCards();
        const maxIndex = Math.max(0, cards.length - visibleCount);
        const threshold = 40; // minimum swipe distance

        if (diffX > threshold && currentIndex < maxIndex) {
          // Swipe Left -> Next
          currentIndex++;
          slideCarousel();
        } else if (diffX < -threshold && currentIndex > 0) {
          // Swipe Right -> Prev
          currentIndex--;
          slideCarousel();
        }
      },
      { passive: true },
    );

    window.addEventListener("resize", slideCarousel);

    // Initial setup timeout to ensure layout calculations are complete
    setTimeout(slideCarousel, 100);
    window.addEventListener("load", slideCarousel);
  }

  // Dynamic Scroll to Top Button
  let scrollTopBtn = document.getElementById("scroll-top-btn");
  if (!scrollTopBtn) {
    scrollTopBtn = document.createElement("button");
    scrollTopBtn.id = "scroll-top-btn";
    scrollTopBtn.className = "scroll-top-btn";
    scrollTopBtn.setAttribute("aria-label", "Scroll to top");
    scrollTopBtn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
    document.body.appendChild(scrollTopBtn);
  }

  const toggleScrollTopBtn = () => {
    if (window.scrollY > 300) {
      scrollTopBtn.classList.add("show");
    } else {
      scrollTopBtn.classList.remove("show");
    }
  };

  window.addEventListener("scroll", toggleScrollTopBtn);
  toggleScrollTopBtn();

  // Fixed Sticky Navigation Bar Handler (No Disappearing, No Overlap, No Jump)
  const siteHeader = document.querySelector("header:not(.hero)");
  const topBar = document.querySelector(".top-bar");

  if (siteHeader) {
    // Create dynamic spacer element behind header to prevent layout jump
    let spacer = document.querySelector(".header-spacer");
    if (!spacer) {
      spacer = document.createElement("div");
      spacer.className = "header-spacer";
      siteHeader.parentNode.insertBefore(spacer, siteHeader.nextSibling);
    }

    const winnerSection = document.getElementById("winners-runners");

    const handleScroll = () => {
      const topBarHeight = topBar ? topBar.offsetHeight : 0;
      const headerHeight = siteHeader.offsetHeight;

      if (window.scrollY > topBarHeight) {
        siteHeader.classList.add("is-fixed");
        spacer.style.height = headerHeight + "px";
        spacer.classList.add("active");
      } else {
        siteHeader.classList.remove("is-fixed");
        spacer.classList.remove("active");
      }

      // Hide header completely when viewing the Winner & Runner glory section so images display edge-to-edge
      if (winnerSection) {
        const rect = winnerSection.getBoundingClientRect();
        if (rect.top <= headerHeight + 10 && rect.bottom > 50) {
          siteHeader.classList.add("is-nav-hidden-section");
        } else {
          siteHeader.classList.remove("is-nav-hidden-section");
        }
      }
    };

    window.addEventListener("scroll", handleScroll, { passive: true });
    window.addEventListener("resize", handleScroll);
    handleScroll();
  }

  scrollTopBtn.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  // Global Navigation Profile Manager
  window.updateNavProfileState = function (playerObject) {
    const navLinks = document.querySelector(".nav-links");
    if (!navLinks) return;

    let profileLi = navLinks.querySelector("li.nav-user-profile-item");

    let activePlayer = playerObject || window.currentLoggedInPlayer;
    if (!activePlayer) {
      try {
        const saved =
          sessionStorage.getItem("upphl_active_player") ||
          localStorage.getItem("upphl_active_player");
        if (saved) activePlayer = JSON.parse(saved);
      } catch (e) {}
    }

    if (activePlayer) {
      try {
        const name = activePlayer.fullName || "Player";
        const firstName = name.trim().split(" ")[0] || "Player";
        const playerId = activePlayer.playerId || "UPPHL-S2-001";
        const photo =
          activePlayer.photoUrl || "assets/images/default-player.png";

        if (!profileLi) {
          profileLi = document.createElement("li");
          profileLi.className = "dropdown nav-user-profile-item";
          navLinks.appendChild(profileLi);
        }

        profileLi.innerHTML = `
                    <a href="my-profile.php" class="dropdown-toggle nav-user-badge" title="${name} (${playerId})">
                        <img src="${photo}" alt="${name}" class="nav-user-avatar" id="navAvatarImg">
                        <span class="nav-user-firstname">${firstName}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-user-header">
                            <strong>${name}</strong>
                            <small>ID: ${playerId}</small>
                        </li>
                        <li>
                            <a href="my-profile.php">
                                <i class="fa-solid fa-id-card"></i>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a href="#" class="logout-btn" onclick="logoutPlayer(event)">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                `;
      } catch (e) {}
    } else {
      if (profileLi) {
        profileLi.remove();
      }
    }
  };

  window.logoutPlayer = function (e) {
    if (e) e.preventDefault();
    sessionStorage.removeItem("upphl_active_player");
    localStorage.removeItem("upphl_active_player");
    window.currentLoggedInPlayer = null;
    window.updateNavProfileState(null);
    window.location.href = "index.php";
  };

  window.updateNavProfileState();

  // Global Player Login Modal Trigger
  window.openPlayerLoginModal = function (e) {
    if (e) e.preventDefault();
    let modal = document.getElementById("playerLoginModal");
    if (!modal) {
      // Auto inject global login modal into body if not present
      const modalDiv = document.createElement("div");
      modalDiv.className = "my-profile-modal";
      modalDiv.id = "playerLoginModal";
      modalDiv.innerHTML = `
              <div class="my-profile-modal-backdrop" onclick="closePlayerLoginModal()"></div>
              <div class="my-profile-modal-card" style="max-width: 440px;">
                <button class="modal-close-btn" type="button" onclick="closePlayerLoginModal()">&times;</button>
                <div class="profile-modal-header" style="text-align: center; display: block; padding: 24px 20px 16px;">
                  <div style="width: 54px; height: 54px; border-radius: 50%; background: #fff4eb; color: #f47b20; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto 12px;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                  </div>
                  <h2 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 4px;">Player Login</h2>
                  <p style="font-size: 13px; color: #64748b; margin: 0;">Enter your Player ID or Registered Mobile Number</p>
                </div>
                <form onsubmit="handleGlobalLoginSubmit(event)" style="padding: 20px 24px 28px;">
                  <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Player ID / Mobile Number</label>
                    <input type="text" id="loginPlayerIdInput" required placeholder="e.g. UPPHL-S2-001 or 9876543210" style="width: 100%; padding: 11px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                  </div>
                  <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Password</label>
                    <input type="password" id="loginPasswordInput" required placeholder="Enter Generated Password" style="width: 100%; padding: 11px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                  </div>
                  <div id="loginErrorMsg" style="display: none; background: #fef2f2; color: #ef4444; padding: 8px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-bottom: 16px; border: 1px solid #fecaca;">
                    Invalid credentials. Please enter valid Player ID/Mobile & Password.
                  </div>
                  <button type="submit" style="width: 100%; background: #f47b20; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer;">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login Now
                  </button>
                </form>
              </div>
            `;
      document.body.appendChild(modalDiv);
      modal = modalDiv;
    }

    const err = document.getElementById("loginErrorMsg");
    if (err) err.style.display = "none";
    modal.classList.add("open");
    document.body.style.overflow = "hidden";
  };

  window.closePlayerLoginModal = function () {
    const modal = document.getElementById("playerLoginModal");
    if (modal) {
      modal.classList.remove("open");
      document.body.style.overflow = "";
    }
  };

  window.handleGlobalLoginSubmit = function (e) {
    e.preventDefault();
    const inputVal = (document.getElementById("loginPlayerIdInput").value || "")
      .trim()
      .toLowerCase();
    const passVal = (
      document.getElementById("loginPasswordInput")?.value || ""
    ).trim();
    const err = document.getElementById("loginErrorMsg");

    fetch("api/get-approved-players.php")
      .then((r) => r.json())
      .then((res) => {
        const players = res.players || [];
        const match = players.find((p) => {
          const idOrMobileMatch =
            (p.playerId || "").toLowerCase() === inputVal ||
            (p.mobile || "").toLowerCase() === inputVal ||
            (p.fullName || "").toLowerCase() === inputVal;
          const passMatch = !p.password || p.password === passVal;
          return idOrMobileMatch && passMatch;
        });

        if (match) {
          sessionStorage.setItem("upphl_active_player", JSON.stringify(match));
          localStorage.setItem("upphl_active_player", JSON.stringify(match));
          if (window.updateNavProfileState) {
            window.updateNavProfileState(match);
          }
          window.closePlayerLoginModal();
          window.location.href =
            "my-profile.php?id=" + encodeURIComponent(match.playerId);
        } else {
          if (err) {
            err.style.display = "block";
            err.textContent =
              "Invalid credentials. Please enter valid Player ID/Mobile & Password.";
          }
        }
      })
      .catch(() => {
        if (err) {
          err.style.display = "block";
          err.textContent = "Connection error. Please try again.";
        }
      });
  };

  // =========================================================
  // UPPHL OFFICIAL FRANCHISE TEAMS DATASET (DYNAMIC + FALLBACK)
  // =========================================================
  const defaultTeamsData = [
    {
      id: "mathura-brij-star",
      name: "Mathura Brij Star",
      city: "Mathura",
      logo: "assets/images/teams/1-mathura-brij-star.jpeg",
      gradient: "linear-gradient(135deg, #18181b 0%, #991b1b 50%, #ea580c 100%)",
      accentColor: "#ea580c",
      owner: {
        name: "Braj Bhoomi Sports",
        role: "Franchise Owner",
        avatarText: "BS",
        avatarBg: "#881337",
      },
      coach: {
        name: "Jagdish Sharma",
        role: "Head Coach",
        avatarText: "JS",
        avatarBg: "#0284c7",
      },
      captain: {
        name: "Krishna Yadav",
        role: "Team Captain",
        avatarText: "KY",
        avatarBg: "#16a34a",
      },
      players: [
        { name: "Krishna Yadav", pos: "Pivot", no: "09" },
        { name: "Brijesh Kumar", pos: "Goalkeeper", no: "01" },
        { name: "Radhey Shyam", pos: "Centre Back", no: "07" },
        { name: "Mohit Chaturvedi", pos: "Left Wing", no: "11" },
        { name: "Govind Goswami", pos: "Right Wing", no: "03" },
        { name: "Dharmendra Baghel", pos: "Left Back", no: "15" },
        { name: "Sunil Agrawal", pos: "Right Back", no: "08" },
        { name: "Hemant Saini", pos: "Defender", no: "20" },
      ],
    },
    {
      id: "barbarik-warriors",
      name: "Barbarik Warriors",
      city: "Bhadohi",
      logo: "assets/images/teams/2-barabarik.jpeg",
      gradient: "linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #b45309 100%)",
      accentColor: "#f59e0b",
      owner: {
        name: "Jeevan Sanchay Trust",
        role: "Franchise Owner",
        avatarText: "JS",
        avatarBg: "#b45309",
      },
      coach: {
        name: "Balwant Singh",
        role: "Head Coach",
        avatarText: "BS",
        avatarBg: "#0284c7",
      },
      captain: {
        name: "Aditya Pratap",
        role: "Team Captain",
        avatarText: "AP",
        avatarBg: "#16a34a",
      },
      players: [
        { name: "Aditya Pratap", pos: "Centre Back", no: "07" },
        { name: "Sameer Dixit", pos: "Goalkeeper", no: "01" },
        { name: "Shivam Upadhyay", pos: "Left Wing", no: "09" },
        { name: "Harshvardhan Rai", pos: "Right Wing", no: "11" },
        { name: "Deepak Maurya", pos: "Pivot / Line", no: "14" },
        { name: "Amit Bhardwaj", pos: "Left Back", no: "04" },
        { name: "Vikas Pathak", pos: "Right Back", no: "08" },
        { name: "Ankit Tiwari", pos: "Defender", no: "18" },
      ],
    },
    {
      id: "noida-blasters",
      name: "Noida Blasters",
      city: "Noida",
      logo: "assets/images/teams/3-noida-blasters.jpeg",
      gradient: "linear-gradient(135deg, #0c4a6e 0%, #0284c7 50%, #f97316 100%)",
      accentColor: "#0284c7",
      owner: {
        name: "Noida Media & Sports",
        role: "Franchise Owner",
        avatarText: "NM",
        avatarBg: "#0891b2",
      },
      coach: {
        name: "S. K. Kapoor",
        role: "Head Coach",
        avatarText: "SK",
        avatarBg: "#0284c7",
      },
      captain: {
        name: "Devendra Pal",
        role: "Team Captain",
        avatarText: "DP",
        avatarBg: "#16a34a",
      },
      players: [
        { name: "Devendra Pal", pos: "Left Wing", no: "11" },
        { name: "Aryan Tyagi", pos: "Goalkeeper", no: "01" },
        { name: "Kunal Chaudhary", pos: "Centre Back", no: "06" },
        { name: "Rishabh Bhati", pos: "Right Wing", no: "04" },
        { name: "Naveen Gurjar", pos: "Pivot", no: "12" },
        { name: "Varun Tomar", pos: "Left Back", no: "18" },
        { name: "Sahil Malik", pos: "Right Back", no: "08" },
        { name: "Prateek Sharma", pos: "Defender", no: "15" },
      ],
    },
    {
      id: "kashi-kings",
      name: "Kashi Kings",
      city: "Varanasi",
      logo: "assets/images/teams/4-kashi-king.jpeg",
      gradient: "linear-gradient(135deg, #0f172a 0%, #78350f 45%, #eab308 100%)",
      accentColor: "#eab308",
      owner: {
        name: "Ganga Sports Group",
        role: "Franchise Owner",
        avatarText: "GS",
        avatarBg: "#b45309",
      },
      coach: {
        name: "Vikram Singh",
        role: "Head Coach",
        avatarText: "VS",
        avatarBg: "#0284c7",
      },
      captain: {
        name: "Rajesh Kumar",
        role: "Team Captain",
        avatarText: "RK",
        avatarBg: "#16a34a",
      },
      players: [
        { name: "Rajesh Kumar", pos: "Centre Back", no: "08" },
        { name: "Sandeep Chaurasia", pos: "Goalkeeper", no: "01" },
        { name: "Pradeep Bind", pos: "Left Wing", no: "09" },
        { name: "Vivek Jaiswal", pos: "Right Wing", no: "11" },
        { name: "Avinash Dubey", pos: "Pivot", no: "04" },
        { name: "Nitin Srivastava", pos: "Left Back", no: "14" },
        { name: "Rohit Sonkar", pos: "Right Back", no: "17" },
        { name: "Akash Patel", pos: "Defender", no: "21" },
      ],
    },
    {
      id: "ghaziabad-panthers",
      name: "Ghaziabad Panthers",
      city: "Ghaziabad",
      logo: "assets/images/teams/5-ghaziabad.jpeg",
      gradient: "linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%)",
      accentColor: "#3b82f6",
      owner: {
        name: "Ghaziabad Sports Arena",
        role: "Franchise Owner",
        avatarText: "GS",
        avatarBg: "#1e40af",
      },
      coach: {
        name: "Alok Pandey",
        role: "Head Coach",
        avatarText: "AP",
        avatarBg: "#0284c7",
      },
      captain: {
        name: "Rahul Yadav",
        role: "Team Captain",
        avatarText: "RY",
        avatarBg: "#16a34a",
      },
      players: [
        { name: "Rahul Yadav", pos: "Pivot", no: "10" },
        { name: "Shivendra Shukla", pos: "Goalkeeper", no: "01" },
        { name: "Piyush Kesarwani", pos: "Centre Back", no: "08" },
        { name: "Ashish Jaiswal", pos: "Left Wing", no: "11" },
        { name: "Saurabh Bind", pos: "Right Wing", no: "04" },
        { name: "Saurav Malviya", pos: "Left Back", no: "15" },
        { name: "Mayank Mishra", pos: "Right Back", no: "06" },
        { name: "Durgesh Srivastava", pos: "Defender", no: "19" },
      ],
    },
    {
      id: "gorakhpur-rowdies",
      name: "Gorakhpur Rowdies",
      city: "Gorakhpur",
      logo: "assets/images/teams/6-gorakhpur-rowdies.jpeg",
      gradient: "linear-gradient(135deg, #1e3a8a 0%, #ea580c 60%, #f97316 100%)",
      accentColor: "#ea580c",
      owner: {
        name: "Manoj Gorakhpuri",
        role: "Franchise Owner",
        avatarText: "MG",
        avatarBg: "#1d4ed8",
      },
      coach: {
        name: "S. N. Tripathi",
        role: "Head Coach",
        avatarText: "ST",
        avatarBg: "#0284c7",
      },
      captain: {
        name: "Rudra Mishra",
        role: "Team Captain",
        avatarText: "RM",
        avatarBg: "#16a34a",
      },
      players: [
        { name: "Rudra Mishra", pos: "Left Back", no: "10" },
        { name: "Pankaj Chauhan", pos: "Goalkeeper", no: "12" },
        { name: "Alok Vishwakarma", pos: "Pivot", no: "05" },
        { name: "Sonu Yadav", pos: "Right Wing", no: "03" },
        { name: "Rakesh Pandey", pos: "Centre Back", no: "06" },
        { name: "Suraj Shukla", pos: "Left Wing", no: "15" },
        { name: "Manish Gond", pos: "Defender", no: "02" },
        { name: "Abhishek Gupta", pos: "Right Back", no: "19" },
      ],
    },
  ];

  const teamsData = (window.UPPHL_TEAMS_DATA && Array.isArray(window.UPPHL_TEAMS_DATA) && window.UPPHL_TEAMS_DATA.length > 0)
    ? window.UPPHL_TEAMS_DATA
    : defaultTeamsData;

  // Helper for circular avatar SVG or photo
  function generateAvatarSvg(name, bg) {
    const initials = (name || "UP")
      .split(" ")
      .map((n) => n[0])
      .slice(0, 2)
      .join("")
      .toUpperCase();
    return `<svg viewBox="0 0 80 80" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="38" fill="${bg || "#1e293b"}" />
            <circle cx="40" cy="40" r="36" fill="#0f172a" fill-opacity="0.25" />
            <text x="40" y="47" text-anchor="middle" fill="#ffffff" font-family="'Outfit', sans-serif" font-weight="800" font-size="24" letter-spacing="1">${initials}</text>
        </svg>`;
  }

  function renderPersonAvatar(person, fallbackBg) {
    if (person && person.photoUrl && person.photoUrl.trim() !== "") {
      return `<img src="${person.photoUrl}" alt="${person.name || 'Member'}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;" />`;
    }
    return generateAvatarSvg(person ? person.name : "Member", fallbackBg);
  }

  // =========================================================
  // 1. HOMEPAGE AUTOSLIDER FOR TEAMS (index.php)
  // =========================================================
  const teamsCarouselTrack = document.getElementById("teamsCarouselTrack");
  const teamsPrevBtn = document.getElementById("teamsPrevBtn");
  const teamsNextBtn = document.getElementById("teamsNextBtn");
  const teamsViewport = document.getElementById("teamsViewport");

  if (teamsCarouselTrack) {
    let teamIndex = 0;
    let autoSlideTimer = null;

    // Render Team Cards linking to team-details.php?team=...
    teamsCarouselTrack.innerHTML = teamsData
      .map(
        (team) => `
            <a href="team-details.php?team=${team.id}" class="team-card-link" title="${team.name}">
                <img src="${team.posterImage || team.logo}" alt="${team.name}" class="team-card-banner-img" />
            </a>
        `,
      )
      .join("");

    const cards = teamsCarouselTrack.querySelectorAll(".team-card-link");

    function getVisibleTeamCards() {
      if (window.innerWidth <= 480) return 1;
      if (window.innerWidth <= 768) return 2;
      if (window.innerWidth <= 1024) return 3;
      return 4;
    }

    function slideTeamsCarousel() {
      const visibleCount = getVisibleTeamCards();
      const maxIndex = Math.max(0, cards.length - visibleCount);

      if (teamIndex > maxIndex) teamIndex = 0; // Infinite loop forward
      if (teamIndex < 0) teamIndex = maxIndex;

      const firstCard = cards[0];
      if (!firstCard) return;
      const cardWidth = firstCard.offsetWidth;
      const gap =
        parseFloat(window.getComputedStyle(teamsCarouselTrack).gap) || 18;
      const translateOffset = teamIndex * (cardWidth + gap);

      teamsCarouselTrack.style.transform = `translateX(-${translateOffset}px)`;
    }

    function startAutoSlide() {
      stopAutoSlide();
      autoSlideTimer = setInterval(() => {
        const visibleCount = getVisibleTeamCards();
        const maxIndex = Math.max(0, cards.length - visibleCount);
        if (teamIndex < maxIndex) {
          teamIndex++;
        } else {
          teamIndex = 0;
        }
        slideTeamsCarousel();
      }, 2800);
    }

    function stopAutoSlide() {
      if (autoSlideTimer) {
        clearInterval(autoSlideTimer);
        autoSlideTimer = null;
      }
    }

    if (teamsNextBtn) {
      teamsNextBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const visibleCount = getVisibleTeamCards();
        const maxIndex = Math.max(0, cards.length - visibleCount);
        if (teamIndex < maxIndex) {
          teamIndex++;
        } else {
          teamIndex = 0;
        }
        slideTeamsCarousel();
        startAutoSlide();
      });
    }

    if (teamsPrevBtn) {
      teamsPrevBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const visibleCount = getVisibleTeamCards();
        const maxIndex = Math.max(0, cards.length - visibleCount);
        if (teamIndex > 0) {
          teamIndex--;
        } else {
          teamIndex = maxIndex;
        }
        slideTeamsCarousel();
        startAutoSlide();
      });
    }

    // Pause on mouse hover / resume on leave
    if (teamsViewport) {
      teamsViewport.addEventListener("mouseenter", stopAutoSlide);
      teamsViewport.addEventListener("mouseleave", startAutoSlide);
    }

    window.addEventListener("resize", slideTeamsCarousel);
    slideTeamsCarousel();
    startAutoSlide();
  }

  // =========================================================
  // 2. DEDICATED TEAM DETAILS PAGE LOGIC (team-details.php)
  // =========================================================
  const dedicatedTeamContainer = document.getElementById(
    "dedicatedTeamContainer",
  );
  const teamTabSelector = document.getElementById("teamTabSelector");
  const breadcrumbTeamName = document.getElementById("breadcrumbTeamName");
  const heroTeamTitle = document.getElementById("heroTeamTitle");

  if (dedicatedTeamContainer && teamTabSelector) {
    // Read ?team=... from URL query param
    const urlParams = new URLSearchParams(window.location.search);
    let activeTeamId = urlParams.get("team") || "barbarik-warriors";

    let currentTeam =
      teamsData.find((t) => t.id === activeTeamId) || teamsData[0];

    function renderTeamPage(team) {
      if (breadcrumbTeamName) breadcrumbTeamName.textContent = team.name;
      if (heroTeamTitle)
        heroTeamTitle.textContent = `${team.name} - Franchise Squad`;

      // 1. Render Selector Pill Tabs
      teamTabSelector.innerHTML = teamsData
        .map(
          (t) => `
                <button class="team-tab-btn ${t.id === team.id ? "active" : ""}" data-id="${t.id}">
                    <i class="fa-solid fa-shield"></i> ${t.name}
                </button>
            `,
        )
        .join("");

      // Add click listeners to tab buttons
      teamTabSelector.querySelectorAll(".team-tab-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
          const tid = btn.getAttribute("data-id");
          const targetTeam = teamsData.find((t) => t.id === tid);
          if (targetTeam) {
            history.pushState(
              null,
              "",
              `team-details.php?team=${targetTeam.id}`,
            );
            renderTeamPage(targetTeam);
          }
        });
      });

      // 2. Render Main Dedicated Card
      dedicatedTeamContainer.innerHTML = `
                <div class="team-main-profile-card">
                    <!-- Top Team Profile Banner -->
                    <div class="team-details-header-banner" style="background: ${team.gradient};">
                        <div class="team-banner-left">
                            <div class="team-banner-crest">
                                <img src="${team.logo}" alt="${team.name}" />
                            </div>
                            <div class="team-banner-text">
                                <h2>${team.name}</h2>
                                <p><i class="fa-solid fa-location-dot"></i> Home City: <strong>${team.city}</strong> &bull; UP Pro Handball League</p>
                            </div>
                        </div>
                        <div class="team-banner-stats">
                            <div class="team-badge-pill"><i class="fa-solid fa-users"></i> ${(team.players || []).length} Squad Athletes</div>
                            <div class="team-badge-pill"><i class="fa-solid fa-award"></i> ${team.season || 'Season 1'} Contender</div>
                        </div>
                    </div>

                    <!-- Leadership / Management -->
                    <div class="details-group-heading">
                        <i class="fa-solid fa-crown"></i> Team Leadership & Management
                    </div>
                    <div class="details-leadership-grid">
                        <!-- Owner (Malik) -->
                        <div class="details-leader-card">
                            <div class="leader-avatar-circle" style="border-color: #f59e0b;">
                                ${renderPersonAvatar(team.owner, team.owner ? team.owner.avatarBg : '#b45309')}
                            </div>
                            <div class="leader-info">
                                <h4>${team.owner ? team.owner.name : 'Franchise Management'}</h4>
                                <span class="leader-badge badge-owner"><i class="fa-solid fa-award"></i> ${team.owner ? team.owner.role : 'Franchise Owner'}</span>
                            </div>
                        </div>

                        <!-- Head Coach -->
                        <div class="details-leader-card">
                            <div class="leader-avatar-circle" style="border-color: #0ea5e9;">
                                ${renderPersonAvatar(team.coach, team.coach ? team.coach.avatarBg : '#0284c7')}
                            </div>
                            <div class="leader-info">
                                <h4>${team.coach ? team.coach.name : 'Head Coach'}</h4>
                                <span class="leader-badge badge-coach"><i class="fa-solid fa-clipboard-user"></i> ${team.coach ? team.coach.role : 'Head Coach'}</span>
                            </div>
                        </div>

                        <!-- Team Captain -->
                        <div class="details-leader-card">
                            <div class="leader-avatar-circle" style="border-color: #10b981;">
                                ${renderPersonAvatar(team.captain, team.captain ? team.captain.avatarBg : '#16a34a')}
                            </div>
                            <div class="leader-info">
                                <h4>${team.captain ? team.captain.name : 'Team Captain'}</h4>
                                <span class="leader-badge badge-captain"><i class="fa-solid fa-star"></i> ${team.captain ? team.captain.role : 'Team Captain'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team Squad / Players Grid -->
                    <div class="details-group-heading">
                        <i class="fa-solid fa-hand-fist"></i> Team Squad (Players)
                    </div>
                    <div class="details-players-grid">
                        ${(team.players && team.players.length > 0) ? team.players
                          .map(
                            (player) => `
                            <div class="details-player-card">
                                <div class="player-avatar-circle" style="border-color: ${team.accentColor || '#ea580c'};">
                                    ${renderPersonAvatar(player, team.accentColor || '#ea580c')}
                                </div>
                                <div class="player-card-details">
                                    <h5>${player.name}</h5>
                                    <span class="player-pos-text"><i class="fa-solid fa-circle-dot" style="color: ${team.accentColor || '#ea580c'}; font-size: 6px;"></i> ${player.pos}</span>
                                </div>
                                <div class="player-jersey-tag">#${player.no}</div>
                            </div>
                        `,
                          )
                          .join("") : '<p style="color:#64748b; padding:15px; grid-column:1/-1;">No players added to squad yet.</p>'}
                    </div>
                </div>
            `;
    }

    renderTeamPage(currentTeam);
  }

  // =========================================================
  // 3. TEAM-START.PHP TAB SWITCHER (TEAMS vs PLAYER STATS)
  // =========================================================
  window.switchTeamStartTab = function (tabName) {
    const teamsView = document.getElementById("teamsView");
    const playersView = document.getElementById("playersView");
    const tabTeamsBtn = document.getElementById("tabTeamsBtn");
    const tabPlayersBtn = document.getElementById("tabPlayersBtn");
    const heroTitle = document.getElementById("teamsHeroTitle");
    const heroDesc = document.getElementById("teamsHeroDesc");

    if (tabName === "teams") {
      if (teamsView) teamsView.classList.add("active");
      if (playersView) playersView.classList.remove("active");
      if (tabTeamsBtn) tabTeamsBtn.classList.add("active");
      if (tabPlayersBtn) tabPlayersBtn.classList.remove("active");
      if (heroTitle) heroTitle.textContent = "Franchise Teams";
      if (heroDesc)
        heroDesc.textContent =
          "Meet the 6 official franchise teams competing in the UP Pro Handball League.";
    } else if (tabName === "players") {
      if (teamsView) teamsView.classList.remove("active");
      if (playersView) playersView.classList.add("active");
      if (tabTeamsBtn) tabTeamsBtn.classList.remove("active");
      if (tabPlayersBtn) tabPlayersBtn.classList.add("active");
      if (heroTitle) heroTitle.textContent = "Player Stats & MVP Stars";
      if (heroDesc)
        heroDesc.textContent =
          "Top standout MVP performers, scorers, and defensive leaders across all franchises.";
    }
  };

  // =========================================================
  // 5. HANDBALL THROW & GOAL PAGE LOADER CONTROLLER
  // =========================================================
  function initHandballLoader() {
    const pageLoader = document.getElementById("pageLoader");
    if (!pageLoader) return;

    const trackFill = document.getElementById("trackFill");
    const flyingBall = document.getElementById("flyingBall");
    const loaderPercent = document.getElementById("loaderPercent");
    const loaderStatus = document.getElementById("loaderStatus");
    const goalFlash = document.getElementById("goalFlash");
    const goalPopup = document.getElementById("goalPopup");
    const goalNet = document.getElementById("goalNet");

    let currentPercent = 0;
    const totalDuration = 1600; // 1.6s total time
    const intervalTime = 16; // ~60fps
    const stepIncrement = 100 / (totalDuration / intervalTime);

    const progressTimer = setInterval(() => {
      currentPercent += stepIncrement;
      const displayPercent = Math.min(100, Math.floor(currentPercent));

      // Update UI: Orange line fill, flying ball position, text percentage
      if (trackFill) trackFill.style.width = displayPercent + "%";
      if (flyingBall) flyingBall.style.left = displayPercent + "%";
      if (loaderPercent) loaderPercent.textContent = displayPercent + "%";

      // Status message based on progress
      if (displayPercent >= 35 && displayPercent < 75 && loaderStatus) {
        loaderStatus.textContent = "PASSING MID-COURT... 50%";
      } else if (displayPercent >= 75 && displayPercent < 100 && loaderStatus) {
        loaderStatus.textContent = "STRIKING TOWARDS GOAL...";
      }

      if (displayPercent >= 100) {
        clearInterval(progressTimer);
        if (loaderPercent) loaderPercent.textContent = "100%";
        if (trackFill) trackFill.style.width = "100%";
        if (flyingBall) flyingBall.style.left = "100%";

        if (loaderStatus) {
          loaderStatus.textContent = "GOAL! READY TO PLAY";
          loaderStatus.style.color = "#16a34a";
        }

        // Trigger Goal Net flash — add class to FA icon
        if (goalFlash) {
          goalFlash.classList.add("goal-scored");
        }
        if (goalPopup) {
          goalPopup.style.opacity = "1";
          goalPopup.style.transform = "scale(1) rotate(0deg)";
        }
        if (goalNet) {
          goalNet.style.transform = "scale(1.2) rotate(4deg)";
          setTimeout(() => {
            if (goalNet) goalNet.style.transform = "scale(1) rotate(0deg)";
          }, 250);
        }

        // Smoothly dismiss loader
        setTimeout(() => {
          pageLoader.classList.add("loaded");
          setTimeout(() => {
            pageLoader.style.display = "none";
          }, 400);
        }, 350);
      }
    }, intervalTime);

    // Fail-safe safety timer
    setTimeout(() => {
      if (pageLoader && !pageLoader.classList.contains("loaded")) {
        pageLoader.classList.add("loaded");
        setTimeout(() => {
          pageLoader.style.display = "none";
        }, 400);
      }
    }, 2600);
  }

  initHandballLoader();

  // ==========================================
  // DYNAMIC HERO BANNERS & SLIDER SYSTEM
  // ==========================================
  function initHeroBanners() {
    const heroSection = document.querySelector(".hero-section");
    if (!heroSection) return;

    fetch("api/get-banners.php?active_only=true")
      .then((res) => res.json())
      .then((data) => {
        if (!data || !data.success || !data.banners || data.banners.length === 0) {
          // No dynamic banners: keep default static hero
          return;
        }

        const banners = data.banners;

        function buildSlideHtml(b, isActive = false) {
          const isVideo = b.mediaType === "video";
          const mediaHtml = isVideo
            ? `<video class="hero-video-bg" autoplay muted loop playsinline src="${b.mediaUrl}"></video><div class="hero-overlay-dynamic"></div>`
            : `<div class="hero-video-bg" style="background-image: url('${b.mediaUrl}'); background-size: cover; background-position: center; animation: heroZoom 14s ease-in-out infinite alternate;"></div><div class="hero-overlay-dynamic"></div>`;

          const badgeHtml = b.badgeText
            ? `<span class="hero-badge"><i class="fa-solid fa-fire"></i> ${escapeHtml(b.badgeText)}</span>`
            : `<span class="hero-badge"><i class="fa-solid fa-fire"></i> UP PRO HANDBALL LEAGUE</span>`;

          const btn1Html = b.btn1Text
            ? `<a href="${escapeHtml(b.btn1Link || '#')}" class="btn btn-primary">${escapeHtml(b.btn1Text)} <i class="fa-solid fa-arrow-right"></i></a>`
            : "";

          const btn2Html = b.btn2Text
            ? `<a href="${escapeHtml(b.btn2Link || '#')}" class="btn btn-outline">${escapeHtml(b.btn2Text)}</a>`
            : "";

          return `
            <div class="hero-slide ${isActive ? 'active' : ''}">
              ${mediaHtml}
              <div class="container hero-content">
                ${badgeHtml}
                <h1>${formatHeroHeading(b.heading)}</h1>
                ${b.subtitle ? `<p>${escapeHtml(b.subtitle)}</p>` : ''}
                ${(btn1Html || btn2Html) ? `<div class="hero-actions">${btn1Html} ${btn2Html}</div>` : ''}
              </div>
              <div class="hero-shape shape-one"></div>
              <div class="hero-shape shape-two"></div>
            </div>
          `;
        }

        function formatHeroHeading(heading) {
          if (!heading) return "";
          const safe = escapeHtml(heading);
          // If already contains span, return as is
          if (safe.includes("&lt;span&gt;")) {
            return safe.replace(/&lt;(\/?)span&gt;/g, "<$1span>");
          }
          const words = safe.split(" ");
          if (words.length > 2) {
            const firstPart = words.slice(0, words.length - 2).join(" ");
            const lastPart = words.slice(words.length - 2).join(" ");
            return `${firstPart} <span>${lastPart}</span>`;
          } else if (words.length === 2) {
            return `${words[0]} <span>${words[1]}</span>`;
          }
          return `<span>${safe}</span>`;
        }

        function escapeHtml(str) {
          if (!str) return "";
          return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
        }

        if (banners.length === 1) {
          // Single banner
          heroSection.innerHTML = buildSlideHtml(banners[0], true);
        } else {
          // Multi-banner slider
          let slidesHtml = '<div class="hero-slider-container">';
          banners.forEach((b, idx) => {
            slidesHtml += buildSlideHtml(b, idx === 0);
          });

          // Navigation arrows
          slidesHtml += `
            <button class="hero-nav-arrow prev" id="heroPrevBtn" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="hero-nav-arrow next" id="heroNextBtn" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>
          </div>`;
          heroSection.innerHTML = slidesHtml;

          // Slider logic
          let currentSlide = 0;
          const slides = heroSection.querySelectorAll(".hero-slide");
          let slideInterval = null;

          function goToSlide(n) {
            slides[currentSlide].classList.remove("active");
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add("active");
          }

          function nextSlide() {
            goToSlide(currentSlide + 1);
          }

          function prevSlide() {
            goToSlide(currentSlide - 1);
          }

          function startAutoSlide() {
            if (slideInterval) clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 6500);
          }

          function stopAutoSlide() {
            if (slideInterval) clearInterval(slideInterval);
          }

          const nextBtn = document.getElementById("heroNextBtn");
          const prevBtn = document.getElementById("heroPrevBtn");

          if (nextBtn) {
            nextBtn.addEventListener("click", () => {
              nextSlide();
              startAutoSlide();
            });
          }

          if (prevBtn) {
            prevBtn.addEventListener("click", () => {
              prevSlide();
              startAutoSlide();
            });
          }

          heroSection.addEventListener("mouseenter", stopAutoSlide);
          heroSection.addEventListener("mouseleave", startAutoSlide);

          startAutoSlide();
        }
      })
      .catch((err) => {
        console.log("Dynamic banner loader quiet error:", err);
      });
  }

  initHeroBanners();

  // =========================================================
  // DYNAMIC GALLERY SEASONS NAVBAR DROPDOWN
  // Automatically adds Season dropdown to navbar if Season 2+ exists
  // =========================================================
  function initDynamicGalleryNav() {
    fetch('api/get-gallery.php')
      .then(r => r.json())
      .then(data => {
        if (data.success && data.seasons && data.seasons.length > 1) {
          const navLinksUl = document.querySelector('.nav-links');
          if (!navLinksUl) return;

          // Find gallery nav item
          const galleryAnchor = Array.from(navLinksUl.querySelectorAll('a')).find(a => {
            const href = a.getAttribute('href') || '';
            return href === 'gallery.php' || href.startsWith('gallery.php?');
          });

          if (galleryAnchor && !galleryAnchor.closest('.dropdown')) {
            const li = galleryAnchor.parentElement;
            li.className = 'dropdown gallery-nav-dropdown';
            const isActive = galleryAnchor.classList.contains('active') ? ' active' : '';

            let subMenuHtml = `
              <a href="gallery.php" class="dropdown-toggle${isActive}">
                Gallery <i class="fa-solid fa-chevron-down nav-arrow"></i>
              </a>
              <ul class="dropdown-menu">
                <li><a href="gallery.php"><i class="fa-solid fa-images"></i> All Seasons</a></li>
            `;

            data.seasons.forEach(s => {
              subMenuHtml += `<li><a href="gallery.php?season=${encodeURIComponent(s)}"><i class="fa-solid fa-trophy"></i> ${s}</a></li>`;
            });

            subMenuHtml += `</ul>`;
            li.innerHTML = subMenuHtml;

            // Re-bind accordion on mobile for the newly created toggle
            const newToggle = li.querySelector('.dropdown-toggle');
            if (newToggle) {
              newToggle.addEventListener('click', (e) => {
                if (window.innerWidth <= 1024) {
                  e.preventDefault();
                  e.stopPropagation();
                  li.classList.toggle('open');
                  const arrow = newToggle.querySelector('.nav-arrow');
                  if (arrow) {
                    arrow.style.transform = li.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
                  }
                }
              });
            }
          }
        }
      })
      .catch(err => console.log('Gallery nav check silent error:', err));
  }

  // =========================================================
  // GLOBAL CONTACT SETTINGS SYNC (Top-bar & Footer)
  // =========================================================
  function initGlobalContactSync() {
    fetch('api/get-contact-settings.php')
      .then(r => r.json())
      .then(data => {
        if (data.success && data.settings) {
          const s = data.settings;
          
          // Top bar phone
          if (s.phone) {
            const topPhone = document.querySelector('.top-bar-left a[href^="tel:"]');
            if (topPhone) {
              topPhone.href = 'tel:' + s.phone.replace(/[^0-9+]/g, '');
              topPhone.innerHTML = `<i class="fa-solid fa-phone"></i> ${s.phone}`;
            }
          }

          // Top bar email
          if (s.email) {
            const topEmail = document.querySelector('.top-bar-left a[href^="mailto:"]');
            if (topEmail) {
              topEmail.href = 'mailto:' + s.email;
              topEmail.innerHTML = `<i class="fa-solid fa-envelope"></i> ${s.email}`;
            }
          }

          // Top bar socials
          if (s.facebook) {
            const topFb = document.querySelector('.top-bar-right a[aria-label="Facebook"], .top-bar-right a[aria-label="facebook"]');
            if (topFb) topFb.href = s.facebook;
          }
          if (s.instagram) {
            const topIg = document.querySelector('.top-bar-right a[aria-label="Instagram"], .top-bar-right a[aria-label="instagram"]');
            if (topIg) topIg.href = s.instagram;
          }
          if (s.youtube) {
            const topYt = document.querySelector('.top-bar-right a[aria-label="Youtube"], .top-bar-right a[aria-label="YouTube"], .top-bar-right a[aria-label="youtube"]');
            if (topYt) topYt.href = s.youtube;
          }

          // Footer socials
          if (s.facebook) {
            const footFb = document.querySelector('.footer-social-icons a.facebook, .footer-social-icons a[aria-label="Facebook"]');
            if (footFb) footFb.href = s.facebook;
          }
          if (s.instagram) {
            const footIg = document.querySelector('.footer-social-icons a.instagram, .footer-social-icons a[aria-label="Instagram"]');
            if (footIg) footIg.href = s.instagram;
          }
          if (s.youtube) {
            const footYt = document.querySelector('.footer-social-icons a.youtube, .footer-social-icons a[aria-label="YouTube"]');
            if (footYt) footYt.href = s.youtube;
          }
        }
      })
      .catch(err => console.log('Global contact sync silent error:', err));
  }

  // =========================================================
  // HERO STATS NUMBER COUNTER ANIMATION (Only Numbers Animate)
  // =========================================================
  function initHomeStatsCounter() {
    const statCards = document.querySelectorAll('.stats-section .stat-number');
    if (!statCards || statCards.length === 0) return;

    const animateCounter = (el) => {
      const numSpan = el.querySelector('.counter-num');
      if (!numSpan) return;

      const targetVal = parseFloat(el.getAttribute('data-target')) || 0;
      const decimals = parseInt(el.getAttribute('data-decimals')) || 0;
      const duration = 1800; // 1.8 seconds smooth animation
      const startTime = performance.now();

      const easeOutQuart = (t) => 1 - Math.pow(1 - t, 4);

      const updateCount = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const ease = easeOutQuart(progress);
        const currentVal = ease * targetVal;

        if (decimals > 0) {
          numSpan.textContent = currentVal.toFixed(decimals);
        } else {
          numSpan.textContent = Math.floor(currentVal).toLocaleString('en-US');
        }

        if (progress < 1) {
          requestAnimationFrame(updateCount);
        } else {
          if (decimals > 0) {
            numSpan.textContent = targetVal.toFixed(decimals);
          } else {
            numSpan.textContent = Math.round(targetVal).toLocaleString('en-US');
          }
        }
      };

      requestAnimationFrame(updateCount);
    };

    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15 });

      statCards.forEach(card => observer.observe(card));
    } else {
      statCards.forEach(card => animateCounter(card));
    }
  }

  initDynamicGalleryNav();
  initGlobalContactSync();
  initHomeStatsCounter();
});


