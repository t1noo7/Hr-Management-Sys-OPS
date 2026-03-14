<?php

require_once('../process/dbh.php');
$sql = "SELECT employee.id,employee.firstName,employee.lastName,salary.base,salary.bonus from employee,`salary` where employee.id=salary.id";

$result = mysqli_query($conn, $sql);

$deleteId = $_POST['delete_id'];
if (isset($_POST['delete_id'])) {
	$delete_query = "DELETE FROM `salary` WHERE id='$deleteId'";
	$result = mysqli_query($conn, $delete_query);
	if ($result) {
		echo "<script>alert('Salary deleted successfully'); window.location.href='salaryemp.php';</script>";
		exit();
	} else {
		echo "<script>alert('Error deleting salary')</>";
	}
}

?>



<html>

<head>
	<title>Salary Table | HRMS</title>
	<link rel="stylesheet" type="text/css" href="../styleview.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
	<!-- font-awesome CDN -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
		integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
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
				<li><a class="homeblack" href="admin/dashboard.php">Dashboard</a></li>
				<li><a class="homered" href="salaryemp.php">Salary Table</a></li>
				<li><a class="homeblack" href="empleave.php">Employee Leave</a></li>
				<li><a class="homeblack" href="banner_list.php">Banner</a></li>
				<li><a class="homeblack" href="../alogin.html">Log Out</a></li>
			</ul>
		</nav>
	</header>

	<div class="divider"></div>
	<div id="divimg">

	</div>

	<table>
		<tr>
			<th align="center">Emp. ID</th>
			<th align="center">Name</th>


			<th align="center">Base Salary</th>
			<th align="center">Bonus</th>
			<th align="center">TotalSalary</th>
			<th align="center">Edit</th>
			<th align="center">Delete</th>


		</tr>

		<?php
		while ($employee = mysqli_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>" . $employee['id'] . "</td>";
			echo "<td>" . $employee['firstName'] . " " . $employee['lastName'] . "</td>";

			echo "<td>" . $employee['base'] . "</td>";
			echo "<td>" . $employee['bonus'] . " %</td>";
			$total = $employee['base'] + ($employee['base'] * $employee['bonus'] / 100);
			echo "<td>" . $total . "</td>";
			echo "<td><a href=\"edit_salary.php?id=$employee[id]\" class=\"text-light\"><i class='fa-solid fa-pen-to-square'></i></a></td>";
			echo "<td>
					<form method='POST' style='display:inline;'
						onsubmit=\"return confirm('Are you sure you want to delete?');\">
						<input type='hidden' name='delete_id' value='{$employee['id']}'>
						<button type='submit' class='btn btn-link text-light p-0' style='border:none;background:none;'>
							<i class='fa-solid fa-trash'></i>
						</button>
					</form>
				</td>";


		}


		?>
	</table>
	<div class="p-t-20">
		<button class="btn btn--radius btn--green" type="submit" style="float: right; margin-right: 60px"><a
				href="reset.php" style="text-decoration: none; color: white"> Add salary</button>
	</div>
</body>

</html>