<?php
include "../config/config.php";

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'delete-dpc-kdi') {
    $response['message'] = 'Invalid request.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$data_id = intval($_POST['data_id'] ?? 0);
if ($data_id <= 0) {
    $response['message'] = 'Invalid record.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare('DELETE FROM operations WHERE entry_id = ?');
if (!$stmt) {
    $response['message'] = 'Prepare failed: ' . $conn->error;
} else {
    $stmt->bind_param('i', $data_id);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'DPC KDI record deleted.';
    } else {
        $response['message'] = 'Delete failed: ' . $stmt->error;
    }
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>