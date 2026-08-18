<?php 
require_once __DIR__ . "/ClobalView.php";

Class MenuView extends GlobalView{



function content($categories, $items){

  $selected_category = $categories[0]['id'];
  ?>

  <!-- PAGE BANNER -->
  <section class="page-banner">
    <div class="crumb"><a href="/Client/">Home</a> / Menu</div>
    <h1>Our <span class="accent">Full</span> Menu</h1>
    <p>Savor the flavors, one dish at a time</p>
  </section>

  <!-- MENU CATEGORY NAV -->
  <nav class="menu-cat-nav">
    <div class="wrap">
      <?php foreach ($categories as $cat): ?>
        <a href="#"
           class="cat-link <?php echo $cat['id'] == $selected_category ? 'active' : ''; ?>"
           data-category="<?php echo $cat['id']; ?>">
          <?php echo htmlspecialchars($cat['title']); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>

  <!-- MENU LIST -->
  <section class="menu-page">
    <div class="wrap">

      <?php foreach ($categories as $cat): ?>
        <div class="menu-category"
             data-category-panel="<?php echo $cat['id']; ?>"
             style="<?php echo $cat['id'] == $selected_category ? '' : 'display:none;'; ?>">

          <div class="menu-cat-head">
            <h2><?php echo htmlspecialchars($cat['title']); ?></h2>
            <div class="rule-line"></div>
          </div>

          <?php
          $catItems = array_filter($items, function($item) use ($cat) {
              return $item['category'] == $cat['id'];
          });

          if (empty($catItems)):
          ?>
            <p style="color:var(--muted); font-size:13.5px;">No items yet in this category.</p>
          <?php
          else:
            foreach ($catItems as $item):
          ?>
            <div class="menu-row">
              <div class="menu-row-img">
                <img src="/images/<?php echo htmlspecialchars($item['photo_url']); ?>"
                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                     onerror="this.style.display='none'">
              </div>
              <div class="menu-row-body">
                <div class="menu-row-top">
                  <span class="menu-row-name">
                    <?php echo htmlspecialchars($item['name']); ?>
                    <?php if (!empty($item['is_recommended'])): ?>
                      <span class="menu-row-tag">Chef's Pick</span>
                    <?php endif; ?>
                  </span>
                  <span class="menu-row-dots"></span>
                  <span class="menu-row-price">$<?php echo htmlspecialchars($item['price']); ?></span>
                </div>
                <div class="menu-row-desc"><?php echo htmlspecialchars($item['description']); ?></div>
              </div>
            </div>
          <?php
            endforeach;
          endif;
          ?>

        </div>
      <?php endforeach; ?>

    </div>
  </section>

  <script>
    const catLinks = document.querySelectorAll('.cat-link');
    const panels = document.querySelectorAll('[data-category-panel]');

    catLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetCat = link.dataset.category;

        catLinks.forEach(l => l.classList.toggle('active', l === link));
        panels.forEach(p => {
          p.style.display = (p.dataset.categoryPanel === targetCat) ? '' : 'none';
        });
      });
    });
  </script>

  <?php
}

function displayMenuView($contact_details,$opening_hours,$categories,$items){
 $this->header($contact_details['mark']);
 $this->topBar($contact_details);
 $this->nav($contact_details['mark']);
 $this->content($categories,$items);
 $this->footer($contact_details,$opening_hours);
}

}
?>


