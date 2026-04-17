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

$validation = master_settings_validate_payload($entity, $_POST);
if (!$validation['valid']) {
    echo json_encode(['success' => false, 'message' => $validation['message']]);
    exit;
}

$values = $validation['values'];
$fields = array_keys($values);
$assignments = implode(', ', array_map(static function ($field) {
    return '`' . $field . '` = ?';
}, $fields));

$sql = sprintf(
    'UPDATE `%s` SET %s WHERE `%s` = ?',
    $definition['table'],
    $assignments,
    $definition['primary_key']
);

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . mysqli_error($conn)]);
    exit;
}

$bindTypes = str_repeat('s', count($fields)) . 'i';
$bindValues = array_values($values);
$bindValues[] = $id;
$bindParams = [$stmt, $bindTypes];
foreach ($bindValues as $index => $value) {
    $bindParams[] = &$bindValues[$index];
}
call_user_func_array('mysqli_stmt_bind_param', $bindParams);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . mysqli_stmt_error($stmt)]);
    mysqli_stmt_close($stmt);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => $definition['label'] . ' updated successfully.',
]);
mysqli_stmt_close($stmt);
exit;
?>
