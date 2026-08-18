<?php
require_once __DIR__ . "/GlobalView.php";

class ReportView extends GlobalView {

    function content($mark, $from, $to, $summary, $daily, $hourly){
        $total = (int) $summary['total'];
        $confirmedPct = $total > 0 ? round(($summary['confirmed'] / $total) * 100) : 0;
        $declinedPct = $total > 0 ? round(($summary['declined'] / $total) * 100) : 0;
        $completedPct= $total > 0 ? round(($summary['completed'] / $total) * 100) : 0;
        ?>
<body>

<div class="admin-shell">
    <?php $this->sideBar($mark); ?>

    <main class="admin-main">

        <div class="page-head-row">
            <h1>Reservation Report</h1>
        </div>

        <form method="get" action="/Admin/Report/" class="report-filter-bar">
            <div class="param-grid-item">
                <label>From</label>
                <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>">
            </div>
            <div class="param-grid-item">
                <label>To</label>
                <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>">
            </div>
            <button type="submit" class="btn-solid">Show Results</button>
        </form>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="label">Total</div>
                <div class="value" data-stat="total"><?php echo $total; ?></div>
                <div class="sub">reservations</div>
            </div>
            <div class="stat-card">
                <div class="label">Confirmed</div>
                <div class="value" data-stat="confirmed"><?php echo (int) $summary['confirmed']; ?></div>
                <div class="sub" data-stat="confirmed-pct"><?php echo $confirmedPct; ?>% of total</div>
            </div>

            <div class="stat-card">
                <div class="label">Completed</div>
                <div class="value" data-stat="completed"><?php echo (int) $summary['completed']; ?></div>
                <div class="sub" data-stat="completed-pct"><?php echo $completedPct; ?>% of total</div>
            </div>

            <div class="stat-card">
                <div class="label">Declined</div>
                <div class="value warn" data-stat="declined"><?php echo (int) $summary['declined']; ?></div>
                <div class="sub" data-stat="declined-pct"><?php echo $declinedPct; ?>% of total</div>
            </div>
            <div class="stat-card">
                <div class="label">Overdue</div>
                <div class="value alert" data-stat="overdue"><?php echo (int) $summary['overdue']; ?></div>
                <div class="sub">seated past end</div>
            </div>
        </div>

        <div class="report-grid">

            <!-- LEFT: DAILY TREND CHART -->
            <div class="panel">
                <div class="panel-head">
                    <h2>Reservations by Day</h2>
                </div>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            <!-- RIGHT: HOURLY BREAKDOWN TABLE -->
            <div class="panel">
              <div class="panel-head">
                <h2>Breakdown by Hour</h2>
              </div>
            <div class="hourly-table-wrap">
               <table class="hourly-table">
                    <thead>
                        <tr><th>Hour</th><th>Conf</th><th>Comp</th><th>Over</th><th>Rate_Over</th></tr>
                    </thead>
                    <tbody>
                       <?php foreach ($hourly as $row):
                            $hourStart = str_pad($row['hour'], 2, '0', STR_PAD_LEFT) . ':00';
                            $hourEnd = str_pad(($row['hour'] + 1) % 24, 2, '0', STR_PAD_LEFT) . ':00';
                            $rowDenominator = $row['completed'] + $row['seated'];
                            $rate = $rowDenominator > 0 ? round(($row['overdue'] / $rowDenominator) * 100) : null;
                            $isHigh = $rate !== null && $rate > 30;
                        ?>
                            <tr class="<?php echo $isHigh ? 'hourly-row-flagged' : ''; ?>">
                                <td><?php echo $hourStart . '-' . $hourEnd; ?></td>
                                <td><?php echo $row['confirmed']; ?></td>
                                <td><?php echo $row['completed']; ?></td>
                                <td><?php echo $row['overdue']; ?></td>
                                <td><?php echo $rate !== null ? $rate . '%' : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </div>

        </div>

    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
  const dailyData = <?php echo json_encode($daily); ?>;

  let dailyChart;

  function initChart(data){
    if (dailyChart){
      dailyChart.destroy();
    }

    const ctx = document.getElementById('dailyChart');
    dailyChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(d => d.date.slice(5)),
        datasets: [
         { label: 'Confirmed', data: data.map(d => d.confirmed), borderColor: '#4C7A44', backgroundColor: 'rgba(76,122,68,0.1)', tension: 0.3, pointRadius: 4 },
{ label: 'Completed', data: data.map(d => d.completed), borderColor: '#3E5C82', backgroundColor: 'rgba(62,92,130,0.1)', tension: 0.3, pointRadius: 4 },
{ label: 'Overdue', data: data.map(d => d.overdue), borderColor: '#B9860F', backgroundColor: 'rgba(185,134,15,0.1)', tension: 0.3, pointRadius: 4 },
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
         maintainAspectRatio: false,
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });

    console.log(dailyData.map(d => d.overdue));
  }

