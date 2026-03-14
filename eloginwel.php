<?php
$id = (isset($_GET['id']) ? $_GET['id'] : '');
require_once('process/dbh.php');

$visibleQuery = mysqli_query($conn, "SELECT * FROM banners WHERE is_visible = 1");
$bannerList = [];
if ($visibleQuery && $visibleQuery->num_rows > 0) {
	$bannerList = $visibleQuery->fetch_all(MYSQLI_ASSOC);

	// separate active and normal
	$active = array_filter($bannerList, fn($b) => intval($b['is_active']) === 1);
	// sort active by active_order asc (smaller runs earlier)
	usort($active, fn($a, $b) => (intval($a['active_order'] ?? 0) <=> intval($b['active_order'] ?? 0)));

	// normals = visible but not active
	$normal = array_values(array_filter($bannerList, fn($b) => intval($b['is_active']) !== 1));

	// if no active -> shuffle all visible
	if (count($active) === 0) {
		shuffle($normal);
		$bannerList = $normal;
	} else {
		// keep active first (in order), then shuffle normals
		shuffle($normal);
		$bannerList = array_merge($active, $normal);
	}
}

/* Lấy tên user */
$sqlUserName = "SELECT * FROM `employee` WHERE id = $id";
$resultUserName = mysqli_query($conn, $sqlUserName);
$rowUser = mysqli_fetch_assoc($resultUserName);
$empFirstname = $rowUser['firstName'] ?? 'Employee';

/* Attendance queries */
$today = date('Y-m-d');
$attRes = mysqli_query($conn, "SELECT * FROM attendance WHERE emp_id = $id AND work_date = '$today' LIMIT 1");
$attendance = mysqli_fetch_assoc($attRes);
$currentHour = intval(date('H'));
$locked = ($currentHour >= 18);

/* Other queries */
$sql = "SELECT id, firstName, lastName,  points FROM employee, rank WHERE rank.eid = employee.id order by rank.points desc";
$sql1 = "SELECT `pname`, `duedate` FROM `project` WHERE eid = $id and status = 'Due'";
$sql2 = "SELECT * FROM employee, employee_leave WHERE employee.id = $id and employee_leave.id = $id order by employee_leave.token";
$sql3 = "SELECT * FROM salary WHERE id = $id";

$result = mysqli_query($conn, $sql);
$result1 = mysqli_query($conn, $sql1);
$result2 = mysqli_query($conn, $sql2);
$result3 = mysqli_query($conn, $sql3);
?>

<html>

