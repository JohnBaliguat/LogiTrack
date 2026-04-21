<?php
include "../config/config.php";

function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "add-driver") {
    $response = ["success" => false, "message" => ""];

    $lname = validate($_POST["lastName"] ?? "");
    $fname = validate($_POST["firstName"] ?? "");
    $mname = validate($_POST["middleName"] ?? "");
    $idNumber = validate($_POST["idNumber"] ?? "");
    $dailyRate = validate($_POST["dailyRate"] ?? "");
    $hourlyRate = validate($_POST["hourlyRate"] ?? "");

    if ($lname === "" || $fname === "" || $idNumber === "" || $dailyRate === "" || $hourlyRate === "") {
        $response["message"] = "All required fields must be filled in.";
    } elseif (!is_numeric($dailyRate) || !is_numeric($hourlyRate)) {
        $response["message"] = "Rates must be numeric values.";
    } else {
        $checkSql = "SELECT driver_id FROM drivers WHERE driver_IdNumber = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $idNumber);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $response["message"] = "Driver ID Number already exists.";
        } else {
            $insertSql = "INSERT INTO drivers (driver_lname, driver_fname, driver_mname, driver_IdNumber, driver_dailyRate, driver_hourlyRate) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ssssdd", $lname, $fname, $mname, $idNumber, $dailyRate, $hourlyRate);

            if ($insertStmt->execute()) {
                $response["success"] = true;
                $response["message"] = "Driver registered successfully.";
            } else {
                $response["message"] = "Error adding driver: " . $insertStmt->error;
            }
        }
    }

    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
}
?>
