<?php
// refund-policy.php
$pageTitle = 'Refund &amp; Cancellation Policy - UP Pro Handball League (UPPHL)';
$extraCss = array (
  0 => 'assets/css/refund-policy.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('refund-policy');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Refund & Cancellation Policy';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'This policy applies to payments made through the official website of UP Pro Handball League (UPPHL), including registrations, tickets, merchandise, and other paid services.';
$heroBadge = !empty($pb['badgeText']) ? $pb['badgeText'] : 'Payment Governance';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- Hero Banner -->
      <section class="legal-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <div class="legal-hero-content">
            <div class="legal-badge">
              <i class="fa-solid fa-rotate-left"></i> <?= htmlspecialchars($heroBadge) ?>
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
                <i class="fa-solid fa-building-columns"></i>
                <span>Account Payouts: Original Payment Source</span>
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
            <a href="terms-and-conditions.php" class="legal-tab-link">
              <i class="fa-solid fa-file-contract"></i> Terms &amp; Conditions
            </a>
            <a href="refund-policy.php" class="legal-tab-link active">
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
              <li><a href="#sec-1" class="toc-link">1. General Policy</a></li>
              <li><a href="#sec-2" class="toc-link">2. Player Registration Fees</a></li>
              <li><a href="#sec-3" class="toc-link">3. Event &amp; Ticket Cancellation</a></li>
              <li><a href="#sec-4" class="toc-link">4. Merchandise</a></li>
              <li><a href="#sec-5" class="toc-link">5. Duplicate Payments</a></li>
              <li><a href="#sec-6" class="toc-link">6. Failed Transactions</a></li>
              <li><a href="#sec-7" class="toc-link">7. Refund Processing</a></li>
              <li><a href="#sec-8" class="toc-link">8. Non-Refundable Payments</a></li>
              <li><a href="#sec-9" class="toc-link">9. How to Request a Refund</a></li>
              <li><a href="#sec-10" class="toc-link">10. Refund Review</a></li>
              <li><a href="#sec-11" class="toc-link">11. Chargebacks &amp; Disputes</a></li>
              <li><a href="#sec-12" class="toc-link">12. Changes to This Policy</a></li>
              <li><a href="#sec-13" class="toc-link">13. Contact Information</a></li>
            </ul>

            <div class="legal-quick-card">
              <h4>Payment Issue?</h4>
              <p>Facing double deduction or failed transaction issue during player registration?</p>
              <a href="contact.php">
                Accounts Helpdesk <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </aside>

          <!-- Main Policy Content -->
          <div class="policy-content">
            
            <!-- Summary Callout -->
            <div class="policy-callout">
              <div class="policy-callout-icon">
                <i class="fa-solid fa-receipt"></i>
              </div>
              <div class="policy-callout-text">
                <h3>Official UPPHL Refund Guidelines</h3>
                <p>
                  This Refund &amp; Cancellation Policy applies to payments made through the official website of <strong>UP Pro Handball League (UPPHL)</strong>, including registrations, tickets, event-related services, merchandise, applications, and other paid services where applicable.
                </p>
                <p style="margin-top: 8px; font-weight: 600; color: var(--text-dark);">
                  Before making any payment, users are advised to carefully review the applicable registration, ticket, service, or event-specific terms.
                </p>
              </div>
            </div>

            <!-- Section 1 -->
            <div class="policy-section" id="sec-1">
              <div class="policy-section-header">
                <div class="section-number">1</div>
                <h2>General Policy</h2>
              </div>
              <p>
                All payments made through the official UPPHL website are subject to the terms applicable to the particular product, service, registration, ticket, or event.
              </p>
              <p>
                Refund eligibility may vary depending on the nature of the payment.
              </p>
              <p>
                Certain payments may be strictly non-refundable where the service has already been provided, registration has been processed, a seat/ticket has been issued, an application has been reviewed, or where the applicable event-specific terms state that the payment is non-refundable.
              </p>
            </div>

            <!-- Section 2 -->
            <div class="policy-section" id="sec-2">
              <div class="policy-section-header">
                <div class="section-number">2</div>
                <h2>Player Registration Fees</h2>
              </div>
              <p>Where player registration involves a registration fee:</p>
              <ul class="policy-list">
                <li>Submission of a registration form does not automatically guarantee selection, participation, auction selection, or team allocation.</li>
                <li>Registration fees may be non-refundable once the registration/application has been successfully processed.</li>
                <li>If a registration is cancelled by UPPHL before the relevant selection or registration process is completed, a refund may be considered according to the applicable event or registration rules.</li>
                <li>If a player voluntarily withdraws after successful registration, the fee may not be refundable unless otherwise stated in the applicable registration terms.</li>
                <li>If duplicate or erroneous payments are made, the user may contact UPPHL with transaction details for verification.</li>
              </ul>
            </div>

            <!-- Section 3 -->
            <div class="policy-section" id="sec-3">
              <div class="policy-section-header">
                <div class="section-number">3</div>
                <h2>Event and Ticket Cancellation</h2>
              </div>
              <p>If tickets or paid event access are offered:</p>

              <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--orange-dark); margin: 18px 0 8px;">3.1 Event Cancelled by UPPHL</h3>
              <p>
                If an event is cancelled by UPPHL, the organizer may provide a refund, rescheduling option, credit, or another remedy depending on the circumstances and the applicable event terms.
              </p>

              <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--orange-dark); margin: 20px 0 8px;">3.2 Event Rescheduled</h3>
              <p>
                If an event is rescheduled, tickets may remain valid for the revised date unless otherwise communicated.
              </p>
              <p>
                Where permitted, eligible ticket holders may be offered a refund according to the specific cancellation or rescheduling announcement.
              </p>

              <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--orange-dark); margin: 20px 0 8px;">3.3 Event Postponement</h3>
              <p>
                If an event is postponed due to circumstances beyond the reasonable control of UPPHL, including weather conditions, venue issues, government directions, public safety concerns, force majeure events, or other unforeseen circumstances, UPPHL may reschedule the event instead of providing an immediate refund.
              </p>
              <p>
                The applicable decision will be communicated through official channels.
              </p>
            </div>

            <!-- Section 4 -->
            <div class="policy-section" id="sec-4">
              <div class="policy-section-header">
                <div class="section-number">4</div>
                <h2>Merchandise</h2>
              </div>
              <p>If merchandise is sold through the official website:</p>
              <ul class="policy-list">
                <li>Orders may only be cancelled before dispatch, unless otherwise stated.</li>
                <li>Once an order has been dispatched, cancellation may not be available.</li>
                <li>Damaged, defective, incorrect, or incomplete items may qualify for replacement or refund subject to verification.</li>
                <li>Users may be required to provide photographs, videos, order information, or other evidence for damaged or incorrect products.</li>
                <li>Customized or personalized products may not be eligible for cancellation or refund unless required by applicable law.</li>
                <li>The specific return period and conditions, if applicable, will be mentioned on the relevant product or order page.</li>
              </ul>
            </div>

            <!-- Section 5 -->
            <div class="policy-section" id="sec-5">
              <div class="policy-section-header">
                <div class="section-number">5</div>
                <h2>Duplicate Payments</h2>
              </div>
              <p>
                If you believe that you have been charged more than once for the same transaction, please contact us as soon as possible.
              </p>
              <p>
                After verifying the transaction, an eligible duplicate payment may be refunded through the original payment method or another appropriate method.
              </p>
            </div>

            <!-- Section 6 -->
            <div class="policy-section" id="sec-6">
              <div class="policy-section-header">
                <div class="section-number">6</div>
                <h2>Failed Transactions</h2>
              </div>
              <p>
                If money has been deducted from your bank account, card, UPI account, or other payment method but the website shows the transaction as failed or incomplete, please contact us with:
              </p>
              <ul class="policy-list">
                <li>Name</li>
                <li>Registered mobile number</li>
                <li>Email address</li>
                <li>Transaction/reference ID</li>
                <li>Date and time of payment</li>
                <li>Amount paid</li>
                <li>Screenshot or proof of payment, where available</li>
              </ul>
              <p style="margin-top: 14px;">
                We will verify the transaction with the relevant payment service provider. If the payment was not successfully received by UPPHL, the amount may be automatically reversed by the payment provider according to its applicable settlement and reversal timelines.
              </p>
            </div>

            <!-- Section 7 -->
            <div class="policy-section" id="sec-7">
              <div class="policy-section-header">
                <div class="section-number">7</div>
                <h2>Refund Processing</h2>
              </div>
              <p>
                Approved refunds will generally be initiated using the original payment method, where technically possible.
              </p>
              <p>
                The time required for the refunded amount to appear in the user's bank account, card, UPI account, or other payment method may depend on the payment gateway, bank, card network, or financial institution.
              </p>
              <p>
                UPPHL is not responsible for delays caused solely by banks, payment gateways, card networks, or other third-party financial institutions.
              </p>
            </div>

            <!-- Section 8 -->
            <div class="policy-section" id="sec-8">
              <div class="policy-section-header">
                <div class="section-number">8</div>
                <h2>Non-Refundable Payments</h2>
              </div>
              <p>Unless specifically stated otherwise, the following may be non-refundable:</p>
              <ul class="policy-list">
                <li>Application or registration fees after processing</li>
                <li>Fees for services already provided</li>
                <li>Charges for completed registrations</li>
                <li>Customized or personalized products</li>
                <li>Tickets where the applicable ticket terms state that they are non-refundable</li>
                <li>Payments associated with a player's voluntary withdrawal</li>
                <li>Administrative or processing charges, where applicable</li>
                <li>Any other payment expressly identified as non-refundable at the time of purchase</li>
              </ul>
            </div>

            <!-- Section 9 -->
            <div class="policy-section" id="sec-9">
              <div class="policy-section-header">
                <div class="section-number">9</div>
                <h2>How to Request a Refund</h2>
              </div>
              <p>To request a refund or report a payment issue, contact:</p>

              <div class="policy-officer-box">
                <div class="policy-officer-info">
                  <h3>UP Pro Handball League (UPPHL)</h3>
                  <p>Billing &amp; Refund Request Desk</p>
                  <div class="officer-details">
                    <div><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <a href="mailto:uphandballleague@gmail.com">uphandballleague@gmail.com</a></div>
                    <div><i class="fa-solid fa-phone"></i> <strong>Phone:</strong> <a href="tel:7084900009">+91 7084900009</a></div>
                    <div><i class="fa-solid fa-location-dot"></i> <strong>Address:</strong> D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006</div>
                    <div><i class="fa-solid fa-globe"></i> <strong>Website:</strong> <a href="https://upphl.com" target="_blank">https://upphl.com</a></div>
                  </div>
                </div>
              </div>

              <p style="margin-top: 18px;"><strong>Please include the following details in your email:</strong></p>
              <ul class="policy-list">
                <li>Full name</li>
                <li>Registered email address</li>
                <li>Mobile number</li>
                <li>Order/registration ID</li>
                <li>Transaction ID</li>
                <li>Amount paid</li>
                <li>Date of payment</li>
                <li>Reason for the refund request</li>
                <li>Supporting documents, if applicable</li>
              </ul>
              <p style="margin-top: 12px; font-size: 0.92rem; color: var(--text-muted);">
                <em>Note: Requests without sufficient transaction details may take longer to verify.</em>
              </p>
            </div>

            <!-- Section 10 -->
            <div class="policy-section" id="sec-10">
              <div class="policy-section-header">
                <div class="section-number">10</div>
                <h2>Refund Review</h2>
              </div>
              <p>
                All refund requests are subject to verification. Submitting a refund request does not automatically guarantee approval.
              </p>
              <p>UPPHL may review:</p>
              <ul class="policy-list">
                <li>Transaction status</li>
                <li>Registration status</li>
                <li>Service delivery status</li>
                <li>Ticket status</li>
                <li>Event status</li>
                <li>Cancellation circumstances</li>
                <li>Payment gateway records</li>
                <li>Supporting documents</li>
                <li>Applicable event-specific terms</li>
              </ul>
              <p style="margin-top: 12px;">
                The final decision will be communicated to the requester.
              </p>
            </div>

            <!-- Section 11 -->
            <div class="policy-section" id="sec-11">
              <div class="policy-section-header">
                <div class="section-number">11</div>
                <h2>Chargebacks and Payment Disputes</h2>
              </div>
              <p>
                Users are encouraged to contact UPPHL before initiating a chargeback or payment dispute with their bank or payment provider.
              </p>
              <p>
                We will make reasonable efforts to investigate and resolve genuine payment issues.
              </p>
              <p>
                Fraudulent, unauthorized, or abusive payment disputes may be investigated and may be reported to the relevant payment provider or authorities where appropriate.
              </p>
            </div>

            <!-- Section 12 -->
            <div class="policy-section" id="sec-12">
              <div class="policy-section-header">
                <div class="section-number">12</div>
                <h2>Changes to This Policy</h2>
              </div>
              <p>
                UPPHL may modify this Refund &amp; Cancellation Policy from time to time. Any updated version will be published on this page with a revised "Last Updated" date.
              </p>
            </div>

            <!-- Section 13 -->
            <div class="policy-section" id="sec-13">
              <div class="policy-section-header">
                <div class="section-number">13</div>
                <h2>Contact Information</h2>
              </div>
              <p>For any queries regarding this Refund &amp; Cancellation Policy, please reach out to us:</p>

              <div class="policy-officer-box">
                <div class="policy-officer-info">
                  <h3>UP Pro Handball League (UPPHL)</h3>
                  <p>Official Finance &amp; Accounts Desk</p>
                  <div class="officer-details">
                    <div><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <a href="mailto:uphandballleague@gmail.com">uphandballleague@gmail.com</a></div>
                    <div><i class="fa-solid fa-phone"></i> <strong>Phone:</strong> <a href="tel:7084900009">+91 7084900009</a></div>
                    <div><i class="fa-solid fa-location-dot"></i> <strong>Address:</strong> D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006</div>
                    <div><i class="fa-solid fa-globe"></i> <strong>Website:</strong> <a href="https://upphl.com" target="_blank">https://upphl.com</a></div>
                  </div>
                </div>
                <div class="policy-officer-action">
                  <a href="contact.php" class="btn-contact">
                    <i class="fa-solid fa-envelope-open-text"></i> Contact Us
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
