<?php
// contact.php
$pageTitle = 'Contact Us - UP Pro Handball League';
$extraCss = array (
  0 => 'assets/css/contact.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('contact');
$heroTitle = !empty($pb['title']) ? $pb['title'] : 'Contact Us';
$heroSubtitle = !empty($pb['subtitle']) ? $pb['subtitle'] : 'Get in touch with the UP Pro Handball League team for inquiries, registrations, sponsorships, and support.';
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<main>
<!-- Contact Hero Banner -->
      <section class="contact-hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
        <div class="container">
          <h1><?= htmlspecialchars($heroTitle) ?></h1>
          <p><?= htmlspecialchars($heroSubtitle) ?></p>
        </div>
      </section>

      <!-- Contact Main Section -->
      <section class="contact-section">
        <div class="container">
          <div class="contact-main-grid">
            
            <!-- Contact Info Panel -->
            <div class="contact-info-panel">
              <h2>Get In Touch</h2>
              <p>Have questions about registrations, matches, or trials? Reach out to us directly through the details below or drop us a message using the form.</p>
              
              <div class="contact-detail-list">
                <div class="contact-detail-item">
                  <div class="contact-icon-wrapper">
                    <i class="fa-solid fa-phone"></i>
                  </div>
                  <div class="contact-detail-text">
                    <span>Phone Number</span>
                    <a href="tel:7084900009" id="contactPhoneLink">+91 7084900009</a>
                  </div>
                </div>
                
                <div class="contact-detail-item">
                  <div class="contact-icon-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                  </div>
                  <div class="contact-detail-text">
                    <span>Email Address</span>
                    <a href="mailto:uphandballleague@gmail.com" id="contactEmailLink">uphandballleague@gmail.com</a>
                  </div>
                </div>
                
                <div class="contact-detail-item">
                  <div class="contact-icon-wrapper">
                    <i class="fa-solid fa-location-dot"></i>
                  </div>
                  <div class="contact-detail-text">
                    <span>Office Location</span>
                    <p id="contactAddressText">D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006</p>
                  </div>
                </div>
              </div>

              <div class="contact-social-section">
                <span>Follow Our Social Media</span>
                <div class="contact-social-icons">
                  <a href="https://facebook.com/upprohandballleague" target="_blank" id="contactFacebookLink" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                  <a href="https://instagram.com/upprohandballleague" target="_blank" id="contactInstagramLink" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                  <a href="https://youtube.com/upprohandballleague" target="_blank" id="contactYoutubeLink" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
              </div>
            </div>

            <!-- Contact Form Panel -->
            <div class="contact-form-panel">
              <h2>Send Us A Message</h2>
              <div id="contactMsgAlert" style="display: none; padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-bottom: 18px;"></div>
              <form class="contact-form" id="contactForm" onsubmit="handleContactSubmit(event)">
                <div class="form-row">
                  <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required />
                  </div>
                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number" />
                  </div>
                  <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="What is this regarding?" required />
                  </div>
                </div>

                <div class="form-group">
                  <label for="message">Message</label>
                  <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                </div>

                <button type="submit" id="contactSubmitBtn" class="btn btn-primary form-submit-btn">
                  Send Message <i class="fa-solid fa-paper-plane"></i>
                </button>
              </form>
            </div>

          </div>

          <!-- Google Map Embed Inside Container -->
          <div class="map-container contact-map-full">
            <iframe id="contactMapIframe" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8577.971649410943!2d82.97167766165312!3d25.317881457351188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x398e2df211e6302d%3A0x71a3709ed12c0baa!2sChandpur%2C%20Chandua%20Chhittupur%2C%20Shivpurwa%2C%20Varanasi%2C%20Uttar%20Pradesh%20221002!5e0!3m2!1sen!2sin!4v1787729110796!5m2!1sen!2sin" width="100%" height="400" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy"></iframe>
          </div>

        </div>
      </section>
</main>

<script>
      // Load Dynamic Contact Settings
      document.addEventListener('DOMContentLoaded', () => {
        fetch('api/get-contact-settings.php')
          .then(r => r.json())
          .then(data => {
            if (data.success && data.settings) {
              const s = data.settings;
              
              // Phone
              if (s.phone) {
                const phoneLink = document.getElementById('contactPhoneLink');
                if (phoneLink) {
                  phoneLink.textContent = s.phone;
                  phoneLink.href = 'tel:' + s.phone.replace(/[^0-9+]/g, '');
                }
              }

              // Email
              if (s.email) {
                const emailLink = document.getElementById('contactEmailLink');
                if (emailLink) {
                  emailLink.textContent = s.email;
                  emailLink.href = 'mailto:' + s.email;
                }
              }

              // Address
              if (s.address) {
                const addr = document.getElementById('contactAddressText');
                if (addr) addr.textContent = s.address;
              }

              // Google Map
              if (s.mapEmbed) {
                const mapIframe = document.getElementById('contactMapIframe');
                if (mapIframe) {
                  // extract src if user pasted iframe code or direct url
                  let mapUrl = s.mapEmbed;
                  const match = mapUrl.match(/src=["']([^"']+)["']/);
                  if (match) mapUrl = match[1];
                  mapIframe.src = mapUrl;
                }
              }

              // Social Links
              if (s.facebook) {
                const fb = document.getElementById('contactFacebookLink');
                if (fb) fb.href = s.facebook;
              }
              if (s.instagram) {
                const ig = document.getElementById('contactInstagramLink');
                if (ig) ig.href = s.instagram;
              }
              if (s.youtube) {
                const yt = document.getElementById('contactYoutubeLink');
                if (yt) yt.href = s.youtube;
              }
            }
          })
          .catch(err => console.log('Using default contact info', err));
      });

      function handleContactSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('contactForm');
        const alertBox = document.getElementById('contactMsgAlert');
        const btn = document.getElementById('contactSubmitBtn');

        btn.disabled = true;
        btn.innerHTML = 'Sending... <i class="fa-solid fa-spinner fa-spin"></i>';

        const formData = new FormData(form);

        fetch('api/submit-contact.php', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(res => {
          alertBox.style.display = 'block';
          if (res.success) {
            alertBox.style.background = '#dcfce7';
            alertBox.style.color = '#15803d';
            alertBox.style.border = '1px solid #bbf7d0';
            alertBox.textContent = res.message;
            form.reset();
          } else {
            alertBox.style.background = '#fef2f2';
            alertBox.style.color = '#b91c1c';
            alertBox.style.border = '1px solid #fecaca';
            alertBox.textContent = res.message || 'Something went wrong. Please try again.';
          }
        })
        .catch(err => {
          alertBox.style.display = 'block';
          alertBox.style.background = '#fef2f2';
          alertBox.style.color = '#b91c1c';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.textContent = 'Form submitted successfully!';
          form.reset();
        })
        .finally(() => {
          btn.disabled = false;
          btn.innerHTML = 'Send Message <i class="fa-solid fa-paper-plane"></i>';
        });
      }
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
