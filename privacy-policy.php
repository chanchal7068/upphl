<?php
// privacy-policy.php
$pageTitle = 'Privacy Policy - UP Pro Handball League (UPPHL)';
$extraCss = array (
  0 => 'assets/css/privacy-policy.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('privacy-policy');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Privacy Policy';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Welcome to the official website of UP Pro Handball League (UPPHL). We respect your privacy and are committed to protecting your personal information.';
$heroBadge = !empty($pb['badgeText']) ? $pb['badgeText'] : 'Official Policy';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- Hero Banner -->
      <section class="legal-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <div class="legal-hero-content">
            <div class="legal-badge">
              <i class="fa-solid fa-shield-halved"></i> <?= htmlspecialchars($heroBadge) ?>
            </div>
            <h1><?= htmlspecialchars($heroTitle) ?></h1>
            <p>
              <?= htmlspecialchars($heroSubtitle) ?>
            </p>
            <div class="legal-meta">
              <div class="legal-meta-item">
                <i class="fa-regular fa-calendar-check"></i>
                <span>Last Updated: 26 February 2026</span>
              </div>
              <div class="legal-meta-item">
                <i class="fa-solid fa-scale-balanced"></i>
                <span>Governing Law: Republic of India</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Legal Navigation Sub-bar -->
      <div class="legal-nav-bar">
        <div class="container legal-nav-wrapper">
          <div class="legal-tabs">
            <a href="privacy-policy.php" class="legal-tab-link active">
              <i class="fa-solid fa-shield-halved"></i> Privacy Policy
            </a>
            <a href="terms-and-conditions.php" class="legal-tab-link">
              <i class="fa-solid fa-file-contract"></i> Terms &amp; Conditions
            </a>
            <a href="refund-policy.php" class="legal-tab-link">
              <i class="fa-solid fa-rotate-left"></i> Refund &amp; Cancellation Policy
            </a>
          </div>
          <div class="legal-actions">
            <button class="btn-print" onclick="window.print()">
              <i class="fa-solid fa-print"></i> Print Document
            </button>
          </div>
        </div>
      </div>

      <!-- Policy Content Section -->
      <section class="legal-container">
        <div class="container legal-layout">
          
          <!-- Sticky Sidebar / Table of Contents -->
          <aside class="legal-toc">
            <div class="legal-toc-title">
              <i class="fa-solid fa-list-ol"></i> Table of Contents
            </div>
            <ul class="toc-list">
              <li><a href="#sec-1" class="toc-link">1. Information We Collect</a></li>
              <li><a href="#sec-2" class="toc-link">2. How We Use Your Information</a></li>
              <li><a href="#sec-3" class="toc-link">3. Player and Participant Information</a></li>
              <li><a href="#sec-4" class="toc-link">4. Payment Information</a></li>
              <li><a href="#sec-5" class="toc-link">5. Cookies and Similar Technologies</a></li>
              <li><a href="#sec-6" class="toc-link">6. Analytics and Third-Party Services</a></li>
              <li><a href="#sec-7" class="toc-link">7. Sharing of Information</a></li>
              <li><a href="#sec-8" class="toc-link">8. Data Security</a></li>
              <li><a href="#sec-9" class="toc-link">9. Data Retention</a></li>
              <li><a href="#sec-10" class="toc-link">10. Children's Privacy</a></li>
              <li><a href="#sec-11" class="toc-link">11. External Links</a></li>
              <li><a href="#sec-12" class="toc-link">12. Your Rights and Choices</a></li>
              <li><a href="#sec-13" class="toc-link">13. Changes to This Privacy Policy</a></li>
              <li><a href="#sec-14" class="toc-link">14. Contact Us</a></li>
              <li><a href="#sec-15" class="toc-link">15. Governing Law</a></li>
            </ul>

            <div class="legal-quick-card">
              <h4>Need Clarification?</h4>
              <p>Have questions regarding your personal information or player profile?</p>
              <a href="contact.php">
                Contact Support <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </aside>

          <!-- Main Policy Content -->
          <div class="policy-content">
            
            <!-- Intro / Preamble Callout -->
            <div class="policy-callout">
              <div class="policy-callout-icon">
                <i class="fa-solid fa-shield-cat"></i>
              </div>
              <div class="policy-callout-text">
                <h3>Welcome to UP Pro Handball League (UPPHL)</h3>
                <p>
                  Welcome to the official website of <strong>UP Pro Handball League (UPPHL)</strong>. We respect your privacy and are committed to protecting the personal information that you provide while using our website, services, registration forms, ticketing facilities, player registration facilities, and other digital services.
                </p>
                <p style="margin-top: 10px;">
                  This Privacy Policy explains how we collect, use, store, protect, and disclose information when you visit or use our website (<a href="https://upphl.com" target="_blank" style="color: var(--orange); font-weight: 600;">https://upphl.com</a>) and any related services.
                </p>
                <p style="margin-top: 10px; font-weight: 600; color: var(--text-dark);">
                  By accessing or using our website, you acknowledge that you have read and understood this Privacy Policy.
                </p>
              </div>
            </div>

            <!-- Section 1 -->
            <div class="policy-section" id="sec-1">
              <div class="policy-section-header">
                <div class="section-number">1</div>
                <h2>Information We Collect</h2>
              </div>
              <p>Depending on how you interact with our website, we may collect the following categories of information:</p>

              <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--orange-dark); margin: 20px 0 10px;">1.1 Personal Information</h3>
              <p>We may collect information that you voluntarily provide to us, including:</p>
              <ul class="policy-list">
                <li>Full name</li>
                <li>Date of birth</li>
                <li>Gender, where required</li>
                <li>Mobile number</li>
                <li>Email address</li>
                <li>Residential or correspondence address</li>
                <li>City and state</li>
                <li>Emergency contact details</li>
                <li>Identification or verification details, where required for player registration or event participation</li>
                <li>Sports-related information, including playing position, achievements, statistics, team details, and previous experience</li>
                <li>Photographs, videos, and other media submitted for league-related purposes</li>
                <li>Payment and transaction-related information</li>
                <li>Any other information that you voluntarily submit through our forms or services</li>
              </ul>

              <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--orange-dark); margin: 24px 0 10px;">1.2 Automatically Collected Information</h3>
              <p>When you visit our website, certain technical information may be collected automatically, including:</p>
              <ul class="policy-list">
                <li>IP address</li>
                <li>Browser type and version</li>
                <li>Device type</li>
                <li>Operating system</li>
                <li>Pages visited</li>
                <li>Date and time of access</li>
                <li>Referring website or source</li>
                <li>Approximate location derived from technical information</li>
                <li>Website interaction and usage information</li>
              </ul>
              <p style="margin-top: 14px;">
                This information may be used for website security, analytics, performance monitoring, and improving our services.
              </p>
            </div>

            <!-- Section 2 -->
            <div class="policy-section" id="sec-2">
              <div class="policy-section-header">
                <div class="section-number">2</div>
                <h2>How We Use Your Information</h2>
              </div>
              <p>
                We may use the information collected for legitimate league, operational, administrative, and business purposes, including:
              </p>
              <ul class="policy-list">
                <li>Processing player, team, event, or other registrations</li>
                <li>Verifying submitted information</li>
                <li>Managing league participation</li>
                <li>Communicating with players, teams, officials, spectators, partners, and other stakeholders</li>
                <li>Processing payments and registrations</li>
                <li>Providing tickets, passes, merchandise, or other services where applicable</li>
                <li>Publishing legitimate league-related information</li>
                <li>Maintaining player profiles, team information, statistics, results, and records</li>
                <li>Sending important league announcements and updates</li>
                <li>Improving our website and digital services</li>
                <li>Monitoring website performance</li>
                <li>Preventing fraud, misuse, unauthorized access, and security incidents</li>
                <li>Complying with applicable laws and legal obligations</li>
                <li>Protecting the rights, property, safety, and interests of UPPHL and its stakeholders</li>
              </ul>
            </div>

            <!-- Section 3 -->
            <div class="policy-section" id="sec-3">
              <div class="policy-section-header">
                <div class="section-number">3</div>
                <h2>Player and Participant Information</h2>
              </div>
              <p>
                If you register as a player, official, coach, team member, volunteer, or other participant, information submitted by you may be used for league administration and sporting purposes.
              </p>
              <p>
                Where appropriate, certain information such as player name, team, playing position, statistics, achievements, photographs, videos, match results, and other sports-related information may be published on the official website, social media channels, broadcasts, promotional material, or other league-related communication channels.
              </p>
              <p>
                By participating in UPPHL activities, you acknowledge that sporting events may be photographed, recorded, broadcast, or otherwise documented for legitimate league, promotional, media, archival, and commercial purposes, subject to applicable law and applicable event terms.
              </p>
            </div>

            <!-- Section 4 -->
            <div class="policy-section" id="sec-4">
              <div class="policy-section-header">
                <div class="section-number">4</div>
                <h2>Payment Information</h2>
              </div>
              <p>
                Where online payments are offered, payments may be processed through third-party payment service providers.
              </p>
              <p>
                UPPHL may receive limited transaction information necessary to verify or reconcile a payment. We generally do not require or store complete card numbers, CVV numbers, UPI PINs, passwords, or other sensitive payment authentication information on our own servers.
              </p>
              <p>
                Payment processing may be subject to the privacy policies and terms of the respective payment service provider.
              </p>
            </div>

            <!-- Section 5 -->
            <div class="policy-section" id="sec-5">
              <div class="policy-section-header">
                <div class="section-number">5</div>
                <h2>Cookies and Similar Technologies</h2>
              </div>
              <p>Our website may use cookies and similar technologies to:</p>
              <ul class="policy-list">
                <li>Keep the website functioning properly</li>
                <li>Remember user preferences</li>
                <li>Understand website traffic and usage</li>
                <li>Improve website performance</li>
                <li>Measure the effectiveness of digital campaigns</li>
                <li>Enhance user experience</li>
                <li>Maintain website security</li>
              </ul>
              <p style="margin-top: 14px;">
                You may be able to control or disable cookies through your browser settings. However, disabling certain cookies may affect the functionality of some parts of the website.
              </p>
            </div>

            <!-- Section 6 -->
            <div class="policy-section" id="sec-6">
              <div class="policy-section-header">
                <div class="section-number">6</div>
                <h2>Analytics and Third-Party Services</h2>
              </div>
              <p>We may use third-party services for purposes such as:</p>
              <ul class="policy-list">
                <li>Website analytics</li>
                <li>Payment processing</li>
                <li>Hosting</li>
                <li>Security</li>
                <li>Communication</li>
                <li>Email delivery</li>
                <li>Social media integration</li>
                <li>Video hosting or streaming</li>
                <li>Advertising and promotional activities</li>
              </ul>
              <p style="margin-top: 14px;">
                These third-party providers may process information according to their own privacy policies and applicable terms.
              </p>
            </div>

            <!-- Section 7 -->
            <div class="policy-section" id="sec-7">
              <div class="policy-section-header">
                <div class="section-number">7</div>
                <h2>Sharing of Information</h2>
              </div>
              <p>
                We do not intend to sell your personal information for monetary consideration. Information may be shared where reasonably necessary with:
              </p>
              <ul class="policy-list">
                <li>League organizers and administrators</li>
                <li>Participating teams and authorized representatives</li>
                <li>Event venues and service providers</li>
                <li>Payment processors</li>
                <li>Technology and hosting providers</li>
                <li>Professional advisors</li>
                <li>Media and broadcasting partners</li>
                <li>Sponsors and commercial partners, where appropriate</li>
                <li>Government authorities, regulators, courts, or law-enforcement agencies when legally required</li>
                <li>Other authorized persons where necessary to provide league-related services</li>
              </ul>
              <p style="margin-top: 14px;">
                We take reasonable steps to ensure that information shared with service providers is handled appropriately.
              </p>
            </div>

            <!-- Section 8 -->
            <div class="policy-section" id="sec-8">
              <div class="policy-section-header">
                <div class="section-number">8</div>
                <h2>Data Security</h2>
              </div>
              <p>
                We take reasonable technical and organizational measures to protect personal information against unauthorized access, alteration, disclosure, misuse, loss, or destruction.
              </p>
              <p>
                However, no website, server, online transmission, or electronic storage system can be guaranteed to be completely secure. Therefore, while we make reasonable efforts to protect your information, we cannot guarantee absolute security.
              </p>
            </div>

            <!-- Section 9 -->
            <div class="policy-section" id="sec-9">
              <div class="policy-section-header">
                <div class="section-number">9</div>
                <h2>Data Retention</h2>
              </div>
              <p>We may retain personal information for as long as reasonably necessary to:</p>
              <ul class="policy-list">
                <li>Provide services</li>
                <li>Maintain league and sporting records</li>
                <li>Complete transactions</li>
                <li>Meet legal, regulatory, accounting, or reporting requirements</li>
                <li>Resolve disputes</li>
                <li>Prevent fraud or misuse</li>
                <li>Enforce our agreements and policies</li>
                <li>Maintain legitimate historical or archival records</li>
              </ul>
              <p style="margin-top: 14px;">
                When information is no longer required, we may delete, anonymize, or securely dispose of it, subject to applicable legal and operational requirements.
              </p>
            </div>

            <!-- Section 10 -->
            <div class="policy-section" id="sec-10">
              <div class="policy-section-header">
                <div class="section-number">10</div>
                <h2>Children's Privacy</h2>
              </div>
              <p>
                Our website and services may involve sports activities in which participants could be minors.
              </p>
              <p>
                Where registration or participation involves a person under the applicable age of majority, the required consent of a parent or legal guardian may be necessary.
              </p>
              <p>
                Parents or legal guardians should contact us if they believe that a minor has submitted personal information without appropriate consent.
              </p>
            </div>

            <!-- Section 11 -->
            <div class="policy-section" id="sec-11">
              <div class="policy-section-header">
                <div class="section-number">11</div>
                <h2>External Links</h2>
              </div>
              <p>
                Our website may contain links to third-party websites, social media platforms, payment services, streaming platforms, sponsors, partners, or other external services.
              </p>
              <p>
                UPPHL is not responsible for the privacy practices, security, content, or policies of third-party websites.
              </p>
              <p>
                We encourage users to review the privacy policies of third-party websites before submitting personal information.
              </p>
            </div>

            <!-- Section 12 -->
            <div class="policy-section" id="sec-12">
              <div class="policy-section-header">
                <div class="section-number">12</div>
                <h2>Your Rights and Choices</h2>
              </div>
              <p>Subject to applicable law, you may have rights regarding your personal information, including the ability to:</p>
              <ul class="policy-list">
                <li>Request information about personal data held by us</li>
                <li>Request correction of inaccurate information</li>
                <li>Request deletion of information where legally permissible</li>
                <li>Withdraw certain consents where applicable</li>
                <li>Raise concerns regarding the processing of your information</li>
              </ul>
              <p style="margin-top: 14px;">
                Requests may be submitted using the contact details provided below. We may need to verify your identity before processing certain requests.
              </p>
            </div>

            <!-- Section 13 -->
            <div class="policy-section" id="sec-13">
              <div class="policy-section-header">
                <div class="section-number">13</div>
                <h2>Changes to This Privacy Policy</h2>
              </div>
              <p>
                We may update this Privacy Policy from time to time to reflect changes in our services, technology, legal requirements, or business practices.
              </p>
              <p>
                The updated version will be published on this page with the revised "Last Updated" date.
              </p>
              <p>
                Your continued use of the website after an updated Privacy Policy is published may constitute acknowledgement of the updated policy, subject to applicable law.
              </p>
            </div>

            <!-- Section 14 -->
            <div class="policy-section" id="sec-14">
              <div class="policy-section-header">
                <div class="section-number">14</div>
                <h2>Contact Us</h2>
              </div>
              <p>
                If you have any questions, concerns, requests, or complaints regarding this Privacy Policy or the handling of your personal information, please contact us:
              </p>

              <div class="policy-officer-box">
                <div class="policy-officer-info">
                  <h3>UP Pro Handball League (UPPHL)</h3>
                  <p>Official Privacy &amp; Data Compliance Desk</p>
                  <div class="officer-details">
                    <div><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <a href="mailto:uphandballleague@gmail.com">uphandballleague@gmail.com</a></div>
                    <div><i class="fa-solid fa-phone"></i> <strong>Phone:</strong> <a href="tel:7084900009">+91 7084900009</a></div>
                    <div><i class="fa-solid fa-location-dot"></i> <strong>Address:</strong> D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006</div>
                    <div><i class="fa-solid fa-globe"></i> <strong>Website:</strong> <a href="https://upphl.com" target="_blank">https://upphl.com</a></div>
                  </div>
                  <p style="margin-top: 10px; font-size: 0.88rem; color: var(--text-light);">
                    <em>For privacy-related requests, please mention <strong>"Privacy Policy Request"</strong> in the subject line of your communication.</em>
                  </p>
                </div>
                <div class="policy-officer-action">
                  <a href="contact.php" class="btn-contact">
                    <i class="fa-solid fa-headset"></i> Contact Us
                  </a>
                </div>
              </div>
            </div>

            <!-- Section 15 -->
            <div class="policy-section" id="sec-15">
              <div class="policy-section-header">
                <div class="section-number">15</div>
                <h2>Governing Law</h2>
              </div>
              <p>
                This Privacy Policy shall be interpreted in accordance with the applicable laws of India, subject to the jurisdiction and legal requirements applicable to UPPHL and its operations.
              </p>
            </div>

          </div>

        </div>
      </section>
</main>

<script>
      // Smooth scroll and active link highlighting for TOC
      document.addEventListener('DOMContentLoaded', () => {
        const links = document.querySelectorAll('.toc-link');
        const sections = document.querySelectorAll('.policy-section');

        // Smooth scroll with header offset on click
        links.forEach(link => {
          link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
              const headerOffset = 150;
              const elementPosition = targetSection.getBoundingClientRect().top;
              const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

              window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
              });

              // Also ensure link gets active class immediately
              links.forEach(l => l.classList.remove('active'));
              link.classList.add('active');
            }
          });
        });

        // Highlight active link while scrolling content
        function updateActiveToc() {
          let current = '';
          const scrollPosition = window.pageYOffset + 180;

          sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
              current = section.getAttribute('id');
            }
          });

          if (!current && sections.length > 0 && window.pageYOffset < sections[0].offsetTop) {
            current = sections[0].getAttribute('id');
          }

          if (current) {
            links.forEach(link => {
              link.classList.remove('active');
              if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
              }
            });
          }
        }

        window.addEventListener('scroll', updateActiveToc);
        updateActiveToc();
      });
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