  function updateChart(data){
    dailyChart.data.labels = data.map(d => d.date.slice(5));
    dailyChart.data.datasets[0].data = data.map(d => d.confirmed);
    dailyChart.data.datasets[1].data = data.map(d => d.completed);
    dailyChart.data.datasets[2].data = data.map(d => d.overdue);
    dailyChart.update();
  }

  function renderSummary(summary){
    const total = parseInt(summary.total) || 0;
    const confirmedPct = total > 0 ? Math.round((summary.confirmed / total) * 100) : 0;
    const completedPct = total > 0 ? Math.round((summary.completed / total) * 100) : 0;

    document.querySelector('[data-stat="total"]').textContent = total;
    document.querySelector('[data-stat="confirmed"]').textContent = summary.confirmed;
    document.querySelector('[data-stat="confirmed-pct"]').textContent = confirmedPct + '% of total';
    document.querySelector('[data-stat="completed"]').textContent = summary.completed;
    document.querySelector('[data-stat="completed-pct"]').textContent = completedPct + '% of total';
    document.querySelector('[data-stat="overdue"]').textContent = summary.overdue;
  }

function renderHourlyTable(hourly, total){
  const tbody = document.querySelector('.hourly-table tbody');

  tbody.innerHTML = hourly.map(row => {
    const hourStart = String(row.hour).padStart(2, '0') + ':00';
    const hourEnd = String((row.hour + 1) % 24).padStart(2, '0') + ':00';
    const rowDenominator = parseInt(row.completed) + parseInt(row.seated);
    const rate = rowDenominator > 0 ? Math.round((row.overdue / rowDenominator) * 100) : null;
    const isHigh = rate !== null && rate > 30;

    return `
      <tr class="${isHigh ? 'hourly-row-flagged' : ''}">
        <td>${hourStart}-${hourEnd}</td>
        <td>${row.confirmed}</td>
        <td>${row.completed}</td>
        <td>${row.overdue}</td>
        <td>${rate !== null ? rate + '%' : '—'}</td>
      </tr>
    `;
  }).join('');
}

  async function refreshReport(){
    try {
      const params = new URLSearchParams(window.location.search);
      params.set('format', 'json');

      const res = await fetch(`/Admin/Report/?${params.toString()}`);
      if (!res.ok) throw new Error('Request failed: ' + res.status);
      const data = await res.json();

      renderSummary(data.summary);
      updateChart(data.daily);
      renderHourlyTable(data.hourly, data.summary.total);

    } catch (err){
      console.error('Report refresh failed:', err);
    }
  }

  initChart(dailyData);
  setInterval(refreshReport, 15000);
</script>

</body>
        <?php
    }

    function displayReportView($mark, $from, $to, $summary, $daily, $hourly){
        $this->header($mark);
        $this->content($mark, $from, $to, $summary, $daily, $hourly);
        $this->scirptSideBar();
    }
}