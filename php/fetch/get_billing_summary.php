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
    COUNT(*) AS total_rows,
    COUNT(DISTINCT COALESCE(NULLIF(TRIM(billing_sku), ''), CONCAT('ROW-', entry_id))) AS total_skus,
    MIN(DATE(created_date)) AS first_date,
    MAX(DATE(created_date)) AS last_date
FROM operations
WHERE DATE(created_date) BETWEEN ? AND ?
  AND TRIM(COALESCE(billing_sku, '')) <> ''";

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
$summary = $result ? $result->fetch_assoc() : [];
$stmt->close();

echo json_encode([
    'success' => true,
    'summary' => [
        'total_rows' => (int) ($summary['total_rows'] ?? 0),
        'total_skus' => (int) ($summary['total_skus'] ?? 0),
        'first_date' => $summary['first_date'] ?? null,
        'last_date' => $summary['last_date'] ?? null,
    ],
]);
exit;
?>
