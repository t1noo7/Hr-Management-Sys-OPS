<?php
require_once('../process/dbh.php');

$sql = "SELECT SUM(total_hours) AS total_hours, SUM(ot_hours) AS total_ot FROM attendance";
$r = mysqli_query($conn, $sql);
$t = mysqli_fetch_assoc($r);
$total_hours = floatval($t['total_hours'] ?? 0);
$total_ot = floatval($t['total_ot'] ?? 0);

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
while ($row = mysqli_fetch_assoc($res2)) {
    $labels[] = $row['firstName'] . ' ' . $row['lastName'];
    $employee_hours[] = floatval($row['total_hours']);
    $employee_ot[] = floatval($row['total_ot']);
}

// 3) Last 30 days totals for line chart
$sql3 = "SELECT work_date, SUM(total_hours) AS day_hours
         FROM attendance
         WHERE work_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
         GROUP BY work_date
         ORDER BY work_date ASC";
$res3 = mysqli_query($conn, $sql3);
$dates = [];
$day_hours = [];
while ($r3 = mysqli_fetch_assoc($res3)) {
    $dates[] = $r3['work_date'];
    $day_hours[] = floatval($r3['day_hours']);
}
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
    <title>Admin Attendance Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial;
            padding: 30px;
        }

        .row {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            background: #fff;
        }

        .card.large {
            width: 780px;
        }

        .card.small {
            width: 360px;
        }

        h2 {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
                <li><a class="homeblack" href="attendance.php">Attendance</a></li>
                <li><a class="homered" href="dashboard.php">Dashboard</a></li>
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
    <header>
        <h1>Attendance Dashboard</h1>
    </header>
    &nbsp;
    <div class="row">
        <div class="card small">
            <h3>Company Hours</h3>
            <p>Total Regular Hours: <strong><?php echo $total_hours; ?></strong></p>
            <p>Total OT Hours: <strong><?php echo $total_ot; ?></strong></p>
            <p><a href="export_attendance.php">Export Excel</a></p>
        </div>

        <div class="card small">
            <canvas id="pieOT" width="350" height="250"></canvas>
        </div>

        <div class="card large">
            <canvas id="barHours" width="750" height="350"></canvas>
        </div>

        <div class="card large">
            <canvas id="lineDays" width="750" height="250"></canvas>
        </div>
    </div>

    <h3>Details (per employee)</h3>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Total Hours</th>
                    <th>Total OT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // reuse result2 - re-query for safety
                $rs = mysqli_query($conn, $sql2);
                while ($rw = mysqli_fetch_assoc($rs)) {
                    echo '<tr><td>' . htmlspecialchars($rw['firstName'] . ' ' . $rw['lastName']) . '</td>' .
                        '<td>' . $rw['total_hours'] . '</td><td>' . $rw['total_ot'] . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        const pieCtx = document.getElementById('pieOT');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Regular Hours', 'Overtime Hours'],
                datasets: [{
                    data: [<?= $total_hours ?>, <?= $total_ot ?>]
                }]
            }
        });

        const barCtx = document.getElementById('barHours');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [
                    { label: 'Total Hours', data: <?= json_encode($employee_hours) ?> },
                    { label: 'Total OT', data: <?= json_encode($employee_ot) ?> }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const lineCtx = document.getElementById('lineDays');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{ label: 'Total Hours per day', data: <?= json_encode($day_hours) ?>, fill: false }]
            }
        });
    </script>
</body>

</html>