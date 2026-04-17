<?php
include "../config/config.php";

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT `trailer_id`, `trailer_name` FROM `trailer` ORDER BY `trailer_name` ASC";
$result = mysqli_query($conn, $sql);

$trailers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $trailers[] = [
            'trailer_id' => (int) $row['trailer_id'],
            'trailer_name' => $row['trailer_name'],
        ];
    }
}

echo json_encode($trailers);