<head>
	<title>Employee Panel | HRMS</title>
	<link rel="stylesheet" type="text/css" href="styleemplogin.css">

	<style>
		.hero-wrapper {
			position: relative;
			width: 100%;
			height: 380px;
			/* smaller than 70vh for desktop consistency; change as needed */
			overflow: hidden;
			background: #111;
		}

		.hero-wrapper img {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
			opacity: 0;
			transform: scale(1.02);
			transition: opacity 700ms ease, transform 900ms ease, filter 700ms ease;
			filter: blur(2px);
		}

		.hero-wrapper img.active {
			opacity: 1;
			transform: scale(1);
			filter: blur(0);
		}

		.nav-btn {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			background: rgba(0, 0, 0, 0.35);
			border: none;
			color: #fff;
			padding: 10px 14px;
			font-size: 22px;
			cursor: pointer;
			border-radius: 50%;
			z-index: 50;
		}

		.nav-btn.prev {
			left: 18px;
		}

		.nav-btn.next {
			right: 18px;
		}

		.dots {
			position: absolute;
			bottom: 14px;
			width: 100%;
			text-align: center;
			z-index: 60;
		}

		.dot {
			display: inline-block;
			width: 10px;
			height: 10px;
			margin: 0 6px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.45);
			cursor: pointer;
			transition: transform .18s;
		}

		.dot.active {
			background: #fff;
			transform: scale(1.25);
		}

		/* small responsive */
		@media (max-width:700px) {
			.hero-wrapper {
				height: 220px;
			}

			.nav-btn {
				padding: 8px;
				font-size: 18px;
			}
		}

		/* Base button */
		.btn {
			padding: 5px 8px;
			border-radius: 14px;
			font-size: 1.15em;
			font-weight: 600;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 10px;
			cursor: pointer;
			border: none;
			outline: none;
			transition: transform 0.15s ease, box-shadow 0.3s ease;
			user-select: none;
		}

		/* RUNG KHI CLICK */
		.btn:active {
			animation: btnShake 0.2s linear;
			transform: scale(0.96);
		}

		@keyframes btnShake {
			0% {
				transform: translateX(0);
			}

			30% {
				transform: translateX(-3px);
			}

			60% {
				transform: translateX(3px);
			}

			100% {
				transform: translateX(0);
			}
		}

		/* GLOW khi hover */
		.btn:hover {
			box-shadow: 0 0 18px rgba(255, 255, 255, 0.4);
		}

		/* COLORS — gradient neon */
		.btn-green {
			background: linear-gradient(135deg, #00d98d, #00a86b);
			color: white;
		}

		.btn-green:hover {
			background: linear-gradient(135deg, #00f2a5, #00c27c);
		}

		.btn-yellow {
			background: linear-gradient(135deg, #ffd86f, #ffb800);
			color: #3c2f00;
		}

		.btn-yellow:hover {
			background: linear-gradient(135deg, #ffe89b, #ffcb33);
		}

		.btn-red {
			background: linear-gradient(135deg, #ff6a6a, #d84343);
			color: white;
		}

		.btn-red:hover {
			background: linear-gradient(135deg, #ff8a8a, #e25555);
		}

		/* ICON NÚT */
		.btn i {
			font-size: 1.3em;
		}

		/* INFO message */
		.info-label,
		.info-error {
			padding: 10px 14px;
			border-radius: 8px;
			margin-bottom: 10px;
			display: inline-block;
			font-weight: 500;
		}

		.info-label {
			background: #eef5ff;
			border-left: 4px solid #007bff;
		}

		.info-error {
			background: #ffeaea;
			border-left: 4px solid #d90000;
		}
	</style>
</head>

<body>

	<!-- HEADER -->
	<header>
		<nav>
			<h1>HRMS</h1>
			<ul id="navli">
				<li><a class="homered" href="eloginwel.php?id=<?php echo $id ?>">HOME</a></li>
				<li><a class="homeblack" href="myprofile.php?id=<?php echo $id ?>">My Profile</a></li>
				<li><a class="homeblack" href="empproject.php?id=<?php echo $id ?>">My Projects</a></li>
				<li><a class="homeblack" href="applyleave.php?id=<?php echo $id ?>">Apply Leave</a></li>
				<li><a class="homeblack" href="elogin.html">Log Out</a></li>
			</ul>
		</nav>
	</header>

	<div class="divider"></div>

	<div style="text-align:right; margin:30px 0;">
		<h3>Hello 👋<span
				style="color: #FF4500; font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; font-size: 1.3em; font-weight: bold"><?php echo htmlspecialchars($empFirstname); ?></span>,
			Have a good day!</h3>

		<?php
		date_default_timezone_set('Asia/Ho_Chi_Minh'); // optional if set in dbh.php
		$nowHour = intval(date('H'));
		?>
		<?php if (!$attendance): ?>

			<?php if ($locked): ?>
				<div class="info-error">Attendance locked after 18:00. Contact admin.</div>
			<?php else: ?>
				<form action="attendance_action.php" method="get">
					<input type="hidden" name="id" value="<?= $id ?>">
					<?php if ($nowHour < 12): ?>
						<button class="btn btn-green">🌅 Check In (Morning)</button>
					<?php else: ?>
						<button class="btn btn-green">🌞 Check In (Afternoon)</button>
					<?php endif; ?>
				</form>
			<?php endif; ?>

		<?php else: ?>

			<?php if (!empty($attendance['check_in_morning']) && empty($attendance['check_out_lunch'])): ?>
				<div class="info-label">Morning in: <?= $attendance['check_in_morning'] ?></div>
				<form action="attendance_action.php" method="get">
					<input type="hidden" name="id" value="<?= $id ?>">
					<button class="btn btn-yellow">🍱 Check Out (Lunch)</button>
				</form>

			<?php elseif (!empty($attendance['check_out_lunch']) && empty($attendance['check_in_afternoon'])): ?>
				<div class="info-label">Morning out: <?= $attendance['check_out_lunch'] ?></div>
				<form action="attendance_action.php" method="get">
					<input type="hidden" name="id" value="<?= $id ?>">
					<button class="btn btn-green">🌞 Check In (Afternoon)</button>
				</form>

			<?php elseif (!empty($attendance['check_in_afternoon']) && empty($attendance['check_out_evening'])): ?>
				<div class="info-label">Afternoon in: <?= $attendance['check_in_afternoon'] ?></div>
				<?php if ($locked): ?>
					<div class="info-error">It's after 18:00 — contact admin.</div>
				<?php else: ?>
					<form action="attendance_action.php" method="get">
						<input type="hidden" name="id" value="<?= $id ?>">
						<button class="btn btn-red">🌙 Check Out (Evening)</button>
					</form>
				<?php endif; ?>

			<?php elseif (!empty($attendance['check_out_evening']) && empty($attendance['check_in_ot'])): ?>
				<div class="info-label">Evening out: <?= $attendance['check_out_evening'] ?></div>
				<form action="attendance_action.php" method="get">
					<input type="hidden" name="id" value="<?= $id ?>">
					<button class="btn btn-purple">🔥 Start OT</button>
				</form>

			<?php elseif (!empty($attendance['check_in_ot']) && empty($attendance['check_out_ot'])): ?>
				<div class="info-label">OT in: <?= $attendance['check_in_ot'] ?></div>
				<form action="attendance_action.php" method="get">
					<input type="hidden" name="id" value="<?= $id ?>">
					<button class="btn btn-red">⏳ End OT</button>
				</form>

			<?php else: ?>
				<div class="info-label">
					Completed today — Total Hours:
					<strong><?= $attendance['total_hours'] ?? '0' ?></strong>
					(OT: <strong><?= $attendance['ot_hours'] ?? '0' ?></strong>)
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</div>

	<div class="hero-wrapper" id="hero">
		<?php foreach ($bannerList as $i => $b): ?>
			<img src="<?= htmlspecialchars($b['image_url']) ?>" alt="banner-<?= $i ?>"
				class="<?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
		<?php endforeach; ?>

		<?php if (count($bannerList) > 1): ?>
			<button class="nav-btn prev" id="prevBtn">❮</button>
			<button class="nav-btn next" id="nextBtn">❯</button>
		<?php endif; ?>

		<div class="dots" id="dots">
			<?php foreach ($bannerList as $i => $b): ?>
				<span class="dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>"></span>
			<?php endforeach; ?>
		</div>
	</div>

	<div style="width:90%;margin:auto;margin-top:40px;">

		<h2 style="text-align:center;">Employee Leaderboard</h2>
		<table>
			<tr bgcolor="#000">
				<th>Seq.</th>
				<th>Emp. ID</th>
				<th>Name</th>
				<th>Points</th>
			</tr>
			<?php $seq = 1;
			while ($r = mysqli_fetch_assoc($result)): ?>
				<tr>
					<td><?= $seq++ ?></td>
					<td><?= $r['id'] ?></td>
					<td><?= $r['firstName'] . ' ' . $r['lastName'] ?></td>
					<td><?= $r['points'] ?></td>
				</tr>
			<?php endwhile; ?>
		</table>

		<h2 style="text-align:center;">Due Projects</h2>
		<table>
			<tr>
				<th>Project Name</th>
				<th>Due Date</th>
			</tr>
			<?php while ($r = mysqli_fetch_assoc($result1)): ?>
				<tr>
					<td><?= $r['pname'] ?></td>
					<td><?= $r['duedate'] ?></td>
				</tr>
			<?php endwhile; ?>
		</table>

		<h2 style="text-align:center;">Salary Status</h2>
		<table>
			<tr>
				<th>Base Salary</th>
				<th>Bonus</th>
				<th>Total Salary</th>
			</tr>
			<?php while ($r = mysqli_fetch_assoc($result3)): ?>
				<?php $total = $r['base'] + ($r['base'] * $r['bonus'] / 100); ?>
				<tr>
					<td><?= $r['base'] ?></td>
					<td><?= $r['bonus'] ?> %</td>
					<td><?= $total ?></td>
				</tr>
			<?php endwhile; ?>
		</table>

		<h2 style="text-align:center;">Leave Status</h2>
		<table>
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Total Days</th>
				<th>Reason</th>
				<th>Status</th>
			</tr>
			<?php while ($r = mysqli_fetch_assoc($result2)): ?>
				<?php
				$d1 = new DateTime($r['start']);
				$d2 = new DateTime($r['end']);
				$days = $d1->diff($d2)->days;
				?>
				<tr>
					<td><?= $r['start'] ?></td>
					<td><?= $r['end'] ?></td>
					<td><?= $days ?></td>
					<td><?= $r['reason'] ?></td>
					<td><?= $r['status'] ?></td>
				</tr>
			<?php endwhile; ?>
		</table>

	</div>

	<script>
		(function () {
			const slides = Array.from(document.querySelectorAll('.hero-wrapper img'));
			const dots = Array.from(document.querySelectorAll('.dot'));
			const prevBtn = document.getElementById('prevBtn');
			const nextBtn = document.getElementById('nextBtn');
			if (!slides.length) return;

			let index = 0;
			let timer = null;
			const INTERVAL = 5000;

			function show(i) {
				slides.forEach(s => s.classList.remove('active'));
				dots.forEach(d => d.classList.remove('active'));
				slides[i].classList.add('active');
				if (dots[i]) dots[i].classList.add('active');
				index = i;
			}

			function next() { show((index + 1) % slides.length); }
			function prev() { show((index - 1 + slides.length) % slides.length); }

			if (nextBtn) nextBtn.addEventListener('click', () => { next(); resetTimer(); });
			if (prevBtn) prevBtn.addEventListener('click', () => { prev(); resetTimer(); });

			dots.forEach(d => d.addEventListener('click', () => { show(parseInt(d.dataset.index)); resetTimer(); }));

			function startTimer() {
				timer = setInterval(next, INTERVAL);
			}
			function resetTimer() { clearInterval(timer); startTimer(); }

			// keyboard navigation
			document.addEventListener('keydown', (e) => {
				if (e.key === 'ArrowLeft') { prev(); resetTimer(); }
				if (e.key === 'ArrowRight') { next(); resetTimer(); }
			});

			startTimer();
			show(index);
		})();
	</script>

</body>

</html>