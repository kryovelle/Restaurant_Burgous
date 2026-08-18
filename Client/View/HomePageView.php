<?php 
require_once __DIR__ . "/ClobalView.php";

Class HomePageView extends GlobalView{

function content($categories,$items){
?>

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <div>
      <div class="hero-eyebrow">Taste the Difference</div>
      <h1>Where Every Meal<br>Becomes a <br><span class="accent"> Memory</span></h1>
      <p>Indulge in exceptional cuisine made with the finest ingredients, served in an elegant setting designed for unforgettable moments.</p>
      <div class="hero-ctas">
        <a href="/Client/Reservation/" class="btn-solid">Reserve Your Table</a>
        <a href="/Client/Menu/" class="btn-outline">View Menu</a>
      </div>
      <div class="hero-dots">
        <span class="active"></span><span></span><span></span>
      </div>
    </div>
    <div class="hero-photo">
      <img src="/images/hero-photo.jpg" alt="hero-photo.jpg" class="plate">
    </div>
  </div>
</section>

<!-- MENU CATEGORIES -->
<section class="categories">
  <div class="wrap">
    <div class="section-title">
      Our Menu Categories
      <div class="rule">✦</div>
    </div>
  <div class="cat-grid" style="grid-template-columns:repeat(<?php echo htmlspecialchars(count($categories))?>,1fr); ">
    <?php foreach ($categories as $category):?>

      <div class="cat-item" >
        <img src="/images/<?php echo htmlspecialchars($category['icon']) ; ?>" alt="<?php echo htmlspecialchars($category['icon']) ; ?>" class="cat-circle" >
        <span class="name"><?php echo htmlspecialchars($category['title']) ; ?></span>
      </div>

    <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CHEF'S RECOMMENDATIONS -->
<section class="recommend" id="menu">
  <div class="wrap">
    <div class="section-title">
      Chef's Recommendations
      <div class="rule">👑</div>
    </div>

    <div class="dish-grid">
      <?php foreach ($items as $item): ?>

      <div class="dish-card">
        <img src="/images/<?php echo htmlspecialchars($item['photo_url']); ?>" class="dish-img">

        <div class="dish-body">
          <div class="dish-name"><?php echo htmlspecialchars($item['name']) ?></div>
          <div class="dish-desc"><?php echo htmlspecialchars($item['description']) ?></div>
          <div class="dish-foot"><span class="dish-price"><?php echo htmlspecialchars($item['price']) ?>&nbsp; $</span> </div>
        </div>
      </div>
      <?php endforeach;?>

    </div>

    <div class="see-full"><a href="/Client/Menu/">See Full Menu →</a></div>
  </div>
</section>

<!-- PROMO BANNER -->
<section class="promo">
  <div class="wrap promo-inner">
    <div class="eyebrow">Special Offer</div>
    <h2>Get 20% Off On Your First Reservation</h2>
    <a href="/Client/Reservation/">Reserve a Table</a>
  </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
  <div class="wrap feat-grid">

    <!-- Leaf: fresh / local ingredients -->
    <div class="feat-item">
      <div class="feat-icon-circle">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#B9860F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 19c-1.5-5 1-11 8-14 3 3 4 8 2 12-2.5 4.5-7 3.5-10 2z"/>
          <path d="M6 18c3-4 6-7 11-11.5"/>
        </svg>
      </div>
      <div class="title">Seasonal Produce</div>
      <div class="sub">Sourced weekly from local farms</div>
    </div>

    <!-- Chef hat: expert chefs -->
    <div class="feat-item">
      <div class="feat-icon-circle">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#B9860F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M7 11c-2.2 0-4-1.7-4-3.8 0-2.3 2-4 4.3-3.7C8 2 9.8 1 12 1s4 1 4.7 2.5C19 3.2 21 4.9 21 7.2 21 9.3 19.2 11 17 11"/>
          <path d="M7 11v3h10v-3"/>
          <path d="M6.5 14h11l-.7 6.2a1 1 0 01-1 .8H8.2a1 1 0 01-1-.8L6.5 14z"/>
        </svg>
      </div>
      <div class="title">Skilled Kitchen Team</div>
      <div class="sub">Trained in classic technique</div>
    </div>

    <!-- House / sofa: cozy ambience -->
    <div class="feat-item">
      <div class="feat-icon-circle">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#B9860F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 20v-6a2 2 0 012-2h12a2 2 0 012 2v6"/>
          <path d="M4 20h16"/>
          <path d="M6 12V9a2 2 0 012-2h8a2 2 0 012 2v3"/>
          <path d="M6 15.5h.01M18 15.5h.01"/>
        </svg>
      </div>
      <div class="title">Relaxed Setting</div>
      <div class="sub">A room built for lingering over dinner</div>
    </div>

    <!-- Delivery truck: fast delivery -->
    <div class="feat-item">
      <div class="feat-icon-circle">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#B9860F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="8" width="11" height="8" rx="1"/>
          <path d="M13 11h4l3 2.5V16h-7z"/>
          <circle cx="6.5" cy="18" r="1.6"/>
          <circle cx="17" cy="18" r="1.6"/>
        </svg>
      </div>
      <div class="title">Quick Turnaround</div>
      <div class="sub">Straight from kitchen to door</div>
    </div>

  </div>
</section>
<?php
}

function displayHomePageView($contact_details,$opening_hours,$categories,$items){
 $this->header($contact_details['mark']);
 $this->topBar($contact_details);
 $this->nav($contact_details['mark']);
 $this->content($categories,$items);
 $this->footer($contact_details,$opening_hours);
}

}
?>


