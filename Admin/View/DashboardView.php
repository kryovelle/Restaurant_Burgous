<?php
require_once __DIR__ . "/GlobalView.php";
Class DashboardView extends GlobalView{
   function content($mark,$cards,$up_reservations,$rec_contacts){
    ?>
<body>

<div class="admin-shell">
   <!-- SIDE BAR -->
    <?php echo $this->sideBar($mark);?>

  <!-- MAIN PANEL -->
  <main class="admin-main">

    <div class="admin-topline">
      <div>
        <h1>Overview</h1>
        <p><?php echo date('l, F j, Y');?></p>
      </div>
      <div class="admin-user">
        <div class="avatar">A</div>
        <span>Admin</span>
      </div>
    </div>

      <!-- STAT CARDS -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="label">Today</div>
        <div class="value" data-stat="count_reservations_today"><?php echo htmlspecialchars($cards['count_reservations_today']) ?></div>
        <div class="sub">reservations</div>
      </div>
      <div class="stat-card">
        <div class="label">Pending</div>
        <div class="value warn" data-stat="count_pending_reservations"><?php echo htmlspecialchars($cards['count_pending_reservations']) ?></div>
        <div class="sub">today's awaiting confirmation</div>
      </div>
      <div class="stat-card">
        <div class="label">This Week</div>
        <div class="value" data-stat="count_reservations_week"><?php echo htmlspecialchars($cards['count_reservations_week']) ?></div>
        <div class="sub">total bookings</div>
      </div>
      <div class="stat-card">
        <div class="label">Occupancy</div>
        <div class="value" data-stat="tables_perc"><?php echo htmlspecialchars($cards['tables_perc']) ?>%</div>
        <div class="sub">tables booked today</div>
      </div>
      <div class="stat-card">
        <div class="label">Overdue</div>
        <div class="value alert" data-stat="count_overdue"><?php echo htmlspecialchars($cards['count_overdue']) ?></div>
        <div class="sub">seated past end time today</div>
      </div>
    </div>

    <!-- CONTENT GRID -->
    <div class="content-grid">

      <!-- UPCOMING RESERVATIONS -->
      <div class="panel">

        <div class="panel-head">
          <h2>Upcoming (next 3 hrs)</h2>
          <a href="/Admin/Reservations/">View all →</a>
        </div>    
       <div id="reservationsList">
         <?php if(!$up_reservations):?>
              <p style="color:var(--muted); font-size:13px; padding:8px 0;">No upcoming reservations in the next few hours.</p>
              <?php else:
            foreach($up_reservations as $reservation):?>
              <div class="upcoming-row">
                <div class="upcoming-time"><?php echo htmlspecialchars(date('H:i', strtotime($reservation['time_slot']))) ?></div>
                <div class="upcoming-info">
                  <div class="upcoming-name">
                    <?php echo htmlspecialchars($reservation['first_name']).', '.
                    htmlspecialchars($reservation['last_name']).' _ Party of '.
                    htmlspecialchars($reservation['guests'])
                    ?>
                  </div>
                  <div class="upcoming-meta">Table <?php echo htmlspecialchars($reservation['table_number']).' . '.
                    htmlspecialchars($reservation['phone'])?>
                  </div>
                </div>
                <span class="status-pill status-<?php echo htmlspecialchars($reservation['status'])?>"><?php echo htmlspecialchars($reservation['status'])?></span>
              </div>
            <?php endforeach ;
         endif; ?>
        </div>
      </div>


      <!-- RECENT CONTACT MESSAGES -->
      <div class="panel">

         <div class="panel-head">
          <h2>Recent Contact Messages</h2>
          <a href="/Admin/Contact/">View all →</a>
         </div>
        <div id="msgsList">
            <?php if(!$rec_contacts):?><p style="color:var(--muted); font-size:13px; padding:8px 0;">No messages yet.</p>
            <?php else:
                foreach($rec_contacts as $contact):?>
                  <div class="msg-row">
                    <div class="msg-top">
                      <span class="msg-name">

                        <span class="<?php echo $contact['is_read'] ? '' :'msg-unread-dot'?>"></span>

                      <?php echo htmlspecialchars($contact['name']);?></span>
                      <span class="msg-time"><?php echo htmlspecialchars($contact['created_at']);?></span>
                    </div>
                    <div class="msg-preview">
                        <?php
                          $message = $contact['message'];
                          $maxLength = 60;
                          if (mb_strlen($message) > $maxLength) {
                              echo htmlspecialchars(mb_substr($message, 0, $maxLength)) . '...';
                          } else {
                              echo htmlspecialchars($message);
                          }
                        ?>
                    </div>
                  </div>
                <?php endforeach;
        endif;?>
      </div>

      </div>

    </div>

  </main>

</div>

<script>
  (function(){
      const POLL_INTERVAL = 15000; 

      function truncate(str, maxLength = 60){
        str = str ?? '';
        return str.length > maxLength ? str.slice(0, maxLength) + '...' : str;
      }

      function formatTime(timeStr){
        return (timeStr ?? '').slice(0, 5); // "19:15:00" -> "19:15"
      }

      function escapeHtml(str){
        const div= document.createElement('div');
        div.textContent=str ?? '';
        return div.innerHTML;
      }

      function renderReservationsList(up_reservations){
        const container=document.getElementById('reservationsList');

        if(!up_reservations || up_reservations.length==0){
          container.innerHTML='<p style="color:var(--muted); font-size:13px; padding:8px 0;">No upcoming reservations in the next few hours.</p>';
          return;
        }

        container.innerHTML = up_reservations.map( r =>
          `<div class="upcoming-row">
                <div class="upcoming-time">${escapeHtml(formatTime(r.time_slot))}</div>
                <div class="upcoming-info">
                  <div class="upcoming-name">${escapeHtml(r.first_name)}, ${escapeHtml(r.last_name)} _ Party of  ${escapeHtml(r.guests)}
                  </div>
                  <div class="upcoming-meta">Table ${escapeHtml(r.table_number)} . ${escapeHtml(r.phone)} 
                  </div>
                </div>
                <span class="status-pill status-${escapeHtml(r.status)}">${escapeHtml(r.status)}</span>
              </div>
          `).join('');
      }

      function renderMsgsList(rec_contacts){
        const container= document.getElementById('msgsList');
        if(!rec_contacts ||rec_contacts.length==0 ){
          container.innerHTML='<p style="color:var(--muted); font-size:13px; padding:8px 0;">No messages yet.</p>';
          return;
        }
        container.innerHTML=rec_contacts.map( m =>
          `
          <div class="msg-row">
            <div class="msg-top">
              <span class="msg-name"><span class="${m.is_read ?'' :'msg-unread-dot'}"></span>${escapeHtml(m.name)}</span>
              <span class="msg-time">${escapeHtml(m.created_at)}</span>
            </div>
            <div class="msg-preview">${escapeHtml(truncate(m.message))}</div>
        </div>
          `
        ).join('');
      }

      async function refreshDashboard(){
        try{
          const res = await fetch('/Admin/Dashboard/?format=json');
          if(!res.ok) throw new Error ('Request failed: ' + res.status);
          const data = await res.json();

          //stats cards
          Object.keys(data.cards).forEach((key)=>{
            const el = document.querySelector(`[data-stat="${key}"]`);
            if(el) el.textContent=data.cards[key] + (key=='tables_perc' ? '%': '');
          });

          renderReservationsList(data.up_reservations);
          renderMsgsList(data.rec_contacts);
        }
        catch(err){
            console.error('Dashboard refresh failed:', err);
        }
      }

      setInterval(refreshDashboard,POLL_INTERVAL);

  })();

</script>


<?php
  }
  
  function displayDashboardView($mark,$cards,$up_reservations,$rec_contacts){
    $this->header($mark);
    $this->content($mark,$cards,$up_reservations,$rec_contacts);
    $this->scirptSideBar();
  }
}
?>