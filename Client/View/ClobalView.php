<?php
 Class GlobalView {

 function header($mark){
  ?>
      <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($mark ?? ''); ?> — Taste the Difference</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Client/index.css">
    </head>
    <body>
<?php
 }

  function footer($contact_details,$opening_hours){
    ?>
<footer id="contact">
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <div class="mark"><?php echo htmlspecialchars($contact_details['mark']) ?></div>
        <p>A perfect blend of taste, art, and ambiance — crafted to delight your senses.</p>
        <div class="foot-socials">
          <a href="<?php echo htmlspecialchars($contact_details['facebook'])?>"><?php $this->facebookIcon()?></a>

          <a  href="<?php echo htmlspecialchars($contact_details['instagram'])?>"><?php $this->instagramIcon() ?></a>

          <a  href="<?php echo htmlspecialchars($contact_details['twitter'])?>"><?php $this->twitterIcon()?></a>

        </div>
      </div>
      <div class="foot-col">
        <h4>Quick Links</h4>
          <a href="/Client/" class="active">Home</a>
          <a href="/Client/Menu/">Menu</a>
          <a href="/Client/Reservation/">Reservation</a>
          <a href="/Client/Contact/">Contact Us</a>
      </div>
      <div class="foot-col">
        <h4>Contact</h4>
        <p><?php echo htmlspecialchars($contact_details['location']) ?? ''?></p>
        <a href="tel:<?php echo htmlspecialchars($contact_details['phone_number']) ?? '' ;?>"><?php echo htmlspecialchars($contact_details['phone_number']) ?? '' ?></a>
        
       <a href="mailto:<?php echo htmlspecialchars($contact_details['email'] ?? ''); ?>">
      </div>
      <div class="foot-col">
        <h4>Hours</h4>
        <?php foreach ($opening_hours as $opening_hour) :?>
        <p><?php  echo htmlspecialchars($opening_hour['days']) ." : ". htmlspecialchars($opening_hour['open_time']) . "–" .htmlspecialchars($opening_hour['close_time']) ?? '' ;?></p>
        <?php endforeach;?>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© 2026 <?php echo htmlspecialchars($contact_details['mark']) ?? ''?>. All rights reserved.</span>
      <span>Privacy Policy · Terms of Service</span>
    </div>
  </div>
</footer>

<script>
  const navToggle = document.getElementById('navToggle');
  const navLinks = document.querySelector('nav.links');

  navToggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
  });
</script>
</body>
</html>
<?php
  }

  function topBar($contact_details){
    ?>
    <div class="topbar" >
  <div class="wrap">
    <div class="left">
      <span><?php $this->phoneIcon(); echo htmlspecialchars($contact_details['phone_number'])?></span>
      <span> <?php $this->emailIcon();echo htmlspecialchars($contact_details['email'])?></span>
    </div>
  
    <div class="right">
      <span>Follow Us:</span>
      <span class="socials">
        <a href="<?php  echo htmlspecialchars($contact_details['facebook'])?>"><?php $this->facebookIcon();?></a>

        <a href="<?php  echo htmlspecialchars($contact_details['instagram'])?>"><?php $this->instagramIcon(); ?></a>

        <a href="<?php echo htmlspecialchars($contact_details['twitter'])?>"><?php $this->twitterIcon();?> </a>
      </span>
    </div>
  </div>
</div>
<?php
  }

  function nav($mark){

    $currentPage = rtrim($_SERVER['REQUEST_URI'], '/');

    function isActive($url, $currentPage) {
        $url = rtrim($url, '/');

        // Special case for home
        if ($url == '/Client') {
            return $currentPage == $url ? 'active' : '';
        }

        return str_contains($currentPage, $url) ? 'active' : '';
    };

    
?>

<header class="mainnav">
  
  <div class="wrap navrow">

 <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
  <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>

    <nav class="links">
      <a href="/Client/" 
         class="<?= isActive('/Client/', $currentPage) ?>">
         Home
      </a>

      <a href="/Client/Menu/" 
         class="<?= isActive('/Client/Menu/', $currentPage) ?>">
         Menu
      </a>

      <a href="/Client/Reservation/" 
         class="<?= isActive('/Client/Reservation/', $currentPage) ?>">
         Reservation
      </a>

       <a href="/Client/Contact/" 
         class="<?= isActive('/Client/Contact/', $currentPage) ?>" id="fixed">
         Contact Us
      </a>

      <a href="/Client/#features" 
         class="<?= isActive('/Client/About/', $currentPage) ?> " id="fixed">
         About
      </a>

    </nav>

    <a href="/Client/" class="logo">
      <div class="mark"><?= htmlspecialchars($mark) ?></div>
      <div class="sub">Taste the Difference</div>
    </a>

    <nav class="links">
      <a href="/Client/Contact/" 
         class="<?= isActive('/Client/Contact/', $currentPage) ?>">
         Contact Us
      </a>

      <a href="/Client/#features" 
         class="<?= isActive('/Client/About/', $currentPage) ?>">
         About
      </a>
    </nav>

  </div>
</header>
<?php
}
function emailIcon(){
    ?>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C9971F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="5" width="18" height="14" rx="2"/>
      <path d="M3 7l9 6 9-6"/>
    </svg>
    <?php
}

function phoneIcon(){
    ?>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C9971F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
      <path d="M4 5c0 8.3 6.7 15 15 15l3-3.5-5-3-2 2c-2.4-1.2-4-2.8-5.2-5.2l2-2-3-5L4 5z"/>
    </svg>
    <?php
}

function facebookIcon(){
    ?>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="#C9971F">
      <path d="M13.5 21v-7.8h2.6l.4-3h-3v-1.9c0-.9.2-1.5 1.5-1.5h1.6V4.1c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.2H8v3h2.5V21h3z"/>
    </svg>
    <?php
}

function instagramIcon(){
    ?>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C9971F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
      <rect x="4" y="4" width="16" height="16" rx="4"/>
      <circle cx="12" cy="12" r="3.2"/>
      <circle cx="16.3" cy="7.7" r="0.6" fill="#C9971F" stroke="none"/>
    </svg>
    <?php
}

function twitterIcon(){
    ?>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="#C9971F">
      <path d="M13.6 10.6L20.4 3h-2l-5.6 6.4L8 3H3l7 10.2L3 21h2l6-6.8L16 21h5l-7.4-10.4z"/>
    </svg>
    <?php
}

 }
?>