<?php
require_once('../process/dbh.php');
$payload = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

if (!$payload || !isset($payload['id'])) {
    echo json_encode(['ok' => false, 'error' => 'invalid_request']);
    exit;
}
$id = intval($payload['id']);

$res = mysqli_query($conn, "SELECT image_url FROM banners WHERE id = $id LIMIT 1");
if (!$res || mysqli_num_rows($res) === 0) {
    echo json_encode(['ok' => false, 'error' => 'not_found']);
    exit;
}
$row = mysqli_fetch_assoc($res);
$path = '../' . $row['image_url'];

$stmt = mysqli_prepare($conn, "DELETE FROM banners WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

$fileDeleted = true;
if ($ok) {
    if (file_exists($path)) {
        $fileDeleted = unlink($path);
    }
    echo json_encode(['ok' => true, 'fileDeleted' => $fileDeleted]);
} else {
    echo json_encode(['ok' => false, 'error' => 'db_fail']);
}