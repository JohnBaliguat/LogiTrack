<?php
include "../config/config.php";

header('Content-Type: application/json; charset=utf-8');

$dateFrom = trim((string) ($_GET['date_from'] ?? ''));
$dateTo = trim((string) ($_GET['date_to'] ?? ''));

if ($dateFrom === '' || $dateTo === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Date range is required.',
    ]);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid date format.',
    ]);
    exit;
}

if ($dateFrom > $dateTo) {
    echo json_encode([
        'success' => false,
        'message' => 'Date from must not be later than date to.',
    ]);
    exit;
}

$sql = "SELECT
    COALESCE(NULLIF(TRIM(driver), ''), 'No Driver') AS driver_name,
    COALESCE(NULLIF(TRIM(driver_idNumber), ''), '-') AS driver_id,
    COUNT(*) AS total_entries,
    SUM(CAST(COALESCE(NULLIF(piece_rate, ''), '0') AS DECIMAL(12,2))) AS total_piece_rate
FROM operations
WHERE DATE(created_date) BETWEEN ? AND ?
  AND COALESCE(NULLIF(TRIM(piece_rate), ''), '0') <> '0'
GROUP BY COALESCE(NULLIF(TRIM(driver), ''), 'No Driver'), COALESCE(NULLIF(TRIM(driver_idNumber), ''), '-')
ORDER BY driver_name ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Prepare failed: ' . $conn->error,
    ]);
    exit;
}

$stmt->bind_param('ss', $dateFrom, $dateTo);
if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Query failed: ' . $stmt->error,
    ]);
    $stmt->close();
    exit;
}

$result = $stmt->get_result();
$rows = [];
$grandTotal = 0.0;
$grandEntries = 0;

while ($row = $result->fetch_assoc()) {
    $totalPieceRate = (float) ($row['total_piece_rate'] ?? 0);
    $totalEntries = (int) ($row['total_entries'] ?? 0);

    $rows[] = [
        'driver_name' => $row['driver_name'],
        'driver_id' => $row['driver_id'],
        'total_entries' => $totalEntries,
        'total_piece_rate' => number_format($totalPieceRate, 2, '.', ''),
    ];

    $grandTotal += $totalPieceRate;
    $grandEntries += $totalEntries;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'rows' => $rows,
    'summary' => [
        'total_drivers' => count($rows),
        'total_entries' => $grandEntries,
        'grand_total_piece_rate' => number_format($grandTotal, 2, '.', ''),
    ],
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
    'generated_at' => date('c'),
]);
exit;
?>
