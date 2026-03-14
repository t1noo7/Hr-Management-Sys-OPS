<?php
require_once('../process/dbh.php');

$debug = false;

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid payload']);
    exit;
}

$items = $data['order'];

$pairs = [];
foreach ($items as $p) {
    $id = intval($p['id'] ?? 0);
    $ord = intval($p['order'] ?? 0);
    if ($id > 0 && $ord > 0) {
        $pairs[$id] = $ord;
    }
}

if (empty($pairs)) {
    echo json_encode(['ok' => false, 'error' => 'No valid items']);
    exit;
}

$success_count = 0;
$errors = [];

foreach ($pairs as $id => $order) {
    $sql = "UPDATE banners SET active_order = $order, is_active = 1 WHERE id = $id";

    if ($debug) {
        $errors[] = "Running: $sql";
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $affected = mysqli_affected_rows($conn);
        $success_count++;
        if ($debug) {
            $errors[] = "ID $id: affected rows = $affected";
        }
    } else {
        $errors[] = "ID $id: ERROR - " . mysqli_error($conn);
    }
}

if ($debug) {
    echo json_encode([
        'ok' => true,
        'updated' => $success_count,
        'debug' => $errors,
        'received_pairs' => $pairs
    ]);
} else {
    echo json_encode(['ok' => true, 'updated' => $success_count]);
}