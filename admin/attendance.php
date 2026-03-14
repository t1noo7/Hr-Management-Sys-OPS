<?php

require_once('../process/dbh.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

function formatHours($hoursDecimal)
{
    $hoursDecimal = floatval($hoursDecimal);
    $h = floor($hoursDecimal);
    $m = round(($hoursDecimal - $h) * 60);
    return sprintf("%dh %02dm", $h, $m);
}


$presentQ = mysqli_query($conn, "
    SELECT COUNT(DISTINCT emp_id) AS present
    FROM attendance
    WHERE work_date = CURDATE()
      AND (
         check_in_morning IS NOT NULL
      OR check_in_afternoon IS NOT NULL
      OR check_in_ot IS NOT NULL
      )
");
$present = intval(mysqli_fetch_assoc($presentQ)['present'] ?? 0);

$skipQ = mysqli_query($conn, "
    SELECT COUNT(DISTINCT emp_id) AS skipped
    FROM attendance
    WHERE work_date = CURDATE()
      AND (
          (check_in_morning IS NOT NULL AND check_out_lunch IS NULL)
       OR (check_in_afternoon IS NOT NULL AND check_out_evening IS NULL)
       OR (check_in_ot IS NOT NULL AND check_out_ot IS NULL)
      )
");
$skipped = intval(mysqli_fetch_assoc($skipQ)['skipped'] ?? 0);

$otCountQ = mysqli_query($conn, "
    SELECT COUNT(*) AS ot_count FROM attendance WHERE work_date = CURDATE() AND COALESCE(ot_hours,0) > 0
");
$ot_count = intval(mysqli_fetch_assoc($otCountQ)['ot_count'] ?? 0);

$otSumQ = mysqli_query($conn, "
    SELECT COALESCE(SUM(ot_hours),0) AS ot_sum
    FROM attendance
    WHERE work_date = CURDATE()
");
$ot_sum = floatval(mysqli_fetch_assoc($otSumQ)['ot_sum'] ?? 0.0);

$totalHoursQ = mysqli_query($conn, "
    SELECT COALESCE(SUM(total_hours),0) AS total_sum
    FROM attendance
    WHERE work_date = CURDATE()
");
$total_hours = floatval(mysqli_fetch_assoc($totalHoursQ)['total_sum'] ?? 0.0);

$sql2 = "SELECT e.id, e.firstName, e.lastName,
            COALESCE(SUM(a.total_hours),0) AS total_hours,
            COALESCE(SUM(a.ot_hours),0) AS total_ot
         FROM employee e
         LEFT JOIN attendance a ON e.id = a.emp_id
         GROUP BY e.id
         ORDER BY total_hours DESC";
$res2 = mysqli_query($conn, $sql2);

$labels = [];
$employee_hours = [];
$employee_ot = [];
$empTableRows = [];
while ($row = mysqli_fetch_assoc($res2)) {
    $labels[] = $row['firstName'] . ' ' . $row['lastName'];
    $employee_hours[] = floatval($row['total_hours']);
    $employee_ot[] = floatval($row['total_ot']);
    $empTableRows[] = [
        'name' => $row['firstName'] . ' ' . $row['lastName'],
        'total_hours' => floatval($row['total_hours']),
        'total_ot' => floatval($row['total_ot'])
    ];
}

$pie_ot = $ot_sum;
$pie_regular = max(0, $total_hours - $ot_sum);

$days_back = 13;
$dates = [];
for ($i = $days_back; $i >= 0; $i--) {
    $dates[] = date('Y-m-d', strtotime("-$i days"));
}

$heatmap = [];
$dayTotals = [];
foreach ($dates as $d) {
    $heatmap[$d] = array_fill(0, 24, 0);
    $dayTotals[$d] = 0.0;
}

$dates_list = implode("','", $dates);

$checkQ = mysqli_query($conn, "
    SELECT work_date, check_in_morning, check_in_afternoon, check_in_ot, total_hours
    FROM attendance
    WHERE work_date IN ('$dates_list')
");

while ($r = mysqli_fetch_assoc($checkQ)) {
    $d = $r['work_date'];
    if (!empty($r['check_in_morning'])) {
        $h = intval(date('G', strtotime($r['check_in_morning'])));
        $heatmap[$d][$h]++;
    }
    if (!empty($r['check_in_afternoon'])) {
        $h = intval(date('G', strtotime($r['check_in_afternoon'])));
        $heatmap[$d][$h]++;
    }
    if (!empty($r['check_in_ot'])) {
        $h = intval(date('G', strtotime($r['check_in_ot'])));
        $heatmap[$d][$h]++;
    }
    $dayTotals[$d] += floatval($r['total_hours']);
}

$maxCount = 0;
foreach ($heatmap as $row) {
    $maxCount = max($maxCount, max($row));
}
if ($maxCount == 0)
    $maxCount = 1;

$heatmapData = [];
foreach ($dates as $ri => $d) {
    for ($h = 0; $h < 24; $h++) {
        $heatmapData[] = [
            'x' => $h,
            'y' => $d,
            'v' => $heatmap[$d][$h]
        ];
    }
}

$js_labels = json_encode($labels);
$js_empHours = json_encode($employee_hours);
$js_empOT = json_encode($employee_ot);
$js_heatmapData = json_encode($heatmapData);
$js_dates = json_encode($dates);
$js_dayTotals = json_encode(array_values($dayTotals));
$js_pie_regular = json_encode($pie_regular);
$js_pie_ot = json_encode($pie_ot);

?>
<!doctype html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="../styleview.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- font-awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta charset="utf-8">
    <title>Admin Attendance Dashboard — Glass</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix@2.0.1/dist/chartjs-chart-matrix.min.js"></script>


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <style>
        :root {
            --bg: #071029;
            --card: rgba(255, 255, 255, 0.04);
            --accent1: #7c5cff;
            --accent2: #36d1dc;
            --muted: #9fb3c8;
            --glass-border: rgba(255, 255, 255, 0.06);
            --neon: rgba(124, 92, 255, 0.95);
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: linear-gradient(180deg, #061126 0%, #071029 100%);
            color: #e7f0fb;
            min-height: 100vh;
            padding: 20px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 15, 25, 0.25);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            padding: 12px 20px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 18px;
            gap: 12px;
        }

        .brand {
            font-weight: 800;
            font-size: 20px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* app layout */
        .app {
            display: flex;
            gap: 20px;
        }

        /* sidebar (glass) */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.02));
            border-radius: 14px;
            padding: 16px;
            backdrop-filter: blur(8px) saturate(140%);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 40px rgba(3, 7, 18, 0.6);
            height: fit-content;
            /* ĐỔI THÀNH FIT-CONTENT */
            max-height: calc(100vh - 120px);
            /* THÊM MAX-HEIGHT */
            position: sticky;
            top: 20px;
            overflow-y: auto;
        }

        .sidebar .brandSmall {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 8px
        }

        .nav a {
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 10px;
            color: var(--muted);
            text-decoration: none;
            border-radius: 8px;
        }

        .nav a:hover {
            background: rgba(255, 255, 255, 0.02);
            color: #fff;
        }

        .summary {
            margin-top: 14px;
            display: flex;
            gap: 8px;
            flex-direction: column
        }

        .kv {
            color: var(--muted);
            font-size: 13px
        }

        .badge {
            background: rgba(255, 255, 255, 0.02);
            padding: 8px;
            border-radius: 8px;
            display: inline-block;
            color: #fff;
            font-weight: 700
        }

        /* main area */
        .main {
            flex: 1
        }

        .cards {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px
        }

        .card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border-radius: 12px;
            padding: 14px;
            min-width: 200px;
            border: 1px solid rgba(255, 255, 255, 0.03);
            box-shadow: 0 10px 40px rgba(7, 10, 20, 0.5);
        }

        .card h4 {
            margin: 0;
            color: var(--muted);
            font-size: 13px
        }

        .card p {
            font-size: 20px;
            margin: 6px 0 0 0;
            font-weight: 800
        }

        .charts {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 16px;
            margin-bottom: 18px;
            align-items: start;
        }

        .chart-card {
            padding: 12px;
            border-radius: 12px;
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.02);
            min-height: 320px;
        }

        /* heatmap area */
        .heatwrap {
            display: flex;
            gap: 16px;
            align-items: flex-start
        }

        .heat-card {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.02)
        }

        .heat-meta {
            color: var(--muted);
            font-size: 13px;
            margin-top: 6px
        }

        .table-card {
            padding: 12px;
            border-radius: 12px;
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.02);
        }

        table.emp {
            width: 100%;
            border-collapse: collapse;
            color: var(--muted);
            font-size: 14px
        }

        table.emp th,
        table.emp td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03)
        }

        table.emp th {
            color: #cfe6ff;
            font-weight: 700
        }

        .pagination {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap
        }

        .page-btn {
            padding: 8px 10px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            color: var(--muted);
            border: 1px solid rgba(255, 255, 255, 0.03);
            cursor: pointer
        }

        .page-btn.active {
            background: var(--neon);
            color: #061029;
            font-weight: 700;
            box-shadow: 0 6px 20px rgba(124, 92, 255, 0.15)
        }

        .chartjs-tooltip {
            position: absolute;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
            color: #fff;
            border-radius: 8px;
            padding: 8px 10px;
            box-shadow: 0 8px 30px rgba(124, 92, 255, 0.12);
            pointer-events: none;
            transform: translate(-50%, -120%);
            min-width: 120px;
            font-size: 13px;
            backdrop-filter: blur(6px);
            border: 1px solid rgba(124, 92, 255, 0.14);
        }

        .day-alert td {
            box-shadow: inset 0 -2px 0 rgba(255, 0, 60, 0.12);
            border-left: 2px solid rgba(255, 0, 60, 0.18);
        }

        @media (max-width: 980px) {
            .charts {
                grid-template-columns: 1fr;
            }

            .app {
                flex-direction: column;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto
            }
        }

        .heatmap-card {
            position: relative;
        }

        .glass-tooltip {
            backdrop-filter: blur(12px);
            padding: 10px 14px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
        }

        canvas#heatmap {
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <h1>HRMS</h1>
            <ul id="navli">
                <li><a class="homeblack" href="aloginwel.php">HOME</a></li>
                <li><a class="homeblack" href="addemp.php">Add Employee</a></li>
                <li><a class="homeblack" href="viewemp.php">View Employee</a></li>
                <li><a class="homeblack" href="assign.php">Assign Project</a></li>
                <li><a class="homeblack" href="assignproject.php">Project Status</a></li>
                <li><a class="homered" href="attendance.php">Attendance</a></li>
                <li><a class="homeblack" href="dashboard.php">Dashboard</a></li>
                <li><a class="homeblack" href="salaryemp.php">Salary Table</a></li>
                <li><a class="homeblack" href="empleave.php">Employee Leave</a></li>
                <li><a class="homeblack" href="banner_list.php">Banner</a></li>
                <li><a class="homeblack" href="../alogin.html">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <div class="divider"></div>
    <div id="divimg">

    </div>

    <div class="topbar">
        <div class="brand"><i class="fa fa-sitemap"></i> HRMS — Attendance</div>
        <div style="display:flex; gap:12px; align-items:center;">
            <div class="kv">Today: <strong><?php echo date('Y-m-d'); ?></strong></div>
            <button id="themeToggle" class="page-btn"><i class="fa fa-circle-half-stroke"></i> Theme</button>
        </div>
    </div>

    <div class="app">
        <aside class="sidebar">
            <div class="brandSmall">Admin</div>
            <nav class="nav" style="margin-bottom:12px;">
                <a href="../aloginwel.php"><i class="fa fa-home"></i> Home</a>
                <a href="../viewemp.php"><i class="fa fa-users"></i> Employees</a>
                <a href="dashboard.php"><i class="fa fa-chart-pie"></i> Dashboard</a>
                <a href="../salaryemp.php"><i class="fa fa-coins"></i> Salary</a>
            </nav>

            <div class="summary">
                <div class="kv">Summary</div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <div class="badge">Present: <?php echo $present; ?></div>
                    <div class="badge">Skipped: <?php echo $skipped; ?></div>
                    <div class="badge">OT Count: <?php echo $ot_count; ?></div>
                </div>
                <div style="margin-top:8px;">
                    <div class="kv">OT Hours: <?php echo formatHours($ot_sum); ?></div>
                    <div class="kv">Total Work: <?php echo formatHours($total_hours); ?></div>
                </div>
            </div>
        </aside>

        <main class="main">
            <div class="cards">
                <div class="card">
                    <h4>Total present today</h4>
                    <p><?php echo $present; ?></p>
                    <div class="kv">Distinct employees checked in</div>
                </div>
                <div class="card">
                    <h4>Total skipped</h4>
                    <p><?php echo $skipped; ?></p>
                    <div class="kv">Checked-in but missing checkout</div>
                </div>
                <div class="card">
                    <h4>Total hours (today)</h4>
                    <p><?php echo formatHours($total_hours); ?></p>
                    <div class="kv">Sum of recorded hours</div>
                </div>
                <div class="card">
                    <h4>Total OT hours (today)</h4>
                    <p><?php echo formatHours($ot_sum); ?></p>
                    <div class="kv">Sum of OT hours</div>
                </div>
            </div>

            <div class="charts">
                <div class="chart-card">
                    <canvas id="barHours" height="300"></canvas>
                </div>

                <div class="chart-card">
                    <canvas id="pieOT" height="280"></canvas>
                    <div style="margin-top:8px;color:var(--muted)">Regular hours vs OT (today)</div>
                </div>
            </div>

            <div class="heatwrap">
                <div class="heat-card">
                    <h4>Check-in Heatmap (last 14 days)</h4>
                    <div class="heat-meta">Counts of check-ins per hour (morning, afternoon, OT). Rows with total &gt;
                        10h are highlighted.</div>
                    <div style="height:420px; position:relative;">
                        <canvas id="heatmapChart"></canvas>
                    </div>
                </div>

                <div class="table-card" style="width:420px;">
                    <h4>Per-employee summary</h4>
                    <div style="display:flex; gap:8px; margin-bottom:8px; align-items:center;">
                        <input id="filterName" placeholder="Search name..."
                            style="flex:1;padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:var(--muted)">
                        <select id="perPage" style="padding:8px;border-radius:8px;">
                            <option value="5">5 / page</option>
                            <option value="10" selected>10 / page</option>
                            <option value="20">20 / page</option>
                        </select>
                    </div>

                    <div id="empTableWrap">
                        <table class="emp" id="empTable">
                            <thead>
                                <tr>
                                    <th>Name <button onclick="clientSort(0)" class="page-btn">↕</button></th>
                                    <th>Total Hours <button onclick="clientSort(1)" class="page-btn">↕</button></th>
                                    <th>Total OT <button onclick="clientSort(2)" class="page-btn">↕</button></th>
                                </tr>
                            </thead>
                            <tbody id="empBody">
                                <?php foreach ($empTableRows as $r): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                                        <td><?php echo number_format($r['total_hours'], 2); ?></td>
                                        <td><?php echo number_format($r['total_ot'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="pagination" id="pagination"></div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <div id="chartTooltip" class="chartjs-tooltip" style="display:none;"></div>

    <script>
        const labels = <?php echo $js_labels; ?>;
        const empHours = <?php echo $js_empHours; ?>;
        const empOT = <?php echo $js_empOT; ?>;
        const heatmapData = <?php echo $js_heatmapData; ?>;
        const heatDates = <?php echo $js_dates; ?>;
        const dayTotals = <?php echo $js_dayTotals; ?>;

        const pieRegular = <?php echo $js_pie_regular; ?>;
        const pieOT = <?php echo $js_pie_ot; ?>;

        console.log("Pie data:", pieRegular, pieOT);
        console.log("Pie canvas:", document.getElementById('pieOT'));

        function fmtHours(h) {
            const hours = Math.floor(h);
            const minutes = Math.round((h - hours) * 60);
            return `${hours}h ${minutes}m`;
        }

        const barCtx = document.getElementById('barHours').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Total Hours', data: empHours, backgroundColor: 'rgba(124,92,255,0.85)' },
                    { label: 'Total OT', data: empOT, backgroundColor: 'rgba(54,209,220,0.9)' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {  // THÊM PHẦN NÀY
                        callbacks: {
                            label: (ctx) => {
                                const label = ctx.dataset.label || "";
                                const v = ctx.parsed.y;
                                return `${label}: ${fmtHours(v)}`;
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true } }
            }
        });

        const pieCtx = document.getElementById('pieOT').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Regular (today)', 'OT (today)'],
                datasets: [{
                    data: [pieRegular, pieOT],
                    backgroundColor: ['rgba(124,92,255,0.9)', 'rgba(255,99,71,0.9)']
                }]
            },
            options: {  // THÊM TOÀN BỘ PHẦN NÀY
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                let label = ctx.label || "";
                                let value = ctx.raw;
                                return `${label}: ${fmtHours(value)}`;
                            }
                        }
                    }
                }
            }
        });

        const heatCtx = document.getElementById('heatmapChart').getContext('2d');

        const xLabels = Array.from({ length: 24 }, (_, i) => i);
        const yLabels = heatDates;

        const heatData = heatmapData.map(it => ({ x: it.x, y: it.y, v: it.v }));

        function colorForValue(v, max) {
            const t = Math.min(1, v / Math.max(1, max));
            const a = 0.12 + 0.78 * t;
            const r1 = 124, g1 = 92, b1 = 255; const r2 = 54, g2 = 209, b2 = 220;
            const r = Math.round(r1 + (r2 - r1) * t);
            const g = Math.round(g1 + (g2 - g1) * t);
            const b = Math.round(b1 + (b2 - b1) * t);
            return `rgba(${r},${g},${b},${a})`;
        }

        const maxV = Math.max(1, ...heatData.map(d => d.v));

        const heatmap = new Chart(heatCtx, {
            type: 'matrix',
            data: {
                datasets: [{
                    label: "Check-in Heatmap",
                    data: heatData,
                    backgroundColor(ctx) {
                        const value = ctx.raw.v;
                        const t = Math.min(1, value / maxV);
                        return `rgba(0, 255, 180, ${0.2 + 0.8 * t})`;
                    },
                    width: ctx => {
                        const a = ctx.chart.chartArea;
                        return a ? (a.width / 24) - 2 : 10;
                    },
                    height: ctx => {
                        const a = ctx.chart.chartArea;
                        return a ? (a.height / yLabels.length) - 2 : 10;
                    }
                }]
            },
            options: {
                responsive: true,
                animation: false,
                scales: {
                    x: { type: "linear", min: 0, max: 23, ticks: { stepSize: 1 } },
                    y: { type: "category", labels: yLabels, offset: true }
                },
                plugins: { legend: { display: false } }
            }
        });

        (function renderDayAlerts() {
            const container = document.querySelector('.heat-card');
            const legendWrap = document.createElement('div');
            legendWrap.style.marginTop = '8px';
            legendWrap.innerHTML = '<div style="color:var(--muted); font-size:13px">Alert: day total &gt; 10h will be highlighted.</div>';
            container.appendChild(legendWrap);
        })();

        let rows = Array.from(document.querySelectorAll('#empBody tr')).map(r => {
            return {
                name: r.cells[0].innerText.trim(),
                total_hours: parseFloat(r.cells[1].innerText) || 0,
                total_ot: parseFloat(r.cells[2].innerText) || 0
            };
        });

        let currentPage = 1;
        const perPageSelect = document.getElementById('perPage');
        const filterInput = document.getElementById('filterName');
        const paginationEl = document.getElementById('pagination');
        const empBody = document.getElementById('empBody');

        function renderTable() {
            const perPage = parseInt(perPageSelect.value, 10);
            const q = filterInput.value.trim().toLowerCase();
            let filtered = rows.filter(r => r.name.toLowerCase().includes(q));
            const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * perPage;
            const pageRows = filtered.slice(start, start + perPage);

            empBody.innerHTML = pageRows.map(r => `<tr><td>${r.name}</td><td>${r.total_hours.toFixed(2)}</td><td>${r.total_ot.toFixed(2)}</td></tr>`).join('');

            paginationEl.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const b = document.createElement('button');
                b.className = 'page-btn' + (i === currentPage ? ' active' : '');
                b.innerText = i;
                b.onclick = () => { currentPage = i; renderTable(); };
                paginationEl.appendChild(b);
            }
        }

        perPageSelect.addEventListener('change', () => { currentPage = 1; renderTable(); });
        filterInput.addEventListener('input', () => { currentPage = 1; renderTable(); });

        function clientSort(col) {
            const asc = document.getElementById('empTable').dataset.sortDir !== 'asc';
            rows.sort((a, b) => {
                let va = (col === 0) ? a.name.toLowerCase() : a[col === 1 ? 'total_hours' : 'total_ot'];
                let vb = (col === 0) ? b.name.toLowerCase() : b[col === 1 ? 'total_hours' : 'total_ot'];
                if (va < vb) return asc ? -1 : 1;
                if (va > vb) return asc ? 1 : -1;
                return 0;
            });
            document.getElementById('empTable').dataset.sortDir = asc ? 'asc' : 'desc';
            renderTable();
        }

        renderTable();

        const themeBtn = document.getElementById('themeToggle');
        themeBtn.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            if (document.body.classList.contains('light-mode')) {
                document.body.style.background = '#f5f8fb';
                document.body.style.color = '#061029';
            } else {
                document.body.style.background = '';
                document.body.style.color = '';
            }
        });

        document.querySelectorAll("#empTable tbody tr").forEach(tr => {
            tr.cells[1].innerText = fmtHours(parseFloat(tr.cells[1].innerText));
            tr.cells[2].innerText = fmtHours(parseFloat(tr.cells[2].innerText));
        });


    </script>

</body>

</html>