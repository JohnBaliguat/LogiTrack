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

$sql = "SELECT entry_id, entry_type, segment_empty, activity_empty, segment, activity, remarks, pullout_location_arrival_date, pullout_location_arrival_time, pullout_location_departure_date, pullout_location_departure_time, ph_arrival_date, ph_arrival_time, van_alpha, van_number, van_name, ph, shipper, ecs, tr, gs, waybill, waybill_empty, prime_mover, driver, empty_pullout_location, loaded_van_loading_start_date, loaded_van_loading_start_time, loaded_van_loading_finish_date, loaded_van_loading_finish_time, loaded_van_delivery_departure_date, loaded_van_delivery_departure_time, loaded_van_delivery_arrival_date, loaded_van_delivery_arrival_time, genset_shutoff_date, genset_shutoff_time, end_uploading_date, end_uploading_time, dr_no, load_description, delivered_by_prime_mover, delivered_by_driver, delivered_to, delivered_remarks, genset_hr_meter_start, genset_hr_meter_end, genset_start_date, genset_start_time, genset_end_date, genset_end_time, driver_idNumber, delivered_by_driverIdNumber FROM operations WHERE entry_id = ?";

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
    $record = $result->fetch_assoc();
    $response['success'] = true;
    $response['record'] = $record;
} else {
    $response['message'] = 'Record not found.';
}

$stmt->close();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
