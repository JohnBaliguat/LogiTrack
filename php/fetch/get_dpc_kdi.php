<?php
include "../config/config.php";

$response = ['success' => false, 'message' => '', 'record' => null];

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $response['message'] = 'Invalid record ID.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$sql = "SELECT entry_id, entry_type, segment, activity, waybill_date, waybill, evita_farmind, driver, driver_idNumber, departure, arrival, truck, load_quantity_weight, unit_of_measure, deliver_from, delivered_to, remarks, dpc_date, `13_body`, `13_cover`, `13_pads`, `18_body`, `18_cover`, `18_pads`, `13_total`, `18_total`, total_load, fgtr_no FROM operations WHERE entry_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['message'] = 'Prepare failed: ' . $conn->error;
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    $response['message'] = 'Query failed: ' . $stmt->error;
    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $response['success'] = true;
    $response['record'] = $result->fetch_assoc();
} else {
    $response['message'] = 'Record not found.';
}

$stmt->close();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>