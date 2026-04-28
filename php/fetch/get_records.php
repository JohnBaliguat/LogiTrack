<?php
include "../config/config.php";

header("Content-Type: application/json");

$dateFrom = trim((string) ($_GET["date_from"] ?? ""));
$dateTo = trim((string) ($_GET["date_to"] ?? ""));
$entryType = trim((string) ($_GET["entry_type"] ?? ""));

if (
    $dateFrom === "" ||
    $dateTo === "" ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) ||
    $dateFrom > $dateTo
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid date range.",
    ]);
    exit();
}

$params = [$dateFrom, $dateTo];
$types = "ss";
$entryTypeSql = "";

if ($entryType !== "" && strtoupper($entryType) !== "ALL") {
    $entryTypeSql = " AND entry_type = ?";
    $params[] = $entryType;
    $types .= "s";
}

$sql = "SELECT
    entry_id,
    entry_type,
    customer_ph,
    ph,
    operations_ph,
    waybill,
    waybill_empty,
    van_alpha,
    van_number,
    van_name,
    tr,
    tr2,
    truck,
    truck2,
    driver,
    driver_return,
    status,
    remarks,
    delivered_remarks,
    created_date,
    modified_date
FROM operations
WHERE DATE(created_date) BETWEEN ? AND ?" . $entryTypeSql . "
ORDER BY created_date DESC, entry_id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to prepare records query.",
    ]);
    exit();
}

$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode([
        "success" => false,
        "message" => "Failed to load records.",
    ]);
    exit();
}

$result = $stmt->get_result();
$records = [];

while ($row = $result->fetch_assoc()) {
    $waybills = array_values(array_unique(array_filter([
        trim((string) ($row["waybill"] ?? "")),
        trim((string) ($row["waybill_empty"] ?? "")),
    ], fn($value) => $value !== "")));

    $drivers = array_values(array_unique(array_filter([
        trim((string) ($row["driver"] ?? "")),
        trim((string) ($row["driver_return"] ?? "")),
    ], fn($value) => $value !== "")));

    $vanParts = array_values(array_unique(array_filter([
        trim((string) ($row["van_alpha"] ?? "")),
        trim((string) ($row["van_number"] ?? "")),
        trim((string) ($row["van_name"] ?? "")),
        trim((string) ($row["tr"] ?? "")),
        trim((string) ($row["tr2"] ?? "")),
    ], fn($value) => $value !== "")));

    $customer = trim((string) ($row["customer_ph"] ?? ""));
    if ($customer === "") {
        $customer = trim((string) ($row["ph"] ?? ""));
    }
    if ($customer === "") {
        $customer = trim((string) ($row["operations_ph"] ?? ""));
    }

    $records[] = [
        "entry_id" => (int) $row["entry_id"],
        "entry_type" => $row["entry_type"] ?? "",
        "customer" => $customer,
        "waybills" => $waybills,
        "van" => implode(" ", $vanParts),
        "drivers" => $drivers,
        "status" => $row["status"] ?? "",
        "remarks" => trim((string) (($row["remarks"] ?? "") !== "" ? $row["remarks"] : ($row["delivered_remarks"] ?? ""))),
        "created_date" => $row["created_date"] ?? "",
        "modified_date" => $row["modified_date"] ?? "",
    ];
}

$stmt->close();

echo json_encode([
    "success" => true,
    "records" => $records,
    "generated_at" => date("c"),
]);
exit();
?>
