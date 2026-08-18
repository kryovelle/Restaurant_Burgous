<?php 
require_once __DIR__ . "/ClobalView.php";

Class ContactView extends GlobalView{
 public  $show_popup; 


function content(){
  
 
  ?>

  <!-- PAGE BANNER -->
  <section class="page-banner">
    <div class="crumb"><a  href="/Client/">Home</a> / Contact</div>
    <h1><span class="accent">Contact</span> Us</h1>
    <p>Questions, feedback, or a special request — send us a message and we'll get back to you.</p>
  </section>

  <!-- CONTACT FORM -->
  <section class="reserve-section">
    <div class="wrap">

      <!-- The form points directly to the same page (or your PHP self) -->
      <form class="reserve-card" id="contactForm" action="/Client/redirect.php" method="POST">
        <h2>Send a <span class="accent">Message</span></h2>

        <div class="form-row">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" placeholder="Your full name" required maxlength="300">
        </div>

        <div class="form-row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <div class="form-row">
          <label for="phone">Phone Number</label>
          <input type="phone" id="phone" name="phone" placeholder="+1 234 567 8900" required>
        </div>

        <div class="form-row">
          <label for="message">Message</label>
          <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
        </div>

        <button type="submit" class="form-submit" name="contactForm" >Send Message</button>
        <div class="form-hint">We usually reply within 24 hours.</div>
      </form>

      <div class="confirm-card" id="confirmCard">
        <div class="confirm-icon">✓</div>
        <h2>Message Sent</h2>
        <div class="sub">Thanks for reaching out — we'll be in touch soon.</div>
        <div class="confirm-actions">
          <a href="/Client/" class="btn-outline">Back to Home</a>
          <a href="/Client/Menu/" class="btn-solid">View Menu</a>
        </div>
      </div>

    </div>
  </section>

  <script>
    const contactForm = document.getElementById('contactForm');
    const confirmCard = document.getElementById('confirmCard');

    // Check if success parameter exists in URL
    const urlParams = new URLSearchParams(window.location.search);
    const showPopup = urlParams.has('success');

    if (showPopup) {
        contactForm.classList.add('hide');
        confirmCard.classList.add('show');
        setTimeout(() => {
            confirmCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
</script>
 

  <?php
}


function displayContactView($contact_details,$opening_hours_org){
 $this->header($contact_details['mark']);
 $this->topBar($contact_details);
 $this->nav($contact_details['mark']);
 $this->content();
 $this->footer($contact_details,$opening_hours_org);
}

}
?>


