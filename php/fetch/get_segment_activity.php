<?php
include "../config/config.php";

// Fetch unique segments and activities from trip_rates table
$query = "SELECT DISTINCT `id`, `segment`, `activity`, `baseRate`, `additional`, `totalRates` FROM `trip_rates` ORDER BY `segment` ASC, `activity` ASC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode([]);
    exit();
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'id' => $row['id'],
        'segment' => $row['segment'],
        'activity' => $row['activity'],
        'baseRate' => $row['baseRate'],
        'additional' => $row['additional'],
        'totalRates' => $row['totalRates']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

mysqli_close($conn);
?>
