<?php
include "../config/config.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function validate($data) {
    return htmlspecialchars(trim($data));
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["action"]) &&
    ($_POST["action"] === "add-dry-van" || $_POST["action"] === "update-dry-van")
) {
    $response = ["success" => false, "message" => ""];

    $data_id = intval($_POST["data_id"] ?? 0);
    $entry_type = "DRY VAN ENTRY";
    $status = validate($_POST["status"] ?? "");
    $customer_ph = validate($_POST["customer_ph"] ?? "");
    $delivery_location = validate($_POST["delivery_location"] ?? "");
    $return_location = validate($_POST["return_location"] ?? "");
    $size = validate($_POST["size"] ?? "");
    $kms = validate($_POST["kms"] ?? "");
    $booking = validate($_POST["booking"] ?? "");
    $seal = validate($_POST["seal"] ?? "");

    // Loaded Import / Empty Export fields
    $waybill = validate($_POST["waybill"] ?? "");
    $date_hauled = validate($_POST["date_hauled"] ?? "");
    $driver = validate($_POST["driver"] ?? "");
    $truck = validate($_POST["truck"] ?? "");
    $tr = validate($_POST["tr"] ?? "");
    $date_unloaded = validate($_POST["date_unloaded"] ?? "");
    $remarks = validate($_POST["remarks"] ?? "");
    $driver_idNumber = validate($_POST["driver_idNumber"] ?? "");

    // Empty Import / Loaded Export fields
    $waybill_empty = validate($_POST["waybill_empty"] ?? "");
    $date_returned = validate($_POST["date_returned"] ?? "");
    $driver_return = validate($_POST["driver_return"] ?? "");
    $truck_return = validate($_POST["truck_return"] ?? "");
    $type = validate($_POST["type"] ?? "");
    $delivered_remarks = validate($_POST["delivered_remarks"] ?? "");
    $driver_return_idNumber = validate($_POST["driver_return_idNumber"] ?? "");

    $created_by = isset($_SESSION["user_name"]) ? validate($_SESSION["user_name"]) : "system";

    // Validation
    if (empty($status)) {
        $response["message"] = "Status is required.";
    } elseif (empty($customer_ph)) {
        $response["message"] = "Customer (PH) is required.";
    } else {
        if ($_POST["action"] === "add-dry-van") {
            // INSERT
            $sql = "INSERT INTO operations (
                entry_type, status, customer_ph, delivery_location, return_location, size,
                waybill, date_hauled, driver, truck, tr, date_unloaded, remarks,
                waybill_empty, date_returned, type, delivered_remarks,
                kms, booking, seal, created_by, driver_idNumber
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $response["message"] = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param(
                    "ssssssssssssssssssssss",
                    $entry_type, $status, $customer_ph, $delivery_location, $return_location, $size,
                    $waybill, $date_hauled, $driver, $truck, $tr, $date_unloaded, $remarks,
                    $waybill_empty, $date_returned, $type, $delivered_remarks,
                    $kms, $booking, $seal, $created_by, $driver_idNumber
                );

                if ($stmt->execute()) {
                    $newId = $stmt->insert_id;
                    $response["success"] = true;
                    $response["message"] = "Dry van entry created successfully.";
                    $response["record"] = [
                        "entry_id" => $newId,
                        "status" => $status,
                        "customer_ph" => $customer_ph,
                        "booking" => $booking
                    ];
                } else {
                    $response["message"] = "Execute failed: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            // UPDATE
            if ($data_id <= 0) {
                $response["message"] = "Invalid record ID.";
            } else {
                $sql = "UPDATE operations SET
                    status = ?, customer_ph = ?, delivery_location = ?, return_location = ?, size = ?,
                    waybill = ?, date_hauled = ?, driver = ?, truck = ?, tr = ?, date_unloaded = ?, remarks = ?,
                    waybill_empty = ?, date_returned = ?, type = ?, delivered_remarks = ?,
                    kms = ?, booking = ?, seal = ?, driver_idNumber = ?
                    WHERE entry_id = ?";

                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $response["message"] = "Prepare failed: " . $conn->error;
                } else {
                    $stmt->bind_param(
                        "ssssssssssssssssssi",
                        $status, $customer_ph, $delivery_location, $return_location, $size,
                        $waybill, $date_hauled, $driver, $truck, $tr, $date_unloaded, $remarks,
                        $waybill_empty, $date_returned, $type, $delivered_remarks,
                        $kms, $booking, $seal, $driver_idNumber,
                        $data_id
                    );

                    if ($stmt->execute()) {
                        $response["success"] = true;
                        $response["message"] = "Dry van entry updated successfully.";
                    } else {
                        $response["message"] = "Execute failed: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    }

    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
}
?>
