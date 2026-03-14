<?php
require_once('../process/dbh.php');

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$id = intval($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Invalid ID']);
    exit;
}

if ($action === 'toggle_visible') {
    $sql = "UPDATE banners SET is_visible = NOT is_visible WHERE id = $id";
    mysqli_query($conn, $sql);
    $q = mysqli_query($conn, "SELECT is_visible FROM banners WHERE id = $id");
    $r = mysqli_fetch_assoc($q);
    echo json_encode(['ok' => true, 'is_visible' => intval($r['is_visible'])]);
    exit;
}

if ($action === 'set_active') {
    $q = mysqli_query($conn, "SELECT is_active FROM banners WHERE id = $id");
    $r = mysqli_fetch_assoc($q);
    $cur = intval($r['is_active'] ?? 0);

    if ($cur === 0) {
        $orderQ = mysqli_query($conn, "SELECT MAX(active_order) AS maxo FROM banners WHERE active_order IS NOT NULL");
        $orderR = mysqli_fetch_assoc($orderQ);
        $next = intval($orderR['maxo']) + 1;
        $sql = "UPDATE banners SET is_active = 1, active_order = $next WHERE id = $id";
        mysqli_query($conn, $sql);
    } else {
        $sql = "UPDATE banners SET is_active = 0, active_order = NULL WHERE id = $id";
        mysqli_query($conn, $sql);
    }

    $q2 = mysqli_query($conn, "SELECT is_active, active_order FROM banners WHERE id = $id");
    $r2 = mysqli_fetch_assoc($q2);
    echo json_encode(['ok' => true, 'is_active' => intval($r2['is_active']), 'active_order' => $r2['active_order']]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Unknown action']);