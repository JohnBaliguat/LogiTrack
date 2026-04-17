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

$validation = master_settings_validate_payload($entity, $_POST);
if (!$validation['valid']) {
    echo json_encode(['success' => false, 'message' => $validation['message']]);
    exit;
}

$values = $validation['values'];
$fields = array_keys($values);
$columnsSql = implode(', ', array_map(static function ($field) {
    return '`' . $field . '`';
}, $fields));
$placeholders = implode(', ', array_fill(0, count($fields), '?'));

$sql = sprintf(
    'INSERT INTO `%s` (%s) VALUES (%s)',
    $definition['table'],
    $columnsSql,
    $placeholders
);

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . mysqli_error($conn)]);
    exit;
}

$bindTypes = str_repeat('s', count($fields));
$bindValues = array_values($values);
$bindParams = [$stmt, $bindTypes];
foreach ($bindValues as $index => $value) {
    $bindParams[] = &$bindValues[$index];
}
call_user_func_array('mysqli_stmt_bind_param', $bindParams);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . mysqli_stmt_error($stmt)]);
    mysqli_stmt_close($stmt);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => $definition['label'] . ' added successfully.',
    'id' => mysqli_insert_id($conn),
]);
mysqli_stmt_close($stmt);
exit;
?>
