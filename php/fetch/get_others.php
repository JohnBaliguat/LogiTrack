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

$sql = "SELECT entry_id, entry_type, segment, activity, waybill_date, others_date, cargo_date, dpc_date, pullout_location_arrival_date, production_date, finished_loading_date, created_date, waybill, truck, driver, driver_idNumber, tr, gs, operations_ph, customer_ph, load_quantity_weight, unit_of_measure, kms, deliver_from, delivered_to, remarks FROM operations WHERE entry_id = ?";

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
