<?php 
require_once __DIR__ . "/ClobalView.php";

Class ReservationView extends GlobalView{

function timeToMinutes($timeStr){
    // "00:30:00" -> 30
    list($h, $m, $s) = array_map('intval', explode(':', $timeStr));
    return ($h * 60) + $m;
}

function content($opening_hours, $slot){
  ?>

  <!-- PAGE BANNER -->
  <section class="page-banner">
    <div class="crumb"><a href="/Client/">Home</a> / Reservation</div>
    <h1>Reserve <span class="accent">Your Table</span></h1>
    <p>Booking takes less than a minute — we'll hold your table for 15 minutes past the reserved time.</p>
  </section>

  <!-- RESERVATION FORM -->
  <section class="reserve-section">
    <div class="wrap">

      <form class="reserve-card" id="reserveForm" method="post" action="/Client/redirect.php">
        <h2><span class="accent">Booking</span> Details</h2>

        <div class="form-grid-2">
          <div class="form-row">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="John" required>
          </div>
          <div class="form-row">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" placeholder="Doe" required>
          </div>
        </div>

        <div class="form-row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="john@example.com" required>
        </div>

        <div class="form-row">
          <label for="phone">Phone</label>
          <input type="tel" id="phone" name="phone" placeholder="+1 234 567 8900" required>
        </div>

        <div class="form-grid-3">
          <div class="form-row">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
          </div>
          <div class="form-row">
            <label for="time_slot">Time</label>
            <select id="time_slot" name="time_slot" required>
              <option value="">Pick a date first</option>
            </select>
          </div>
          <div class="form-row">
            <label for="guests">Guests</label>
            <select id="guests" name="guests" required>
              <option value="1">1</option>
              <option value="2" selected>2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6+">6+</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <label for="note">Note <span style="text-transform:none; font-weight:400;"  >(optional)</span></label>
          <textarea id="note" name="note" placeholder="Allergies, special occasion, seating preference..." maxlength="100"></textarea>
        </div>

        <button type="submit" class="form-submit" name="reserveForm" value="1">Confirm Reservation</button>
        <div class="form-hint">You'll receive a confirmation by email once submitted.</div>
      </form>

    </div>
  </section>

  <script>
  const openingHours = <?php echo json_encode($opening_hours); ?>;
  const slotInterval  = <?php echo $this->timeToMinutes($slot['slot_interval']); ?>;

  const dateInput  = document.getElementById('date');
  const timeSelect = document.getElementById('time_slot');

  if (slotInterval <= 0){
    timeSelect.innerHTML = '<option value="">Time slots unavailable</option>';
  } else {

    const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    function toMinutes(hhmm){
      const [h, m] = hhmm.split(':').map(Number);
      return h * 60 + m;
    }
    function toHHMM(mins){
      const h = String(Math.floor(mins / 60)).padStart(2, '0');
      const m = String(mins % 60).padStart(2, '0');
      return `${h}:${m}`;
    }

    dateInput.addEventListener('change', function(){
      const value = this.value;
      timeSelect.innerHTML = '';

      if (!value){
        timeSelect.innerHTML = '<option value="">Pick a date first</option>';
        return;
      }

      const jsDay = new Date(value + 'T00:00:00').getDay();
      const dayName = dayNames[jsDay];

      const todayHours = openingHours.find(oh => oh.day_of_week === dayName);

      if (!todayHours || Number(todayHours.is_closed) === 1){
        timeSelect.innerHTML = '<option value="">Closed on this day</option>';
        return;
      }

      let current = toMinutes(todayHours.open_time);
      const close = toMinutes(todayHours.close_time);

      while (current < close){
        const hhmm = toHHMM(current);
        const opt = document.createElement('option');
        opt.value = hhmm;
        opt.textContent = hhmm;
        timeSelect.appendChild(opt);
        current += slotInterval;
      }

      if (!timeSelect.children.length){
        timeSelect.innerHTML = '<option value="">No slots available</option>';
      }
    });

  }
  </script>
  <?php 
}

function confirmedReservation($reservation){
  $formattedDate = date('F j, Y', strtotime($reservation['date']));
  ?>

  <!-- PAGE BANNER -->
  <section class="page-banner">
    <div class="crumb"><a href="/Client/">Home</a> / Reservation</div>
    <h1>Reservation <span class="accent">Confirmed</span></h1>
  </section>

  <section class="reserve-section">
    <div class="wrap">
      <div class="confirm-card show" id="confirmCard">
        <div class="confirm-icon">✓</div>
        <h2>Reservation Confirmed</h2>
        <div class="sub">We look forward to hosting you.</div>
        <div class="confirm-details">
          <div class="confirm-row">
            <span class="k">Name</span>
            <span class="v"><?php echo htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']); ?></span>
          </div>
          <div class="confirm-row">
            <span class="k">Date</span>
            <span class="v"><?php echo $formattedDate . ' – ' . htmlspecialchars($reservation['time_slot']); ?></span>
          </div>
          <div class="confirm-row">
            <span class="k">Guests</span>
            <span class="v"><?php echo htmlspecialchars($reservation['guests']); ?></span>
          </div>
        </div>
        <div class="confirm-actions">
          <a href="/Client/" class="btn-outline">Back to Home</a>
          <a href="/Client/Menu/" class="btn-solid">View Menu</a>
        </div>
      </div>
    </div>
  </section>

  <?php
}

function PendingReservation(){
  ?>

  <!-- PAGE BANNER -->
  <section class="page-banner">
    <div class="crumb"><a href="/Client/">Home</a> / Reservation</div>
    <h1>Reservation <span class="accent">Unavailable</span></h1>
  </section>

  <section class="reserve-section">
    <div class="wrap">
      <div class="confirm-card show" id="failedCard">
        <div class="confirm-icon">✕</div>
        <h2>No Table Available</h2>
        <div class="sub">
          <?php echo  'This time is currently full. Your    
  request has been <strong>received</strong> and is     
  <strong>pending</strong> confirmation.'?><br><br><?php echo                               
  'We will <strong>email</strong> you as soon as it is      
  confirmed, or offer you an           
  alternative time.'; ?>
        </div>
        <div class="confirm-actions">
          <a href="/Client/" class="btn-outline">Back to Home</a>
          <a href="/Client/Menu/" class="btn-solid">View Menu</a>
        </div>
      </div>
    </div>
  </section>

  <?php
}

function displayReservationView($contact_details, $opening_hours_org, $opening_hours, $slot){
  $this->header($contact_details['mark']);
  $this->topBar($contact_details);
  $this->nav($contact_details['mark']);
  $this->content($opening_hours, $slot);
  $this->footer($contact_details, $opening_hours_org);
}

function displayConfirmedReservationView($contact_details, $opening_hours_org, $reservation){
  $this->header($contact_details['mark']);
  $this->topBar($contact_details);
  $this->nav($contact_details['mark']);
  $this->confirmedReservation($reservation);
  $this->footer($contact_details, $opening_hours_org);
}

function displayPendingReservationView($contact_details, $opening_hours_org, $reason = ''){
  $this->header($contact_details['mark']);
  $this->topBar($contact_details);
  $this->nav($contact_details['mark']);
  $this->pendingReservation();
  $this->footer($contact_details, $opening_hours_org);
}

}
?>