<?php
include "../config/config.php";
require_once __DIR__ . '/../helpers/master_settings.php';

header('Content-Type: application/json; charset=utf-8');

$entity = trim((string) ($_POST['entity'] ?? ''));
$definition = master_settings_entity($entity);

if ($definition === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid settings entity.']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid record ID.']);
    exit;
}

$sql = sprintf(
    'DELETE FROM `%s` WHERE `%s` = ?',
    $definition['table'],
    $definition['primary_key']
);

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . mysqli_stmt_error($stmt)]);
    mysqli_stmt_close($stmt);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => $definition['label'] . ' deleted successfully.',
]);
mysqli_stmt_close($stmt);
exit;
?>
