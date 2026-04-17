<?php
include "../config/config.php";

$query = "SELECT DISTINCT sku_shipper_segment FROM sku WHERE sku_shipper_segment IS NOT NULL AND sku_shipper_segment != '' ORDER BY sku_shipper_segment ASC";
$result = mysqli_query($conn, $query);

$shippers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $shippers[] = [
        'shipper' => $row['sku_shipper_segment']
    ];
}

header('Content-Type: application/json');
echo json_encode($shippers);
?>
