<?php
include "../config/config.php";

$dateFrom = trim((string) ($_GET["date_from"] ?? ""));
$dateTo = trim((string) ($_GET["date_to"] ?? ""));

if (
    $dateFrom === "" ||
    $dateTo === "" ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) ||
    $dateFrom > $dateTo
) {
    http_response_code(400);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Invalid date range.";
    exit();
}

$filename = sprintf("billing_%s_to_%s.csv", $dateFrom, $dateTo);

header("Content-Type: text/csv; charset=utf-8");
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen("php://output", "w");

$headers = [
    "Entry ID",
    "Entry Type",
    "Created Date",
    "Transaction Date",
    "Billing SKU",
    "Waybill",
    "Segment",
    "Activity",
    "Van Alpha",
    "Van Number",
    "Van Name",
    "PH",
    "Shipper",
    "Waybill Empty",
    "TR",
    "GS",
    "Prime Mover",
    "Driver",
    "Pull-Out Location",
    "Date Time Of Van Unloading",
    "DR Receipt",
    "Load",
    "No. Of Trips",
    "Remarks",
    "Standard Kgs",
    "Van Size",
    "Out",
    "In",
    "Actual Hourmeter Time Start",
    "Actual Hourmeter Time End",
    "Piece Rate",
];

fputcsv($output, $headers);

$sql = "SELECT
    entry_id,
    entry_type,
    created_date,
    waybill_date,
    billing_sku,
    waybill,
    segment,
    activity,
    van_alpha,
    van_number,
    van_name,
    ph,
    shipper,
    waybill_empty,
    tr,
    gs,
    prime_mover,
    driver,
    empty_pullout_location,
    loaded_van_delivery_arrival_date,
    loaded_van_delivery_arrival_time,
    dr_no,
    load_description,
    total_trips,
    remarks,
    piece_rate
FROM operations
WHERE DATE(created_date) BETWEEN ? AND ?
  AND TRIM(COALESCE(billing_sku, '')) <> ''
ORDER BY DATE(created_date) ASC, billing_sku ASC, entry_id ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    fclose($output);
    exit();
}

$stmt->bind_param("ss", $dateFrom, $dateTo);
if (!$stmt->execute()) {
    $stmt->close();
    fclose($output);
    exit();
}

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $unloadingDateTime = trim(
        (string) (($row["loaded_van_delivery_arrival_date"] ?? "") .
            " " .
            ($row["loaded_van_delivery_arrival_time"] ?? "")),
    );

    fputcsv($output, [
        $row["entry_id"] ?? "",
        $row["entry_type"] ?? "",
        $row["created_date"] ?? "",
        $row["waybill_date"] ?? "",
        $row["billing_sku"] ?? "",
        $row["waybill"] ?? "",
        $row["segment"] ?? "",
        $row["activity"] ?? "",
        $row["van_alpha"] ?? "",
        $row["van_number"] ?? "",
        $row["van_name"] ?? "",
        $row["ph"] ?? "",
        $row["shipper"] ?? "",
        $row["waybill_empty"] ?? "",
        $row["tr"] ?? "",
        $row["gs"] ?? "",
        $row["prime_mover"] ?? "",
        $row["driver"] ?? "",
        $row["empty_pullout_location"] ?? "",
        $unloadingDateTime,
        $row["dr_no"] ?? "",
        $row["load_description"] ?? "",
        $row["total_trips"] ?? "",
        $row["remarks"] ?? "",
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        $row["piece_rate"] ?? "",
    ]);
}

$stmt->close();
fclose($output);
exit();
?>
