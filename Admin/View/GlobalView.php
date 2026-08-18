<?php

Class GlobalView{
  function header($mark){
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($mark) ?? '';?>_Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Admin/index.css">
    </head>
    <?php
  }

  function scirptSideBar(){
    ?>
    <script>
  const sidebar = document.querySelector('.admin-sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');

  function openSidebar(){
    sidebar.classList.add('open');
    overlay.classList.add('show');
  }
  function closeSidebar(){
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
  }

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);

document.querySelectorAll('.admin-nav a').forEach(link => {
  if (link.getAttribute('href') === window.location.pathname){
    link.classList.add('active');
  }
});
</script>
</body>
</html>
<?php 
  }

  function sideBar($mark){
    ?>
     <!-- SIDEBAR -->
   <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
  <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
  </button>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="admin-sidebar">
    <div class="brand"><?php echo htmlspecialchars($mark)?? '' ; ?></div>

    <nav class="admin-nav">
      <a href="/Admin/Dashboard/" >
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Overview
      </a>
      <a href="/Admin/Menu/">
        <svg viewBox="0 0 24 24"><path d="M4 19V5a2 2 0 012-2h9l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2z"/><path d="M14 3v5h5"/><path d="M8 13h8M8 17h5"/></svg>
        Menu
      </a>
      <a href="/Admin/Reservations/">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
        Reservations
      </a>
      <a href="/Admin/Report/">
        <svg viewBox="0 0 24 24"><path d="M4 20V10M11 20V4M18 20v-7"/></svg>
        Reports
      </a>
      <a href="/Admin/Contact/">
        <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
        Contact
      </a>
      <a href="/Admin/Parameters/">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.6V21a2 2 0 11-4 0v-.2a1.7 1.7 0 00-1-1.6 1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.9 1.7 1.7 0 00-1.6-1H3a2 2 0 110-4h.2a1.7 1.7 0 001.6-1 1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.9.3H9a1.7 1.7 0 001-1.6V3a2 2 0 114 0v.2a1.7 1.7 0 001 1.6 1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.9V9a1.7 1.7 0 001.6 1H21a2 2 0 110 4h-.2a1.7 1.7 0 00-1.6 1z"/></svg>
        Parameters
      </a>

      <div class="logout">
        <a href="/Admin/logout.php">
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
          Logout
        </a>
      </div>
    </nav>
  </aside>
  <?php
  }
}

?>