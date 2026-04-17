<?php
include "../config/config.php";
require_once __DIR__ . '/../helpers/master_settings.php';

header('Content-Type: application/json; charset=utf-8');

$config = master_settings_config();
$response = [
    'success' => true,
    'data' => [],
];

foreach ($config as $entity => $definition) {
    $selectFields = array_merge([$definition['primary_key']], array_keys($definition['fields']));
    $fieldSql = implode(', ', array_map(static function ($field) {
        return '`' . $field . '`';
    }, $selectFields));

    $sql = sprintf(
        'SELECT %s FROM `%s` ORDER BY %s',
        $fieldSql,
        $definition['table'],
        $definition['default_sort']
    );

    $result = mysqli_query($conn, $sql);
    $rows = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }

    $response['data'][$entity] = $rows;
}

echo json_encode($response);
exit;
?>
