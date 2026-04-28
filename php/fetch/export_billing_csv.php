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
    customer_ph,
    operations_ph,
    shipper,
    waybill_empty,
    tr,
    gs,
    prime_mover,
    truck,
    driver,
    empty_pullout_location,
    pullout_location,
    deliver_from,
    date_hauled,
    eir_outDate,
    eir_outTime,
    date_unloaded,
    arrival_time,
    ph_departure_date,
    ph_departure_time,
    dr_no,
    slp_no,
    load_description,
    load_quantity_weight,
    unit_of_measure,
    total_load,
    destination,
    total_trips,
    remarks,
    delivered_remarks,
    size,
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
    $entryTypeValue = trim((string) ($row["entry_type"] ?? ""));
    $isDryVan = $entryTypeValue === "DRY VAN ENTRY";
    $isOthers = $entryTypeValue === "OTHERS ENTRY";

    // Transaction Date
    if ($isDryVan) {
        $transactionDate = trim((string) ($row["date_hauled"] ?? ""));
    } elseif ($isOthers) {
        $transactionDate = trim((string) ($row["waybill_date"] ?? ""));
    } else {
        $transactionDate = trim((string) ($row["waybill_date"] ?? ""));
    }

    // Prime Mover
    $pmValue = ($isDryVan || $isOthers)
        ? trim((string) ($row["truck"] ?? ""))
        : trim((string) ($row["prime_mover"] ?? $row["truck"] ?? ""));

    // Pull-Out Location
    if ($isDryVan) {
        $pulloutLocation = trim((string) ($row["pullout_location"] ?? ""));
    } elseif ($isOthers) {
        $pulloutLocation = trim((string) ($row["deliver_from"] ?? ""));
    } else {
        $pulloutLocation = trim((string) ($row["empty_pullout_location"] ?? ""));
    }

    // Date & Time of Van Unloading
    if ($isDryVan) {
        $unloadingDateTime = trim(($row["date_unloaded"] ?? "") . " " . ($row["arrival_time"] ?? ""));
    } else {
        $unloadingDateTime = trim(($row["ph_departure_date"] ?? "") . " " . ($row["ph_departure_time"] ?? ""));
    }

    // DR Receipt
    $drValue = $isDryVan ? trim((string) ($row["slp_no"] ?? "")) : trim((string) ($row["dr_no"] ?? ""));

    // Load
    if ($isDryVan) {
        $loadValue = trim((string) ($row["destination"] ?? ""));
    } elseif ($isOthers) {
        $qty = trim((string) ($row["load_quantity_weight"] ?? ""));
        $uom = trim((string) ($row["unit_of_measure"] ?? ""));
        $loadValue = $uom !== "" ? trim($qty . " " . $uom) : $qty;
    } else {
        $loadValue = trim((string) ($row["load_description"] ?? $row["total_load"] ?? ""));
    }

    // Van Size
    $vanSize = $isDryVan ? trim((string) ($row["size"] ?? "")) : "";

    // Remarks
    $remarks = trim((string) (($row["remarks"] ?? "") !== "" ? $row["remarks"] : ($row["delivered_remarks"] ?? "")));

    fputcsv($output, [
        $row["entry_id"] ?? "",
        $row["entry_type"] ?? "",
        $row["created_date"] ?? "",
        $transactionDate,
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
        $pmValue,
        $row["driver"] ?? "",
        $pulloutLocation,
        $unloadingDateTime,
        $drValue,
        $loadValue,
        $row["total_trips"] ?? "",
        $remarks,
        "",
        $vanSize,
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
