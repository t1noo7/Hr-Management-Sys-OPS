<?php
require_once(__DIR__ . '/dbh.php');

function compute_and_store_hours($conn, $attendance_id)
{
    $q = "SELECT * FROM attendance WHERE id = $attendance_id LIMIT 1";
    $res = mysqli_query($conn, $q);
    if (!$res)
        return false;
    $row = mysqli_fetch_assoc($res);
    if (!$row)
        return false;

    $mins = 0;

    if (!empty($row['check_in_morning']) && !empty($row['check_out_lunch'])) {
        $q1 = "SELECT TIMESTAMPDIFF(MINUTE, '{$row['check_in_morning']}', '{$row['check_out_lunch']}') AS m";
        $r1 = mysqli_query($conn, $q1);
        $m1 = $r1 ? intval(mysqli_fetch_assoc($r1)['m']) : 0;
        $mins += max(0, $m1);
    }

    if (!empty($row['check_in_afternoon']) && !empty($row['check_out_evening'])) {
        $q2 = "SELECT TIMESTAMPDIFF(MINUTE, '{$row['check_in_afternoon']}', '{$row['check_out_evening']}') AS m";
        $r2 = mysqli_query($conn, $q2);
        $m2 = $r2 ? intval(mysqli_fetch_assoc($r2)['m']) : 0;
        $mins += max(0, $m2);
    }

    if (!empty($row['check_in_ot']) && !empty($row['check_out_ot'])) {
        $q3 = "SELECT TIMESTAMPDIFF(MINUTE, '{$row['check_in_ot']}', '{$row['check_out_ot']}') AS m";
        $r3 = mysqli_query($conn, $q3);
        $m3 = $r3 ? intval(mysqli_fetch_assoc($r3)['m']) : 0;
        $mins += max(0, $m3);
    }

    $total_hours = round($mins / 60, 2);

    $ot_hours = ($total_hours > 8) ? round($total_hours - 8, 2) : 0;

    $update = "UPDATE attendance SET total_hours = $total_hours, ot_hours = $ot_hours WHERE id = $attendance_id";
    mysqli_query($conn, $update);
    return true;
}