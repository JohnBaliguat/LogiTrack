<?php
include "../config/config.php";

header("Content-Type: application/json; charset=utf-8");

$dateFrom = trim((string) ($_GET["date_from"] ?? ""));
$dateTo = trim((string) ($_GET["date_to"] ?? ""));
$driverName = trim((string) ($_GET["driver_name"] ?? ""));
$driverId = trim((string) ($_GET["driver_id"] ?? ""));

if ($dateFrom === "" || $dateTo === "" || $driverName === "") {
    echo json_encode([
        "success" => false,
        "message" => "Driver and date range are required.",
    ]);
    exit();
}

if (
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid date format.",
    ]);
    exit();
}

if ($dateFrom > $dateTo) {
    echo json_encode([
        "success" => false,
        "message" => "Date from must not be later than date to.",
    ]);
    exit();
}

$segments = [];
$segmentResult = mysqli_query(
    $conn,
    "SELECT DISTINCT segment FROM trip_rates WHERE TRIM(COALESCE(segment, '')) <> '' ORDER BY segment ASC",
);
if ($segmentResult) {
    while ($row = mysqli_fetch_assoc($segmentResult)) {
        $segments[] = trim((string) $row["segment"]);
    }
}

$sql = "SELECT
    DATE(created_date) AS work_date,
    segment,
    SUM(CAST(COALESCE(NULLIF(piece_rate, ''), '0') AS DECIMAL(12,2))) AS total_piece_rate
FROM operations
WHERE DATE(created_date) BETWEEN ? AND ?
  AND COALESCE(NULLIF(TRIM(driver), ''), 'No Driver') = ?
  AND (? = '' OR COALESCE(NULLIF(TRIM(driver_idNumber), ''), '-') = ?)
  AND COALESCE(NULLIF(TRIM(piece_rate), ''), '0') <> '0'
GROUP BY DATE(created_date), segment
ORDER BY work_date ASC, segment ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error,
    ]);
    exit();
}

$stmt->bind_param(
    "sssss",
    $dateFrom,
    $dateTo,
    $driverName,
    $driverId,
    $driverId,
);
if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed: " . $stmt->error,
    ]);
    $stmt->close();
    exit();
}

$result = $stmt->get_result();
$valueMap = [];

while ($row = $result->fetch_assoc()) {
    $workDate = $row["work_date"];
    $segment = trim((string) $row["segment"]);
    $valueMap[$workDate][$segment] = (float) ($row["total_piece_rate"] ?? 0);

    if ($segment !== "" && !in_array($segment, $segments, true)) {
        $segments[] = $segment;
    }
}

$stmt->close();

$rows = [];
$subtotals = array_fill_keys($segments, 0.0);
$grandTotal = 0.0;

$current = strtotime($dateFrom);
$end = strtotime($dateTo);

while ($current <= $end) {
    $dateKey = date("Y-m-d", $current);
    $segmentValues = [];
    $dailyTotal = 0.0;

    foreach ($segments as $segment) {
        $amount = (float) ($valueMap[$dateKey][$segment] ?? 0);
        $segmentValues[$segment] = $amount;
        $subtotals[$segment] += $amount;
        $dailyTotal += $amount;
    }

    $grandTotal += $dailyTotal;

    $rows[] = [
        "date" => $dateKey,
        "display_date" => date("m/d/y", $current),
        "segments" => $segmentValues,
        "daily_total" => $dailyTotal,
    ];

    $current = strtotime("+1 day", $current);
}

echo json_encode([
    "success" => true,
    "driver_name" => $driverName,
    "driver_id" => $driverId === "" ? "-" : $driverId,
    "date_from" => $dateFrom,
    "date_to" => $dateTo,
    "segments" => array_values($segments),
    "rows" => $rows,
    "subtotals" => $subtotals,
    "grand_total" => $grandTotal,
    "generated_at" => date("c"),
]);
exit();
?>
