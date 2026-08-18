<?php
require_once __DIR__ . "/GlobalView.php";

class ParametersView extends GlobalView {

    function content($mark, $tables, $slotSettings, $durationSettings, $openingHours){
        ?>
<body>

<div class="admin-shell">
    <?php $this->sideBar($mark); ?>

    <main class="admin-main">

        <div class="page-head-row">
            <h1>Parameters</h1>
        </div>

       <!-- TABS -->
<div class="param-tabs">
    <button class="param-tab active" data-tab="tables" onclick="switchTab('tables')">Tables</button>
    <button class="param-tab" data-tab="capacity" onclick="switchTab('capacity')">Capacity / Duration</button>
    <button class="param-tab" data-tab="hours" onclick="switchTab('hours')">Hours</button>
</div>

<!-- TAB 1 — TABLES -->
<div class="param-panel" id="tab-tables">
    <form method="post" action="/Admin/redirect.php">
        <input type="hidden" name="saveTables" value="1">

        <div class="param-section">
            <div class="param-section-title">Number of Tables: <?php echo count($tables); ?></div>
        </div>

        <div class="param-section">
            <div class="param-section-title">Table Sizes</div>

            <div class="param-grid">
                <?php foreach ($tables as $table): ?>
                    <div class="param-grid-item">
                        <label>Table <?php echo htmlspecialchars($table['table_number']); ?></label>
                        <select name="tables[<?php echo $table['id']; ?>]">
                            <?php foreach ([2, 4, 6, 8] as $seatOption): ?>
                                <option value="<?php echo $seatOption; ?>" <?php echo $table['seats'] == $seatOption ? 'selected' : ''; ?>>
                                    <?php echo $seatOption; ?> Seats
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>

                <div class="param-grid-item param-grid-item-add">
                    <label><h3>+ Add Table</h3></label>
                    <select name="newTable[seats]">
                        <option value="">— Seats —</option>
                        <option value="2">2 Seats</option>
                        <option value="4">4 Seats</option>
                        <option value="6">6 Seats</option>
                        <option value="8">8 Seats</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-solid">Save Changes</button>
    </form>
</div>

<!-- TAB 2 — CAPACITY / DURATION & BUFFER -->
<div class="param-panel" id="tab-capacity" style="display:none;">
    <form method="post" action="/Admin/redirect.php">
        <input type="hidden" name="saveCapacityDuration" value="1">
        <input type="hidden" name="slot[id]" value="<?php echo $slotSettings['id']; ?>">

        <div class="param-section">
            <div class="param-section-title">Booking Slots</div>
            <div class="param-grid">
                <div class="param-grid-item">
                    <label>Slot Interval (min)</label>
                    <select name="slot[slot_interval]">
                        <?php foreach (['00:15:00' => 15, '00:30:00' => 30, '00:45:00' => 45, '01:00:00' => 60] as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $slotSettings['slot_interval'] === $val ? 'selected' : ''; ?>><?php echo $label; ?> min</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="param-grid-item">
                    <label>Max Reservations per Slot</label>
                    <input type="number" name="slot[max_reservations]" value="<?php echo htmlspecialchars($slotSettings['max_reservations']); ?>" min="1">
                </div>
                <div class="param-grid-item">
                    <label>Buffer Time (min)</label>
                    <input type="number" name="slot[buffer_minutes]" value="<?php echo htmlspecialchars($slotSettings['buffer_minutes']); ?>" min="0">
                </div>
            </div>
            <p class="param-hint">Highlighted = high overdue rate (pulled from Report data). Buffer = gap after a table's expected end time before it can be rebooked.</p>
        </div>

        <div class="param-section">
            <div class="param-section-title">Expected Dining Duration</div>
            <div class="param-grid">
                <?php foreach ($durationSettings as $d): ?>
                    <div class="param-grid-item">
                        <label>Party <?php echo htmlspecialchars($d['party_size']); ?> guests</label>
                        <input type="number" name="durations[<?php echo $d['id']; ?>]" value="<?php echo htmlspecialchars($d['expected_duration_minutes']); ?>" min="1">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn-solid">Save Changes</button>
    </form>
</div>

<!-- TAB 3 — OPENING HOURS -->
<div class="param-panel" id="tab-hours" style="display:none;">
    <form method="post" action="/Admin/redirect.php">
        <input type="hidden" name="saveHours" value="1">
      <div class="hours-table-wrap">
        <table class="hours-table">
            <thead>
                <tr><th>Day</th><th>Status</th><th>Open</th><th>Close</th></tr>
            </thead>
            <tbody>
                <?php foreach ($openingHours as $day): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($day['day_of_week']); ?></td>
                        <td>
                            <select name="hours[<?php echo $day['id']; ?>][is_closed]">
                                <option value="0" <?php echo !$day['is_closed'] ? 'selected' : ''; ?>>Open</option>
                                <option value="1" <?php echo $day['is_closed'] ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </td>
                        <td><input type="time" name="hours[<?php echo $day['id']; ?>][open_time]" value="<?php echo substr($day['open_time'], 0, 5); ?>"></td>
                        <td><input type="time" name="hours[<?php echo $day['id']; ?>][close_time]" value="<?php echo substr($day['close_time'], 0, 5); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
        <button type="submit" class="btn-solid">Save Changes</button>
    </form>
</div>

    </main>
</div>

<script>
function switchTab(tab){
    document.querySelectorAll('.param-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.param-tab').forEach(t => t.classList.remove('active'));

    document.getElementById('tab-' + tab).style.display = 'block';
    document.querySelector(`.param-tab[data-tab="${tab}"]`).classList.add('active');
}

function copyMondayHours(){
    const rows = document.querySelectorAll('.hours-table tbody tr');
    const mondayRow = rows[0];
    const mondayStatus = mondayRow.querySelector('select').value;
    const mondayOpen = mondayRow.querySelectorAll('input[type="time"]')[0].value;
    const mondayClose = mondayRow.querySelectorAll('input[type="time"]')[1].value;

    rows.forEach(row => {
        row.querySelector('select').value = mondayStatus;
        const times = row.querySelectorAll('input[type="time"]');
        times[0].value = mondayOpen;
        times[1].value = mondayClose;
    });
}
</script>

</body>
        <?php
    }

    function displayParametersView($mark, $tables, $slotSettings, $durationSettings, $openingHours){
        $this->header($mark);
        $this->content($mark, $tables, $slotSettings, $durationSettings, $openingHours);
        $this->scirptSideBar();
    }
}