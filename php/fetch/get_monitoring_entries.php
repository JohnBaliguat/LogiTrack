<?php
include "../config/config.php";
require_once __DIR__ . "/../helpers/operations_status.php";

header("Content-Type: application/json");
$routeByType = operations_route_by_type();

$sql = "SELECT
    entry_id,
    entry_type,
    segment,
    activity,
    waybill_date,
    waybill,
    truck,
    driver,
    remarks,
    customer_ph,
    outside,
    compound,
    total_trips,
    operations,
    deliver_from,
    delivered_to,
    cargo_date,
    evita_farmind,
    departure,
    arrival,
    tr,
    ph,
    13_body AS thirteen_body,
    13_cover AS thirteen_cover,
    13_pads AS thirteen_pads,
    18_body AS eighteen_body,
    18_cover AS eighteen_cover,
    18_pads AS eighteen_pads,
    13_total AS thirteen_total,
    18_total AS eighteen_total,
    total_load,
    fgtr_no,
    dpc_date,
    gs,
    operations_ph,
    load_quantity_weight,
    unit_of_measure,
    pullout_location_arrival_date,
    pullout_location_arrival_time,
    pullout_location_departure_date,
    pullout_location_departure_time,
    ph_arrival_date,
    ph_arrival_time,
    van_alpha,
    van_number,
    van_name,
    shipper,
    ecs,
    prime_mover,
    empty_pullout_location,
    loaded_van_loading_start_date,
    loaded_van_loading_start_time,
    loaded_van_loading_finish_date,
    loaded_van_loading_finish_time,
    loaded_van_delivery_departure_date,
    loaded_van_delivery_departure_time,
    loaded_van_delivery_arrival_date,
    loaded_van_delivery_arrival_time,
    genset_shutoff_date,
    genset_shutoff_time,
    end_uploading_date,
    end_uploading_time,
    dr_no,
    load_description,
    delivered_by_prime_mover,
    delivered_by_driver,
    genset_hr_meter_start,
    genset_hr_meter_end,
    genset_start_date,
    genset_start_time,
    genset_end_date,
    genset_end_time,
    created_date,
    modified_date
FROM operations
WHERE entry_type IN ('CARGO TRUCK ENTRY', 'DPC_KDs & OPM ENTRY', 'OTHERS ENTRY', 'RV ENTRY')
ORDER BY
    CASE WHEN TRIM(COALESCE(waybill, '')) = '' THEN 1 ELSE 0 END ASC,
    waybill ASC,
    entry_id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to load monitoring entries.",
    ]);
    exit();
}

$records = [];

while ($row = mysqli_fetch_assoc($result)) {
    $normalizedType = operations_normalize_entry_type($row["entry_type"] ?? "");
    $missingFields = operations_missing_fields($row);

    $records[] = [
        "entry_id" => (int) $row["entry_id"],
        "entry_type" => $row["entry_type"],
        "segment" => $row["segment"],
        "activity" => $row["activity"],
        "waybill" => $row["waybill"],
        "driver" => $row["driver"],
        "remarks" => $row["remarks"],
        "created_date" => $row["created_date"],
        "modified_date" => $row["modified_date"],
        "route" => $routeByType[$normalizedType] ?? "entry",
        "is_complete" => count($missingFields) === 0,
        "missing_count" => count($missingFields),
        "missing_fields" => $missingFields,
    ];
}

echo json_encode([
    "success" => true,
    "records" => $records,
    "generated_at" => date("c"),
]);
exit();
?>
