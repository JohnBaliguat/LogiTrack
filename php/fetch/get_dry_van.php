<?php
include "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    
    $query = "SELECT * FROM operations WHERE entry_id = ? AND entry_type = 'DRY VAN ENTRY'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    
    header("Content-Type: application/json");
    echo json_encode($record);
    $stmt->close();
} else {
    header("Content-Type: application/json");
    echo json_encode(null);
}
?>
