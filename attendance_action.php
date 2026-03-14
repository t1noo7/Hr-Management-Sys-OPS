<?php
require_once('process/dbh.php');
require_once('process/attendance_helpers.php');

date_default_timezone_set('Asia/Ho_Chi_Minh');

$empid = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$empid) {
    header("Location: eloginwel.php");
    exit();
}
$today = date('Y-m-d');
$hour = intval(date('H'));

$sql = "SELECT * FROM attendance WHERE emp_id = $empid AND work_date = '$today' LIMIT 1";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if (!$row) {
    if ($hour < 12) {
        $insert = "INSERT INTO attendance (emp_id, work_date, check_in_morning) VALUES ($empid, '$today', NOW())";
    } else {
        $insert = "INSERT INTO attendance (emp_id, work_date, check_in_afternoon) VALUES ($empid, '$today', NOW())";
    }
    mysqli_query($conn, $insert);
    header("Location: eloginwel.php?id=$empid");
    exit();
}

$id = $row['id'];

if (!empty($row['check_in_morning']) && empty($row['check_out_lunch'])) {
    mysqli_query($conn, "UPDATE attendance SET check_out_lunch = NOW() WHERE id = $id");
    header("Location: eloginwel.php?id=$empid");
    exit();
}

if (!empty($row['check_out_lunch']) && empty($row['check_in_afternoon'])) {
    mysqli_query($conn, "UPDATE attendance SET check_in_afternoon = NOW() WHERE id = $id");
    header("Location: eloginwel.php?id=$empid");
    exit();
}

if (!empty($row['check_in_afternoon']) && empty($row['check_out_evening'])) {
    mysqli_query($conn, "UPDATE attendance SET check_out_evening = NOW() WHERE id = $id");
    compute_and_store_hours($conn, $id);
    header("Location: eloginwel.php?id=$empid");
    exit();
}

if (!empty($row['check_out_evening']) && empty($row['check_in_ot'])) {
    mysqli_query($conn, "UPDATE attendance SET check_in_ot = NOW() WHERE id = $id");
    header("Location: eloginwel.php?id=$empid");
    exit();
}

if (!empty($row['check_in_ot']) && empty($row['check_out_ot'])) {
    mysqli_query($conn, "UPDATE attendance SET check_out_ot = NOW() WHERE id = $id");
    compute_and_store_hours($conn, $id);
    header("Location: eloginwel.php?id=$empid");
    exit();
}

header("Location: eloginwel.php?id=$empid");
exit();