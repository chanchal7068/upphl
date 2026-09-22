<?php
// terms-and-conditions.php
$pageTitle = 'Terms &amp; Conditions - UP Pro Handball League (UPPHL)';
$extraCss = array (
  0 => 'assets/css/terms-and-conditions.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('terms-and-conditions');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Terms & Conditions';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Welcome to the official website of UP Pro Handball League (UPPHL). These terms govern your access to and use of the UPPHL portal, registrations, media, and digital services.';
$heroBadge = !empty($pb['badgeText']) ? $pb['badgeText'] : 'League Governance';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- Hero Banner -->
      <section class="legal-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <div class="legal-hero-content">
            <div class="legal-badge">
              <i class="fa-solid fa-file-contract"></i> <?= htmlspecialchars($heroBadge) ?>
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
                <span>Jurisdiction: Courts of Uttar Pradesh, India</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Legal Navigation Sub-bar -->
      <div class="legal-nav-bar">
        <div class="container legal-nav-wrapper">
          <div class="legal-tabs">
            <a href="privacy-policy.php" class="legal-tab-link">
              <i class="fa-solid fa-shield-halved"></i> Privacy Policy
            </a>
            <a href="terms-and-conditions.php" class="legal-tab-link active">
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
              <li><a href="#sec-1" class="toc-link">1. About UPPHL</a></li>
              <li><a href="#sec-2" class="toc-link">2. Eligibility to Use</a></li>
              <li><a href="#sec-3" class="toc-link">3. User Registration</a></li>
              <li><a href="#sec-4" class="toc-link">4. Player Selection Rules</a></li>
              <li><a href="#sec-5" class="toc-link">5. Accuracy of Information</a></li>
              <li><a href="#sec-6" class="toc-link">6. Website Content</a></li>
              <li><a href="#sec-7" class="toc-link">7. Match Schedule &amp; Changes</a></li>
              <li><a href="#sec-8" class="toc-link">8. Intellectual Property</a></li>
              <li><a href="#sec-9" class="toc-link">9. Use of Name &amp; Logo</a></li>
              <li><a href="#sec-10" class="toc-link">10. User-Submitted Content</a></li>
              <li><a href="#sec-11" class="toc-link">11. Photography &amp; Media</a></li>
              <li><a href="#sec-12" class="toc-link">12. Third-Party Services</a></li>
              <li><a href="#sec-13" class="toc-link">13. Payments</a></li>
              <li><a href="#sec-14" class="toc-link">14. Refunds &amp; Cancellations</a></li>
              <li><a href="#sec-15" class="toc-link">15. Prohibited Activities</a></li>
              <li><a href="#sec-16" class="toc-link">16. Website Availability</a></li>
              <li><a href="#sec-17" class="toc-link">17. Disclaimer</a></li>
              <li><a href="#sec-18" class="toc-link">18. Limitation of Liability</a></li>
              <li><a href="#sec-19" class="toc-link">19. Indemnification</a></li>
              <li><a href="#sec-20" class="toc-link">20. Privacy</a></li>
              <li><a href="#sec-21" class="toc-link">21. Changes to Terms</a></li>
              <li><a href="#sec-22" class="toc-link">22. Termination</a></li>
              <li><a href="#sec-23" class="toc-link">23. Governing Law</a></li>
              <li><a href="#sec-24" class="toc-link">24. Contact Us</a></li>
            </ul>

            <div class="legal-quick-card">
              <h4>Tournament Inquiries</h4>
              <p>For questions regarding player contracts, code of conduct, or governance rules.</p>
              <a href="contact.php">
                Contact Committee <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </aside>

          <!-- Main Policy Content -->
          <div class="policy-content">
            
            <!-- Summary Callout -->
            <div class="policy-callout">
              <div class="policy-callout-icon">
                <i class="fa-solid fa-gavel"></i>
              </div>
              <div class="policy-callout-text">
                <h3>Welcome to UP Pro Handball League (UPPHL)</h3>
                <p>
                  These Terms &amp; Conditions ("Terms") govern your access to and use of the UPPHL website, registration services, player information, event information, tickets, media content, digital services, and other services made available through the website.
                </p>
                <p style="margin-top: 8px; font-weight: 600; color: var(--text-dark);">
                  By accessing or using the website, you agree to comply with these Terms. If you do not agree with any part of these Terms, please do not use the website or its services.
                </p>
              </div>
            </div>

            <!-- Section 1 -->
            <div class="policy-section" id="sec-1">
              <div class="policy-section-header">
                <div class="section-number">1</div>
                <h2>About UPPHL</h2>
              </div>
              <p>
                UP Pro Handball League (UPPHL) is a professional handball league associated with the promotion and development of handball in Uttar Pradesh.
              </p>
              <p>
                The league is organized and administered through its authorized organizing entities, partners, associations, teams, and service providers, as applicable.
              </p>
              <p>
                The league's objective includes providing a competitive platform for handball players, promoting the sport, supporting sporting talent, and contributing to the development of a stronger handball ecosystem in Uttar Pradesh.
              </p>
            </div>

            <!-- Section 2 -->
            <div class="policy-section" id="sec-2">
              <div class="policy-section-header">
                <div class="section-number">2</div>
                <h2>Eligibility to Use the Website</h2>
              </div>
              <p>
                You must provide accurate and complete information when using registration forms, applications, ticketing services, or other services available through the website.
              </p>
              <p>
                If you are under the applicable age of majority, you may be required to obtain consent from your parent or legal guardian before using certain services or participating in certain activities.
              </p>
              <p>
                UPPHL may refuse, suspend, or terminate access where information is found to be false, misleading, fraudulent, incomplete, or submitted in violation of applicable rules.
              </p>
            </div>

            <!-- Section 3 -->
            <div class="policy-section" id="sec-3">
              <div class="policy-section-header">
                <div class="section-number">3</div>
                <h2>User Registration</h2>
              </div>
              <p>Certain features may require registration or submission of personal information. You agree that:</p>
              <ul class="policy-list">
                <li>The information submitted by you is accurate and current.</li>
                <li>You will not impersonate another person.</li>
                <li>You will not submit false or fraudulent documents.</li>
                <li>You will not use another person's account or registration details without authorization.</li>
                <li>You will promptly notify UPPHL if you discover unauthorized use of your information.</li>
                <li>UPPHL reserves the right to verify submitted information.</li>
              </ul>
            </div>

            <!-- Section 4 -->
            <div class="policy-section" id="sec-4">
              <div class="policy-section-header">
                <div class="section-number">4</div>
                <h2>Player Registration and Selection</h2>
              </div>
              <p>Registration does not guarantee:</p>
              <ul class="policy-list">
                <li>Selection in a team</li>
                <li>Participation in a match</li>
                <li>Auction selection</li>
                <li>Team allocation</li>
                <li>Playing time</li>
                <li>Contract</li>
                <li>Prize money</li>
                <li>Employment</li>
                <li>Any other sporting or financial benefit</li>
              </ul>
              <p style="margin-top: 14px;">
                Player selection, team composition, auction procedures, eligibility, trials, and participation will be governed by the applicable league rules and official announcements. UPPHL may modify or update selection procedures where reasonably necessary for the organization of the league.
              </p>
            </div>

            <!-- Section 5 -->
            <div class="policy-section" id="sec-5">
              <div class="policy-section-header">
                <div class="section-number">5</div>
                <h2>Accuracy of Information</h2>
              </div>
              <p>
                Users are responsible for ensuring that information submitted to UPPHL is accurate and complete.
              </p>
              <p>
                If incorrect or misleading information is discovered, UPPHL may reject, suspend, cancel, or otherwise take appropriate action regarding the relevant registration or application.
              </p>
            </div>

            <!-- Section 6 -->
            <div class="policy-section" id="sec-6">
              <div class="policy-section-header">
                <div class="section-number">6</div>
                <h2>Website Content</h2>
              </div>
              <p>The website may contain:</p>
              <ul class="policy-list">
                <li>League news</li>
                <li>Match schedules</li>
                <li>Results</li>
                <li>Team information</li>
                <li>Player profiles</li>
                <li>Player statistics</li>
                <li>Photographs</li>
                <li>Videos</li>
                <li>Interviews</li>
                <li>Articles</li>
                <li>Announcements</li>
                <li>Event information</li>
                <li>Sponsor information</li>
                <li>Promotional material</li>
              </ul>
              <p style="margin-top: 14px;">
                We make reasonable efforts to keep information accurate and current. However, schedules, venues, teams, players, timings, broadcasters, sponsors, and other information may change without prior notice.
              </p>
            </div>

            <!-- Section 7 -->
            <div class="policy-section" id="sec-7">
              <div class="policy-section-header">
                <div class="section-number">7</div>
                <h2>Match Schedule and Event Changes</h2>
              </div>
              <p>UPPHL reserves the right to modify:</p>
              <ul class="policy-list">
                <li>Match dates</li>
                <li>Match timings</li>
                <li>Venues</li>
                <li>Team participation</li>
                <li>Match format</li>
                <li>Competition format</li>
                <li>Broadcast arrangements</li>
                <li>Event schedules</li>
              </ul>
              <p style="margin-top: 14px;">
                Changes may occur due to operational, sporting, venue, weather, safety, regulatory, or other circumstances. Users should rely on official UPPHL communications for the latest information.
              </p>
            </div>

            <!-- Section 8 -->
            <div class="policy-section" id="sec-8">
              <div class="policy-section-header">
                <div class="section-number">8</div>
                <h2>Intellectual Property</h2>
              </div>
              <p>Unless otherwise stated, the content available on the website, including:</p>
              <ul class="policy-list">
                <li>UPPHL name</li>
                <li>Logos</li>
                <li>Trademarks</li>
                <li>Graphics</li>
                <li>Designs</li>
                <li>Photographs</li>
                <li>Videos</li>
                <li>Written content</li>
                <li>Statistics</li>
                <li>Website layout</li>
                <li>Icons</li>
                <li>Promotional materials</li>
              </ul>
              <p style="margin-top: 14px;">
                may be owned by or licensed to UPPHL or its respective partners and rights holders. You may not reproduce, copy, modify, distribute, publish, sell, commercially exploit, or create derivative works from protected content without prior written permission from the relevant rights holder.
              </p>
            </div>

            <!-- Section 9 -->
            <div class="policy-section" id="sec-9">
              <div class="policy-section-header">
                <div class="section-number">9</div>
                <h2>Use of League Name and Logo</h2>
              </div>
              <p>
                The UPPHL name, logo, team names, marks, branding, and other intellectual property may not be used for unauthorized commercial purposes.
              </p>
              <p>
                You may not represent yourself, your organization, product, or service as an official partner, sponsor, representative, team, agent, or affiliate of UPPHL without written authorization.
              </p>
            </div>

            <!-- Section 10 -->
            <div class="policy-section" id="sec-10">
              <div class="policy-section-header">
                <div class="section-number">10</div>
                <h2>User-Submitted Content</h2>
              </div>
              <p>Where the website allows users to submit photographs, videos, comments, applications, information, or other content, you agree that:</p>
              <ul class="policy-list">
                <li>You have the right and authority to submit the content.</li>
                <li>The content does not violate applicable law.</li>
                <li>The content does not infringe third-party intellectual property or privacy rights.</li>
                <li>The content does not contain malicious software.</li>
                <li>The content is not fraudulent, abusive, defamatory, obscene, or unlawful.</li>
              </ul>
              <p style="margin-top: 14px;">
                UPPHL may remove or reject content that violates these Terms or applicable law.
              </p>
            </div>

            <!-- Section 11 -->
            <div class="policy-section" id="sec-11">
              <div class="policy-section-header">
                <div class="section-number">11</div>
                <h2>Photography, Video and Media</h2>
              </div>
              <p>
                UPPHL events may be photographed, filmed, recorded, broadcast, streamed, or otherwise documented.
              </p>
              <p>
                By attending or participating in an UPPHL event, you acknowledge that your image, voice, name, performance, or appearance may appear in event-related photographs, videos, broadcasts, social media, promotional materials, news coverage, or archival content, subject to applicable law and event-specific notices.
              </p>
            </div>

            <!-- Section 12 -->
            <div class="policy-section" id="sec-12">
              <div class="policy-section-header">
                <div class="section-number">12</div>
                <h2>Third-Party Websites and Services</h2>
              </div>
              <p>
                The website may contain links to third-party websites, applications, payment providers, social media platforms, sponsors, broadcasters, or other external services.
              </p>
              <p>
                UPPHL does not control and is not responsible for the content, availability, security, privacy practices, or terms of third-party services. Your use of third-party services is subject to their respective terms and policies.
              </p>
            </div>

            <!-- Section 13 -->
            <div class="policy-section" id="sec-13">
              <div class="policy-section-header">
                <div class="section-number">13</div>
                <h2>Payments</h2>
              </div>
              <p>
                Where paid services are offered, the applicable price and payment conditions will be displayed before payment. Users are responsible for providing correct payment and billing information.
              </p>
              <p>
                Payments may be processed through third-party payment gateways. UPPHL does not request users to share passwords, UPI PINs, OTPs, CVV numbers, or other confidential payment authentication information with UPPHL personnel.
              </p>
            </div>

            <!-- Section 14 -->
            <div class="policy-section" id="sec-14">
              <div class="policy-section-header">
                <div class="section-number">14</div>
                <h2>Refunds and Cancellations</h2>
              </div>
              <p>
                Refunds and cancellations are governed by the applicable <a href="refund-policy.php" style="color: var(--orange); font-weight: 600;">Refund &amp; Cancellation Policy</a> and any event-specific terms displayed at the time of purchase or registration. Users should review those terms before making a payment.
              </p>
            </div>

            <!-- Section 15 -->
            <div class="policy-section" id="sec-15">
              <div class="policy-section-header">
                <div class="section-number">15</div>
                <h2>Prohibited Activities</h2>
              </div>
              <p>You agree not to:</p>
              <ul class="policy-list">
                <li>Use the website for unlawful purposes</li>
                <li>Attempt unauthorized access to the website or its systems</li>
                <li>Interfere with website security</li>
                <li>Upload malicious code or files</li>
                <li>Scrape or systematically collect website data without authorization</li>
                <li>Copy protected website content for commercial purposes</li>
                <li>Impersonate UPPHL, its officials, players, teams, partners, or representatives</li>
                <li>Submit false information</li>
                <li>Conduct fraudulent transactions</li>
                <li>Attempt to disrupt website operations</li>
                <li>Use the website to distribute spam or malicious content</li>
                <li>Violate the rights of other users or third parties</li>
              </ul>
              <p style="margin-top: 14px;">
                UPPHL may take appropriate action against users who violate these Terms.
              </p>
            </div>

            <!-- Section 16 -->
            <div class="policy-section" id="sec-16">
              <div class="policy-section-header">
                <div class="section-number">16</div>
                <h2>Website Availability</h2>
              </div>
              <p>
                We aim to keep the website available and operational, but we do not guarantee uninterrupted or error-free access. The website may occasionally be unavailable due to:
              </p>
              <ul class="policy-list">
                <li>Maintenance</li>
                <li>Technical issues</li>
                <li>Server problems</li>
                <li>Security incidents</li>
                <li>Network failures</li>
                <li>Updates</li>
                <li>Third-party service failures</li>
                <li>Force majeure events</li>
              </ul>
            </div>

            <!-- Section 17 -->
            <div class="policy-section" id="sec-17">
              <div class="policy-section-header">
                <div class="section-number">17</div>
                <h2>Disclaimer</h2>
              </div>
              <p>
                The website and its content are provided for general informational and league-related purposes.
              </p>
              <p>
                While reasonable efforts are made to maintain accurate information, UPPHL does not guarantee that all information will always be complete, current, accurate, or free from errors.
              </p>
              <p>
                Sporting information such as schedules, player statistics, team information, results, venues, and announcements may change.
              </p>
            </div>

            <!-- Section 18 -->
            <div class="policy-section" id="sec-18">
              <div class="policy-section-header">
                <div class="section-number">18</div>
                <h2>Limitation of Liability</h2>
              </div>
              <p>
                To the maximum extent permitted by applicable law, UPPHL and its authorized representatives, organizers, partners, service providers, and affiliates shall not be responsible for losses arising from:
              </p>
              <ul class="policy-list">
                <li>Temporary website unavailability</li>
                <li>Technical failures</li>
                <li>Third-party service interruptions</li>
                <li>Unauthorized access caused by circumstances beyond reasonable control</li>
                <li>Reliance on outdated website information</li>
                <li>Changes to schedules or events</li>
                <li>User-provided inaccurate information</li>
                <li>Third-party websites or services</li>
              </ul>
              <p style="margin-top: 14px;">
                Nothing in these Terms is intended to exclude or limit liability that cannot legally be excluded or limited under applicable law.
              </p>
            </div>

            <!-- Section 19 -->
            <div class="policy-section" id="sec-19">
              <div class="policy-section-header">
                <div class="section-number">19</div>
                <h2>Indemnification</h2>
              </div>
              <p>
                To the extent permitted by applicable law, you agree to indemnify and hold harmless UPPHL, its organizers, representatives, partners, service providers, and affiliates from claims, losses, liabilities, damages, costs, or expenses arising from:
              </p>
              <ul class="policy-list">
                <li>Your violation of these Terms</li>
                <li>Your unlawful use of the website</li>
                <li>Your submission of false or unauthorized information</li>
                <li>Your infringement of third-party rights</li>
                <li>Your misuse of the website or its services</li>
              </ul>
            </div>

            <!-- Section 20 -->
            <div class="policy-section" id="sec-20">
              <div class="policy-section-header">
                <div class="section-number">20</div>
                <h2>Privacy</h2>
              </div>
              <p>
                Your use of the website is also governed by our <a href="privacy-policy.php" style="color: var(--orange); font-weight: 600;">Privacy Policy</a>, which explains how personal information may be collected, used, stored, and processed. By using the website, you acknowledge the applicable Privacy Policy.
              </p>
            </div>

            <!-- Section 21 -->
            <div class="policy-section" id="sec-21">
              <div class="policy-section-header">
                <div class="section-number">21</div>
                <h2>Changes to These Terms</h2>
              </div>
              <p>
                UPPHL may modify these Terms from time to time. Updated Terms will be published on this page with the revised "Last Updated" date.
              </p>
              <p>
                Your continued use of the website after changes are published may constitute acceptance of the updated Terms, subject to applicable law.
              </p>
            </div>

            <!-- Section 22 -->
            <div class="policy-section" id="sec-22">
              <div class="policy-section-header">
                <div class="section-number">22</div>
                <h2>Termination or Suspension</h2>
              </div>
              <p>
                UPPHL may suspend or terminate access to certain website features or services where necessary, including where a user:
              </p>
              <ul class="policy-list">
                <li>Violates these Terms</li>
                <li>Provides false information</li>
                <li>Engages in fraudulent activity</li>
                <li>Attempts unauthorized access</li>
                <li>Misuses UPPHL services</li>
                <li>Violates applicable law</li>
              </ul>
              <p style="margin-top: 14px;">
                Termination or suspension does not affect rights or obligations that arose before termination.
              </p>
            </div>

            <!-- Section 23 -->
            <div class="policy-section" id="sec-23">
              <div class="policy-section-header">
                <div class="section-number">23</div>
                <h2>Governing Law and Jurisdiction</h2>
              </div>
              <p>
                These Terms shall be governed by and interpreted in accordance with the applicable laws of India.
              </p>
              <p>
                Any dispute arising in connection with these Terms or the use of the website shall be subject to the jurisdiction of the competent courts having jurisdiction over the applicable UPPHL organizing entity, subject to applicable law.
              </p>
            </div>

            <!-- Section 24 -->
            <div class="policy-section" id="sec-24">
              <div class="policy-section-header">
                <div class="section-number">24</div>
                <h2>Contact Us</h2>
              </div>
              <p>For questions regarding these Terms &amp; Conditions, please contact:</p>

              <div class="policy-officer-box">
                <div class="policy-officer-info">
                  <h3>UP Pro Handball League (UPPHL)</h3>
                  <p>Official Legal &amp; Governance Desk</p>
                  <div class="officer-details">
                    <div><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <a href="mailto:uphandballleague@gmail.com">uphandballleague@gmail.com</a></div>
                    <div><i class="fa-solid fa-phone"></i> <strong>Phone:</strong> <a href="tel:7084900009">+91 7084900009</a></div>
                    <div><i class="fa-solid fa-location-dot"></i> <strong>Address:</strong> D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006</div>
                    <div><i class="fa-solid fa-globe"></i> <strong>Website:</strong> <a href="https://upphl.com" target="_blank">https://upphl.com</a></div>
                  </div>
                </div>
                <div class="policy-officer-action">
                  <a href="contact.php" class="btn-contact">
                    <i class="fa-solid fa-headset"></i> Contact Us
                  </a>
                </div>
              </div>
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
