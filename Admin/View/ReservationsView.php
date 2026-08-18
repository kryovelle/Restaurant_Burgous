<?php
require_once __DIR__ . "/GlobalView.php";

class ReservationsView extends GlobalView{
  function content($mark,$reservations,$tables){
    ?>
     
<body>

<div class="admin-shell">

  <!-- SIDEBAR (reuse GlobalView::sideBar($mark) when wired to PHP) -->
  <?php $this->sideBar($mark);?>     
  <!-- MAIN PANEL -->
  <main class="admin-main">

    <div class="page-head-row">
      <h1>Reservations Management</h1>
    </div>

    <!-- FILTER BAR -->
    <div class="res-filter-bar">
      <select id="filterStatus">
        <option value="">Status: All</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="seated">Seated</option>
        <option value="completed">Completed</option>
        <option value="declined">Declined</option>
        <option value="cancelled">Cancelled</option>
      </select>

      <select id="filterDate">
        <option value="">Date: All</option>
        <option value="today">Today</option>
        <option value="tomorrow">Tomorrow</option>
        <option value="week">This Week</option>
      </select>

      <select id="filterTable">
        <option value="">Table #: All</option>
        <?php if($tables && count($tables)!=0) :?>
        <?php foreach($tables as $table):?>
          <option value="<?php echo htmlspecialchars($table['table_number'])?>">Table <?php echo htmlspecialchars($table['table_number'])?></option>
        <?php endforeach; endif;?>
      </select>

      <span class="res-filter-sort">Sorted by Time ↑</span>
    </div>

    <!-- RESERVATIONS TABLE (static sample rows matching the wireframe) -->
    <div class="res-panel">
      <table class="res-table">
        <thead>
          <tr>
            <th>T#</th>
            <th>First</th>
            <th>Last</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Date</th>
            <th>Start</th>
            <th>Ex_End</th>
            <th>Re_End</th>
            <th>G</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="reservationsTableBody">
          <?php if(!$reservations || count($reservations)===0 ):?>
            <tr><td colspan="11" style="text-align:center; color:var(--muted); padding:30px;">No Reservations Available</td></tr>
       
          <?php else: foreach($reservations as $reservation):?>
          <tr>
            <td><?php echo htmlspecialchars($reservation['table_number'])?></td>
            <td><?php echo htmlspecialchars($reservation['first_name'])?></td>
            <td><?php echo htmlspecialchars($reservation['last_name'])?></td>
            <td><?php echo htmlspecialchars($reservation['phone'])?></td>
            <td><a class="email-link" href="<?php echo htmlspecialchars($reservation['email'])?>"><?php echo htmlspecialchars($reservation['email'])?></a></td>
            <td><?php echo htmlspecialchars($reservation['date'])?></td>
            <td><?php echo htmlspecialchars($reservation['time_slot'])?></td>
            <td><?php echo htmlspecialchars($reservation['expected_end_time'])?></td>
            <td>
              <?php
                $completedAt = $reservation['completed_at'] ?? null;

                if ($completedAt):
                    $expectedEnd = strtotime($reservation['date'] . ' ' . $reservation['expected_end_time']);
                    $actualEnd = strtotime($completedAt);
                    $overdueSeconds = $actualEnd - $expectedEnd;
              ?>
                <div><?php echo htmlspecialchars(date('Y-m-d H:i:s', $actualEnd)); ?></div>
                <?php if ($overdueSeconds > 0 && $reservation['status']=='seated'):
                    $days = intdiv($overdueSeconds, 86400);
                    $hours = intdiv($overdueSeconds % 86400, 3600);
                    $minutes = intdiv($overdueSeconds % 3600, 60);
                    $seconds = $overdueSeconds % 60;

                    $overdueLabel = ($days > 0 ? "{$days}d " : '')
                                  . sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                ?>
                  <div style="color:#A33A3A; font-size:11.5px; font-weight:600; margin-top:2px;">
                    Overdue: <?php echo htmlspecialchars($overdueLabel); ?>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <span style="color:var(--muted);">—</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($reservation['guests'])?></td>
              <?php 
              date_default_timezone_set('Africa/Algiers');
                $isOverdue = false;

                if ($reservation['status'] === 'seated'){
                    $today = date('Y-m-d');
                    $currentTime = date('H:i:s');

                    if ($reservation['date'] < $today){
                        // The reservation was for a previous day and never got marked completed
                        $isOverdue = true;
                    } elseif ($reservation['date'] === $today && $currentTime > $reservation['expected_end_time']){
                        // Same day, and we're past the expected end time
                        $isOverdue = true;
                    }
                }
              ?>

              <?php if ($isOverdue): ?>
               <td><span class="status-pill status-overdue"> seated . Overdue </span></td>
            <?php else:?>  
            <td><span class="status-pill status-<?php echo htmlspecialchars($reservation['status'])?>"><?php echo htmlspecialchars($reservation['status'])?></span></td>
            <?php endif;?>

            <td>
              <?php
                  switch ($reservation['status']){

                      case 'pending':
                          ?>
                          <div class="row-actions">
                              <button onclick="viewClientHistory('<?php echo htmlspecialchars($reservation['phone']); ?>', '<?php echo htmlspecialchars($reservation['email']); ?>')">View</button>
                              <a href="/Admin/Reservations/changeStatus?id=<?= $reservation['id']; ?>&status=declined">Decline</a>
                              <button onclick="handleConfirmation(<?php echo $reservation['id']; ?>)">Confirm</button>
                              <span class="muted-note">Awaiting customer</span>
                          </div>
                          <?php
                          break;

                      case 'confirmed':
                          ?>
                          <div class="row-actions">
                              <button onclick="viewClientHistory('<?php echo htmlspecialchars($reservation['phone']); ?>', '<?php echo htmlspecialchars($reservation['email']); ?>')">View</button>
                              <a href="/Admin/Reservations/changeStatus?id=<?= $reservation['id']; ?>&status=seated">Mark seated</a>
                              <a href="/Admin/Reservations/changeStatus?id=<?= $reservation['id']; ?>&status=cancelled">Cancel</a>
                          </div>
                          <?php
                          break;

                      case 'seated':
                          ?>
                          <div class="row-actions">
                              <button onclick="viewClientHistory('<?php echo htmlspecialchars($reservation['phone']); ?>', '<?php echo htmlspecialchars($reservation['email']); ?>')">View</button>
                              <a href="/Admin/Reservations/changeStatus?id=<?= $reservation['id']; ?>&status=completed">Mark Completed</a>
                          </div>
                          <?php
                          break;

                      case 'completed':
                      case 'declined':
                      case 'cancelled':
                          ?>
                          <div class="row-actions">
                              <button onclick="viewClientHistory('<?php echo htmlspecialchars($reservation['phone']); ?>', '<?php echo htmlspecialchars($reservation['email']); ?>')">View</button>
                          </div>
                          <?php
                          break;
                      case 'proposed':
                        ?>
                        <div class="row-actions">
                            <button onclick="viewClientHistory('<?php echo htmlspecialchars($reservation['phone']); ?>', '<?php echo htmlspecialchars($reservation['email']); ?>')">View</button>
                            <a href="/Admin/Reservations/changeStatus?id=<?= $reservation['id']; ?>&status=confirmed">Confirm</a>
                            <a href="/Admin/Reservations/changeStatus?id=<?= $reservation['id']; ?>&status=declined">Decline</a>
                        </div>
                        <?php
                        break;

                      default:
                          ?>
                          <div class="row-actions">
                              <button onclick="viewClientHistory('<?php echo htmlspecialchars($reservation['phone']); ?>', '<?php echo htmlspecialchars($reservation['email']); ?>')">View</button>
                          </div>
                          <?php
                          break;
                        }
                  ?>
            </td>
          </tr>

          <?php endforeach; endif;?>

        </tbody>
      </table>
    </div>

  </main>

</div>

<!-- MODAL ROOT — filled by JS functions below -->
<div id="modalRoot"></div>

<script>

function openModal(html){
  const root = document.getElementById('modalRoot');
  root.innerHTML = html;
  root.classList.add('show');
}

function closeModal(){
  const root = document.getElementById('modalRoot');
  root.classList.remove('show');
  root.innerHTML = '';
}

// 3.1 / 3.2 — Client History popup (new client vs returning client)
function viewNewClient(data){
  const client = data.client;
  const reservation = data.historyReservations[0]; 
  return `
<div class="modal-box">
  <button class="modal-close" onclick="closeModal()">&times;</button>
  <h2>Client History</h2>

  <div class="modal-line"><strong>${client.first_name} ${client.last_name}</strong></div>
  <div class="modal-line">${client.email} | ${client.phone}</div>

  <div class="new-client-badge">New Client</div>

  <hr class="modal-divider">

  <p class="modal-note">This is their first reservation with you.</p>

  <hr class="modal-divider">

  <div class="modal-line"><strong>Current Reservation</strong></div>
  <div class="modal-line">${reservation.date} — ${reservation.time_slot} — ${reservation.guests} guests</div>
  <div class="modal-line">Status: <span class="status-pill status-${reservation.status}">${reservation.status}</span></div>
  <div class="modal-line modal-note-field">Note: ${reservation.note}</div>

  <div class="modal-actions">
    <button class="btn-outline-sm" onclick="closeModal()">Close</button>
  </div>
</div>
`;
}


function viewReturningClient(data){
  const client = data.client;
  const historyCount = data.historyCount;
  const reservations = data.historyReservations;


  const lastVisit = reservations.length > 0 ? reservations[reservations.length-1] : null;

  const rows = reservations.map(r => `
    <tr>
      <td>${r.date}</td>
      <td>${r.time_slot ? r.time_slot.slice(0, 5) : ''}</td>
      <td><span class="status-pill status-${r.status}">${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</span></td>
    </tr>
  `).join('');

  return `
<div class="modal-box">
  <button class="modal-close" onclick="closeModal()">&times;</button>
  <h2>Client History</h2>

  <div class="modal-line"><strong>${client.first_name} ${client.last_name}</strong></div>
  <div class="modal-line">${client.email} | ${client.phone}</div>

  <hr class="modal-divider">

  <div class="modal-line"><strong>History (matched by email/phone)</strong></div>

  <div class="modal-stat-row"><span class="k">Confirmed</span><span class="v">${historyCount.confirmed}</span></div>
  <div class="modal-stat-row"><span class="k">Completed</span><span class="v">${historyCount.completed}</span></div>
  <div class="modal-stat-row"><span class="k">Cancelled</span><span class="v">${historyCount.cancelled}</span></div>
  <div class="modal-stat-row"><span class="k">Declined</span><span class="v">${historyCount.declined}</span></div>
  <div class="modal-stat-row"><span class="k">Overdue</span><span class="v">${historyCount.overdue}</span></div>

  <hr class="modal-divider">

  <div class="modal-line">Last visit: <strong>${lastVisit ? lastVisit.date : '—'}</strong></div>

  ${lastVisit && lastVisit.note ? `
    <div class="modal-line modal-note-field">Note: ${lastVisit.note}</div>
  ` : ''}

  <hr class="modal-divider">

  <div class="modal-line"><strong>View full reservation list</strong></div>

  <table class="history-table">
    <thead><tr><th>Date</th><th>Time</th><th>Status</th></tr></thead>
    <tbody>${rows}</tbody>
  </table>

  <div class="modal-actions">
    <button class="btn-outline-sm" onclick="closeModal()">Close</button>
  </div>
</div>
`;
}

async function viewClientHistory(phone,email){
  const res = await fetch(`/Admin/Reservations/viewReservation?phone=${phone}&email=${email}`);
  const data = await res.json();

  if(data.historyReservations.length==1){
      openModal(viewNewClient(data)); 
   }else{
      openModal(viewReturningClient(data)); 
   }
}

/* ============================================================
   Client-side reservations table rendering — reused for
   filtering (on select change) and periodic refresh (polling).
   ============================================================ */

let allReservations = []; // cache of the latest full dataset from the server
allReservations = <?php echo json_encode($reservations); ?>;

function escapeHtml(str){
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

/* ---------- Row-level helpers (mirror the PHP logic) ---------- */

function computeIsOverdue(reservation){
  if (reservation.status !== 'seated') return false;

  const today = new Date().toISOString().slice(0, 10);
  const now = new Date();
  const currentTime = now.toTimeString().slice(0, 8);

  if (reservation.date < today) return true;
  if (reservation.date === today && currentTime > reservation.expected_end_time) return true;
  return false;
}

function renderStatusPill(reservation){
  if (computeIsOverdue(reservation)){
    return `<span class="status-pill status-overdue">Seated · Overdue</span>`;
  }
  const status = reservation.status;
  const label = status.charAt(0).toUpperCase() + status.slice(1);
  return `<span class="status-pill status-${escapeHtml(status)}">${escapeHtml(label)}</span>`;
}


function formatLocalDateTime(date){
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  const h = String(date.getHours()).padStart(2, '0');
  const min = String(date.getMinutes()).padStart(2, '0');
  const s = String(date.getSeconds()).padStart(2, '0');
  return `${y}-${m}-${d} ${h}:${min}:${s}`;
}


function renderExpectedEndCell(reservation){
  const completedAt = reservation.completed_at;
  if (!completedAt){
    return `<span style="color:var(--muted);">—</span>`;
  }

  const expectedEnd = new Date(reservation.date + ' ' + reservation.expected_end_time);
  const actualEnd = new Date(completedAt);
  const overdueSeconds = Math.floor((actualEnd - expectedEnd) / 1000);

  const formattedCompletedAt = formatLocalDateTime(actualEnd);

  let html = `<div>${escapeHtml(formattedCompletedAt)}</div>`;

  if (overdueSeconds > 0 && reservation.status == 'seated'){
    const days = Math.floor(overdueSeconds / 86400);
    const hours = String(Math.floor((overdueSeconds % 86400) / 3600)).padStart(2, '0');
    const minutes = String(Math.floor((overdueSeconds % 3600) / 60)).padStart(2, '0');
    const seconds = String(overdueSeconds % 60).padStart(2, '0');

    const overdueLabel = (days > 0 ? `${days}d ` : '') + `${hours}:${minutes}:${seconds}`;

    html += `<div style="color:#A33A3A; font-size:11.5px; font-weight:600; margin-top:2px;">Overdue: ${escapeHtml(overdueLabel)}</div>`;
  }

  return html;
}

function renderRowActions(reservation){
  const phone = escapeHtml(reservation.phone);
  const email = escapeHtml(reservation.email);
  const id = reservation.id;

  switch (reservation.status){
    case 'pending':
      return `
        <div class="row-actions">
          <button onclick="viewClientHistory('${phone}', '${email}')">View</button>
          <a href="/Admin/Reservations/changeStatus?id=${id}&status=declined">Decline</a>
          <button onclick="handleConfirmation('${id}')">Confirm</button>
          <span class="muted-note">Awaiting customer</span>
        </div>`;

    case 'confirmed':
      return `
        <div class="row-actions">
          <button onclick="viewClientHistory('${phone}', '${email}')">View</button>
          <a href="/Admin/Reservations/changeStatus?id=${id}&status=seated">Mark seated</a>
          <a href="/Admin/Reservations/changeStatus?id=${id}&status=cancelled">Cancel</a>
        </div>`;

    case 'seated':
      return `
        <div class="row-actions">
          <button onclick="viewClientHistory('${phone}', '${email}')">View</button>
          <a href="/Admin/Reservations/changeStatus?id=${id}&status=completed">Mark Completed</a>
        </div>`;
    case 'proposed':
    return `
      <div class="row-actions">
        <button onclick="viewClientHistory('${phone}', '${email}')">View</button>
        <a href="/Admin/Reservations/changeStatus?id=${id}&status=confirmed">Confirm</a>
        <a href="/Admin/Reservations/changeStatus?id=${id}&status=declined">Decline</a>
      </div>`;

    case 'completed':
    case 'declined':
    case 'cancelled':
    default:
      return `
        <div class="row-actions">
          <button onclick="viewClientHistory('${phone}', '${email}')">View</button>
        </div>`;
  }
}

/* ---------- Main render function ---------- */

function renderReservations(reservations, mode = 'refresh', filter = {}){
  const tbody = document.getElementById('reservationsTableBody');

  let list = reservations;

  if (mode === 'filter'){
    list = reservations.filter(r => {
      if (filter.status && r.status !== filter.status) return false;
      if (filter.table && r.table_number != filter.table) return false;

      if (filter.date === 'today'){
        const today = new Date().toISOString().slice(0, 10);
        if (r.date !== today) return false;
      } else if (filter.date === 'tomorrow'){
        const tomorrow = new Date(Date.now() + 86400000).toISOString().slice(0, 10);
        if (r.date !== tomorrow) return false;
     } else if (filter.date === 'week'){
    const today = new Date();
    const weekAhead = new Date(Date.now() + 7 * 86400000);
    const rDate = new Date(r.date);
    if (rDate < today || rDate > weekAhead) return false;
}

      return true;
    });
  }

  if (!list || list.length === 0){
    tbody.innerHTML = `<tr><td colspan="11" style="text-align:center; color:var(--muted); padding:30px;">No Reservations Available</td></tr>`;
    return;
  }

  tbody.innerHTML = list.map(r => `
    <tr>
      <td>${escapeHtml(r.table_number)}</td>
      <td>${escapeHtml(r.first_name)}</td>
      <td>${escapeHtml(r.last_name)}</td>
      <td>${escapeHtml(r.phone)}</td>
      <td><a class="email-link" href="mailto:${escapeHtml(r.email)}">${escapeHtml(r.email)}</a></td>
      <td>${escapeHtml(r.date)}</td>
      <td>${escapeHtml(r.time_slot)}</td>
      <td>${escapeHtml(r.expected_end_time)}</td>
      <td>${renderExpectedEndCell(r)}</td>
      <td>${escapeHtml(r.guests)}</td>
      <td>${renderStatusPill(r)}</td>
      <td>${renderRowActions(r)}</td>
    </tr>
  `).join('');
}

/* ---------- Filtering (reads current select values) ---------- */

function applyFilters(){
  const filter = {
    status: document.getElementById('filterStatus').value,
    date: document.getElementById('filterDate').value,
    table: document.getElementById('filterTable').value,
  };
  renderReservations(allReservations, 'filter', filter);
}

['filterStatus', 'filterDate', 'filterTable'].forEach(id => {
  document.getElementById(id).addEventListener('change', applyFilters);
});

/* ---------- Polling — refresh every 15 seconds ---------- */

async function refreshReservations(){
  try {
    const res = await fetch('/Admin/Reservations/?format=json');
    if (!res.ok) throw new Error('Request failed: ' + res.status);
    const data = await res.json();

    allReservations = data.reservations;
    applyFilters(); // re-apply whatever filter is currently selected, using the fresh data

  } catch (err){
    console.error('Reservations refresh failed:', err);
  }
}

setInterval(refreshReservations, 15000);


// 3.3 — Confirm click success popup
function showConfirmSuccessPopup(reservation){
  const dateFormatted = reservation.date; // adjust formatting if needed
  const timeFormatted = reservation.time_slot ? reservation.time_slot.slice(0, 5) : '';

  const html = `
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>Reservation Confirmed</h2>

      <div class="modal-line"><strong>${escapeHtml(reservation.first_name)} ${escapeHtml(reservation.last_name)}</strong> — ${escapeHtml(dateFormatted)} — ${escapeHtml(timeFormatted)}</div>
      <div class="modal-line">${escapeHtml(reservation.guests)} guests, Table ${escapeHtml(reservation.table_number)}</div>

      <hr class="modal-divider">

      <p class="modal-note">A confirmation email has been sent to ${escapeHtml(reservation.email)}.</p>

      <div class="modal-actions">
        <button class="btn-solid-sm" onclick="closeModal()">OK</button>
      </div>
    </div>
  `;
  openModal(html);
}

// 3.4 — Confirm click, slot full popup
function showSlotFullPopup(reservation){
  const dateFormatted = reservation.date;
  const timeFormatted = reservation.time_slot ? reservation.time_slot.slice(0, 5) : '';

  const html = `
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>Time Slot Full</h2>

      <p class="modal-note">No tables available for ${escapeHtml(dateFormatted)} — ${escapeHtml(timeFormatted)}.</p>

      <hr class="modal-divider">

      <p class="modal-note">This reservation was not confirmed. No email has been sent yet.</p>

      <div class="modal-actions">
        <button class="btn-solid-sm" onclick="proposeNewTime(${reservation.id})">Propose New Time</button>
         <a class="btn-outline-sm" href="/Admin/Reservations/changeStatus?id=${reservation.id}&status=declined">Decline Request</a>
      </div>
    </div>
  `;
  openModal(html);

}

async function handleConfirmation(id){
 try {
    const res = await fetch(`/Admin/Reservations/confirmReservation/?id=${id}`);
    if (!res.ok) throw new Error('Request failed: ' + res.status);
    const data = await res.json();
    const table_number = data.table_number;
    const reservation = data.reservation;

    if(table_number){
      showConfirmSuccessPopup(reservation);
    
    }
    else{
      showSlotFullPopup(reservation);
    }

    refreshReservations();
      
    
 }
 catch (err){
    console.error('Confirmation Failed', err);
}

    }

async function proposeNewTime(id){
  const res = await fetch(`/Admin/Reservations/proposeNewTime?id=${id}`);
  const data = await res.json();

  if (data.time_slots.length === 0){
    openModal(`
      <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <h2>No Time Slots Available</h2>
        <p class="modal-note">No time slots are available today.</p>
        <div class="modal-actions">
          <button class="btn-outline-sm" onclick="closeModal()">Close</button>
        </div>
      </div>
    `);
    return;
  }

  const slotButtons = data.time_slots.map(slot => {
    const label = slot.slice(0, 5);
    return `<button class="btn-outline-sm" onclick="selectProposedTime(${id}, '${slot}')">${label}</button>`;
  }).join(' ');

  openModal(`
    <div class="modal-box" id="proposeModalBox">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>Propose New Time</h2>
      <p class="modal-note">Select a time slot to check availability:</p>
      <div class="time-slot-grid" id="timeSlotGrid">${slotButtons}</div>
      <div id="tableCheckResult"></div>
      <div class="modal-actions">
        <button class="btn-outline-sm" onclick="closeModal()">Close</button>
      </div>
    </div>
  `);
}

async function selectProposedTime(id, time){
  const resultDiv = document.getElementById('tableCheckResult');
  resultDiv.innerHTML = `<p class="modal-note">Checking availability...</p>`;

  const res = await fetch(`/Admin/Reservations/getAvailableTableForTime?id=${id}&time=${encodeURIComponent(time)}`);
  const data = await res.json();

  if (!data.table_number){
    resultDiv.innerHTML = `<p class="modal-note" style="color:#A33A3A;">You should pick another time — no table is available for this one.</p>`;

    // Only the close (x) button remains actionable — hide any confirm/send action if present
    const actions = document.querySelector('#proposeModalBox .modal-actions');
    actions.innerHTML = `<button class="btn-outline-sm" onclick="closeModal()">Close</button>`;
    return;
  }

  resultDiv.innerHTML = `<p class="modal-note">Table ${data.table_number} available at ${time.slice(0, 5)}.</p>`;

  const actions = document.querySelector('#proposeModalBox .modal-actions');
  actions.innerHTML = `
    <button class="btn-solid-sm" onclick="sendProposal(${id}, '${time}', ${data.table_number})">Send Proposal</button>
    <button class="btn-outline-sm" onclick="closeModal()">Close</button>
  `;
}

async function sendProposal(id, time, tableNumber){
  const resultDiv = document.getElementById('tableCheckResult');
  const actions = document.querySelector('#proposeModalBox .modal-actions');

  actions.innerHTML = `<button class="btn-outline-sm" disabled>Sending...</button>`;

  try {
    const res = await fetch('/Admin/redirect.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        proposeReservationTime: '1',
        id: id,
        time_slot: time,
        table_number: tableNumber,
      }),
    });

    if (!res.ok) throw new Error('Request failed: ' + res.status);
    const data = await res.json();

    if (!data.success){
      resultDiv.innerHTML = `<p class="modal-note" style="color:#A33A3A;">${escapeHtml(data.message || 'That table is no longer available.')}</p>`;
      actions.innerHTML = `<button class="btn-outline-sm" onclick="closeModal()">Close</button>`;
      return;
    }

    resultDiv.innerHTML = `<p class="modal-note" style="color:#4C7A44;">Proposal sent successfully.</p>`;
    actions.innerHTML = `<button class="btn-solid-sm" onclick="closeModal()">OK</button>`;

    refreshReservations();

  } catch (err){
    console.error('Failed to send proposal:', err);
    resultDiv.innerHTML = `<p class="modal-note" style="color:#A33A3A;">Something went wrong. Please try again.</p>`;
    actions.innerHTML = `<button class="btn-outline-sm" onclick="closeModal()">Close</button>`;
  }
}


</script>

<?php
  }

function displayReservationsView($mark,$reservations,$tables){
  $this->header($mark);
  $this->content($mark,$reservations,$tables);

  $this->scirptSideBar();
}
}?>