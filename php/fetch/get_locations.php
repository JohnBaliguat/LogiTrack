<?php
include "../config/config.php";

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT `location_id`, `location_name` FROM `location` ORDER BY `location_name` ASC";
$result = mysqli_query($conn, $sql);

$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = [
            'location_id' => (int) $row['location_id'],
            'location_name' => $row['location_name'],
        ];
    }
}

echo json_encode($rows);
