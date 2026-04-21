<?php
include "../config/config.php";
require_once __DIR__ . "/../helpers/waybill_duplicate.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function validate($data) {
    return htmlspecialchars(trim($data));
}

function build_dry_van_route_code($location) {
    return strtoupper(trim((string) $location)) === "DICT" ? "PNB" : "DVO";
}

function build_dry_van_billing_sku($customer, $pulloutLocation, $returnLocation) {
    $customer = trim((string) $customer);
    if ($customer === "") {
        return "";
    }

    $pullout = build_dry_van_route_code($pulloutLocation);
    $empty = build_dry_van_route_code($returnLocation);

    return "DRY CONTAINER-" . $customer . "-" . $pullout . "-" . $empty;
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["action"]) &&
    ($_POST["action"] === "add-dry-van" || $_POST["action"] === "update-dry-van")
) {
    $response = ["success" => false, "message" => ""];

    $data_id = intval($_POST["data_id"] ?? 0);
    $action = $_POST["action"];
    $entry_type = "DRY VAN ENTRY";

    $status = validate($_POST["status"] ?? "");
    $customer_ph = validate($_POST["customer_ph"] ?? "");
    $ecs = validate($_POST["ecs"] ?? "");
    $van_alpha = validate($_POST["van_alpha"] ?? "");
    $van_number = validate($_POST["van_number"] ?? "");
    $shipper = validate($_POST["shipper"] ?? "");
    $eir_out = validate($_POST["eir_out"] ?? "");
    $eir_in = validate($_POST["eir_in"] ?? "");
    $pullout_location = validate($_POST["pullout_location"] ?? "");
    $delivered_to = validate($_POST["delivered_to"] ?? "");
    $return_location = validate($_POST["return_location"] ?? "");
    $size = validate($_POST["size"] ?? "");
    $kms = validate($_POST["kms"] ?? "");
    $booking = validate($_POST["booking"] ?? "");
    $seal = validate($_POST["seal"] ?? "");

    $waybill = validate($_POST["waybill"] ?? "");
    $date_hauled = validate($_POST["date_hauled"] ?? "");
    $driver = validate($_POST["driver"] ?? "");
    $truck = validate($_POST["truck"] ?? "");
    $tr = validate($_POST["tr"] ?? "");
    $date_unloaded = validate($_POST["date_unloaded"] ?? "");
    $remarks = validate($_POST["remarks"] ?? "");
    $driver_idNumber = validate($_POST["driver_idNumber"] ?? "");

    $waybill_empty = validate($_POST["waybill_empty"] ?? "");
    $date_returned = validate($_POST["date_returned"] ?? "");
    $driver_return = validate($_POST["driver_return"] ?? "");
    $truck_return = validate($_POST["truck_return"] ?? "");
    $type = validate($_POST["type"] ?? "");
    $delivered_remarks = validate($_POST["delivered_remarks"] ?? "");
    $driver_return_idNumber = validate($_POST["driver_return_idNumber"] ?? "");

    $created_by = isset($_SESSION["user_name"]) ? validate($_SESSION["user_name"]) : "system";

    $billing_sku = build_dry_van_billing_sku($customer_ph, $pullout_location, $return_location);

    if ($status === "") {
        $response["message"] = "Status is required.";
    } elseif ($customer_ph === "") {
        $response["message"] = "Customer (PH) is required.";
    } elseif ($booking !== "" && operations_booking_exists($conn, $booking, $action === "update-dry-van" ? $data_id : null)) {
        $response["message"] = "This booking is already in use. Please use a different booking.";
    } elseif ($action === "update-dry-van" && $data_id <= 0) {
        $response["message"] = "Invalid record ID.";
    } else {
        if ($action === "add-dry-van") {
            $sql = "INSERT INTO operations (
                entry_type, status, customer_ph, ecs, van_alpha, van_number, shipper, eir_out, eir_in, pullout_location, delivered_to, return_location, size,
                waybill, date_hauled, driver, truck, tr, date_unloaded, remarks,
                waybill_empty, date_returned, type, delivered_remarks,
                kms, booking, seal, created_by, driver_idNumber, driver_return, truck2, driver_return_idNumber, billing_sku
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $response["message"] = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param(
                    "sssssssssssssssssssssssssssssssss",
                    $entry_type,
                    $status,
                    $customer_ph,
                    $ecs,
                    $van_alpha,
                    $van_number,
                    $shipper,
                    $eir_out,
                    $eir_in,
                    $pullout_location,
                    $delivered_to,
                    $return_location,
                    $size,
                    $waybill,
                    $date_hauled,
                    $driver,
                    $truck,
                    $tr,
                    $date_unloaded,
                    $remarks,
                    $waybill_empty,
                    $date_returned,
                    $type,
                    $delivered_remarks,
                    $kms,
                    $booking,
                    $seal,
                    $created_by,
                    $driver_idNumber,
                    $driver_return,
                    $truck_return,
                    $driver_return_idNumber,
                    $billing_sku
                );

                if ($stmt->execute()) {
                    $response["success"] = true;
                    $response["message"] = "Dry van entry created successfully.";
                    $response["record"] = [
                        "entry_id" => $stmt->insert_id,
                        "status" => $status,
                        "customer_ph" => $customer_ph,
                        "ecs" => $ecs,
                        "van_alpha" => $van_alpha,
                        "van_number" => $van_number,
                        "shipper" => $shipper,
                        "eir_out" => $eir_out,
                        "eir_in" => $eir_in,
                        "pullout_location" => $pullout_location,
                        "delivered_to" => $delivered_to,
                        "return_location" => $return_location,
                        "driver" => $driver,
                        "truck" => $truck,
                        "driver_idNumber" => $driver_idNumber,
                        "driver_return" => $driver_return,
                        "truck2" => $truck_return,
                        "driver_return_idNumber" => $driver_return_idNumber,
                        "waybill" => $waybill,
                        "date_hauled" => $date_hauled,
                        "tr" => $tr,
                        "date_unloaded" => $date_unloaded,
                        "remarks" => $remarks,
                        "waybill_empty" => $waybill_empty,
                        "date_returned" => $date_returned,
                        "type" => $type,
                        "delivered_remarks" => $delivered_remarks,
                        "booking" => $booking,
                        "billing_sku" => $billing_sku
                    ];
                } else {
                    $response["message"] = "Execute failed: " . $stmt->error;
                }

                $stmt->close();
            }
        } else {
            $sql = "UPDATE operations SET
                status = ?,
                customer_ph = ?,
                ecs = ?,
                van_alpha = ?,
                van_number = ?,
                shipper = ?,
                eir_out = ?,
                eir_in = ?,
                pullout_location = ?,
                delivered_to = ?,
                return_location = ?,
                size = ?,
                waybill = ?,
                date_hauled = ?,
                driver = ?,
                truck = ?,
                tr = ?,
                date_unloaded = ?,
                remarks = ?,
                waybill_empty = ?,
                date_returned = ?,
                type = ?,
                delivered_remarks = ?,
                kms = ?,
                booking = ?,
                seal = ?,
                driver_idNumber = ?,
                driver_return = ?,
                truck2 = ?,
                driver_return_idNumber = ?,
                billing_sku = ?
                WHERE entry_id = ?";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $response["message"] = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param(
                    "ssssssssssssssssssssssssssssssi",
                    $status,
                    $customer_ph,
                    $ecs,
                    $van_alpha,
                    $van_number,
                    $shipper,
                    $eir_out,
                    $eir_in,
                    $pullout_location,
                    $delivered_to,
                    $return_location,
                    $size,
                    $waybill,
                    $date_hauled,
                    $driver,
                    $truck,
                    $tr,
                    $date_unloaded,
                    $remarks,
                    $waybill_empty,
                    $date_returned,
                    $type,
                    $delivered_remarks,
                    $kms,
                    $booking,
                    $seal,
                    $driver_idNumber,
                    $driver_return,
                    $truck_return,
                    $driver_return_idNumber,
                    $billing_sku,
                    $data_id
                );

                if ($stmt->execute()) {
                    $response["success"] = true;
                    $response["message"] = "Dry van entry updated successfully.";
                    $response["record"] = [
                        "entry_id" => $data_id,
                        "status" => $status,
                        "customer_ph" => $customer_ph,
                        "ecs" => $ecs,
                        "van_alpha" => $van_alpha,
                        "van_number" => $van_number,
                        "shipper" => $shipper,
                        "eir_out" => $eir_out,
                        "eir_in" => $eir_in,
                        "pullout_location" => $pullout_location,
                        "delivered_to" => $delivered_to,
                        "return_location" => $return_location,
                        "driver" => $driver,
                        "truck" => $truck,
                        "driver_idNumber" => $driver_idNumber,
                        "driver_return" => $driver_return,
                        "truck2" => $truck_return,
                        "driver_return_idNumber" => $driver_return_idNumber,
                        "waybill" => $waybill,
                        "date_hauled" => $date_hauled,
                        "tr" => $tr,
                        "date_unloaded" => $date_unloaded,
                        "remarks" => $remarks,
                        "waybill_empty" => $waybill_empty,
                        "date_returned" => $date_returned,
                        "type" => $type,
                        "delivered_remarks" => $delivered_remarks,
                        "booking" => $booking,
                        "billing_sku" => $billing_sku
                    ];
                } else {
                    $response["message"] = "Execute failed: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }

    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
}
?>
