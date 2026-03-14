<?php
require_once('../process/dbh.php');

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=attendance_export_" . date('Ymd') . ".xls");

echo "Name\tDate\tMorning In\tLunch Out\tAfternoon In\tEvening Out\tTotal Hours\tOT Hours\n";

$sql = "SELECT a.*, e.firstName, e.lastName FROM attendance a JOIN employee e ON a.emp_id = e.id ORDER BY a.work_date DESC";
$res = mysqli_query($conn, $sql);
while ($r = mysqli_fetch_assoc($res)) {
        echo $r['firstName'] . ' ' . $r['lastName'] . "\t" .
                $r['work_date'] . "\t" .
                ($r['check_in_morning'] ?? '') . "\t" .
                ($r['check_out_lunch'] ?? '') . "\t" .
                ($r['check_in_afternoon'] ?? '') . "\t" .
                ($r['check_out_evening'] ?? '') . "\t" .
                $r['total_hours'] . "\t" .
                $r['ot_hours'] . "\n";
}
exit();