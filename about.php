<?php
// about.php
$pageTitle = 'About Us - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/about.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('about');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'About the League';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Discover the history, objectives, and vision behind the UP Pro Handball League.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- About Hero -->
      <section class="about-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <h1><?= htmlspecialchars($heroTitle) ?></h1>
          <p><?= htmlspecialchars($heroSubtitle) ?></p>
        </div>
      </section>

      <!-- About Intro -->
      <section class="about-section">
        <div class="container">
          <div class="about-grid">
            <div class="about-text">
              <h2>Promoting Handball Excellence in UP</h2>
              <p>The UP Pro Handball League was founded with the vision to identify, nurture, and showcase the best handball talent across Uttar Pradesh. We bring together players, coaches, franchises, and fans under one premium championship platform.</p>
              <p>By establishing professional standards, training setups, and an annual high-energy tournament, we strive to build a vibrant sports community and put handball on the map of top professional sports in India.</p>
            </div>
            <div class="about-image">
              <img src="assets/images/aboutus-banner.jpeg" alt="Handball Play" />
            </div>
          </div>
        </div>
      </section>

      <!-- President Message -->
      <section class="president-section">
        <div class="container">
          <div class="president-card">
            <div class="president-img">
              <img src="assets/images/league-details/amit-pandey.jpeg" alt="President UPPHL" />
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
      </section>

      <!-- Organization & Affiliation Section -->
      <section class="org-collaboration-section">
        <div class="container">
          <div class="section-header">
            <span class="org-badge"><i class="fa-solid fa-medal"></i> Governance &amp; Sanction</span>
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

      <!-- League Policies & Terms -->
      <!-- <section class="about-section" id="policies-terms">
        <div class="container">
          <h2 style="text-align: center; font-size: 2rem; color: var(--text-dark);">League Policies & Terms</h2>
          <div class="values-grid">
            
            <div class="value-card">
              <i class="fa-solid fa-user-shield"></i>
              <h3>Privacy Policy</h3>
              <p>We prioritize the privacy and security of all our players, coaches, and visitors. Personal data collected during registration is strictly protected, kept confidential, and used solely for official league operations, player verification, and communications.</p>
            </div>

            <div class="value-card">
              <i class="fa-solid fa-rotate-left"></i>
              <h3>Refund Policy</h3>
              <p>Registration fees and official event payments are non-refundable once registration is confirmed. In cases of duplicate transactions or official event cancellations, verified refund requests will be processed to the original payment source within 7–10 working days.</p>
            </div>

            <div class="value-card">
              <i class="fa-solid fa-file-contract"></i>
              <h3>Terms & Conditions</h3>
              <p>All players, team staff, and participating franchises must strictly comply with UPPHL tournament rules and code of conduct. The league committee reserves the right to manage fixtures, verify eligibility, and take disciplinary action against rule violations.</p>
            </div>

          </div>
        </div>
      </section> -->
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
