<?php
include "../config/config.php";
require_once __DIR__ . "/../helpers/operations_status.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

function dashboard_query_assoc(mysqli $conn, string $sql): array
{
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return [];
    }

    return mysqli_fetch_assoc($result) ?: [];
}

function dashboard_query_all(mysqli $conn, string $sql): array
{
    $result = mysqli_query($conn, $sql);
    $rows = [];

    if (!$result) {
        return $rows;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

$currentUserType = ucfirst(strtolower((string) ($_SESSION["user_type"] ?? "")));
$currentUserIdNumber = trim((string) ($_SESSION["user_idNumber"] ?? ""));
$operationsWhere = "";

if ($currentUserType === "User" && $currentUserIdNumber !== "") {
    $escapedUserIdNumber = mysqli_real_escape_string($conn, $currentUserIdNumber);
    $operationsWhere = " WHERE created_by = '" . $escapedUserIdNumber . "'";
}

$stats = dashboard_query_assoc(
    $conn,
    "
    SELECT
        COUNT(*) AS total_entries,
        SUM(CASE WHEN DATE(created_date) = CURDATE() THEN 1 ELSE 0 END) AS today_entries,
        SUM(CASE WHEN DATE(created_date) = CURDATE() AND entry_type = 'RV ENTRY' THEN 1 ELSE 0 END) AS today_rv,
        SUM(CASE WHEN DATE(created_date) = CURDATE() AND entry_type = 'OTHERS ENTRY' THEN 1 ELSE 0 END) AS today_others,
        SUM(CASE WHEN DATE(created_date) = CURDATE() AND entry_type = 'DPC_KDs & OPM ENTRY' THEN 1 ELSE 0 END) AS today_dpc,
        SUM(CASE WHEN DATE(created_date) = CURDATE() AND entry_type = 'DRY VAN ENTRY' THEN 1 ELSE 0 END) AS today_dry_van,
        SUM(CASE WHEN DATE(created_date) = CURDATE() AND entry_type = 'CARGO TRUCK ENTRY' THEN 1 ELSE 0 END) AS today_cargo
    FROM operations
    {$operationsWhere}
",
);

$userStats = dashboard_query_assoc(
    $conn,
    "
    SELECT
        COUNT(*) AS total_users,
        SUM(CASE WHEN user_accountStat = 'Active' THEN 1 ELSE 0 END) AS active_users
    FROM user
",
);

$chartRows = dashboard_query_all(
    $conn,
    "
    SELECT
        DATE(created_date) AS created_day,
        COUNT(*) AS total_operations
    FROM operations
    " . ($operationsWhere === ""
        ? "WHERE created_date IS NOT NULL"
        : $operationsWhere . " AND created_date IS NOT NULL") . "
    GROUP BY DATE(created_date)
    ORDER BY created_day ASC
",
);

$chartLabels = [];
$chartValues = [];
foreach ($chartRows as $row) {
    if (empty($row["created_day"])) {
        continue;
    }

    $chartLabels[] = date("M d, Y", strtotime($row["created_day"]));
    $chartValues[] = (int) $row["total_operations"];
}

$activityRows = dashboard_query_all(
    $conn,
    "
    SELECT
        entry_id,
        entry_type,
        waybill,
        created_by,
        modified_by,
        created_date,
        modified_date
    FROM operations
    {$operationsWhere}
    ORDER BY GREATEST(
        COALESCE(modified_date, '1970-01-01 00:00:00'),
        COALESCE(created_date, '1970-01-01 00:00:00')
    ) DESC
    LIMIT 6
",
);

$recentActivities = [];
foreach ($activityRows as $row) {
    $isUpdated =
        !empty($row["modified_date"]) &&
        $row["modified_date"] !== $row["created_date"];
    $recentActivities[] = [
        "type" => $isUpdated ? "updated" : "created",
        "title" => $isUpdated ? "Entry updated" : "New entry added",
        "entry_id" => (int) $row["entry_id"],
        "entry_type" => $row["entry_type"],
        "waybill" => $row["waybill"],
        "actor" => $isUpdated
            ? ($row["modified_by"] ?:
            "System")
            : ($row["created_by"] ?:
            "System"),
        "timestamp" => $isUpdated
            ? $row["modified_date"]
            : $row["created_date"],
    ];
}

$latestRows = dashboard_query_all(
    $conn,
    "
    SELECT
        entry_id,
        entry_type,
        segment,
        activity,
        waybill,
        created_date,
        modified_date,
        truck,
        driver,
        remarks,
        customer_ph,
        outside,
        compound,
        total_trips,
        operations,
        deliver_from,
        delivered_to,
        cargo_date,
        evita_farmind,
        departure,
        arrival,
        tr,
        ph,
        `13_body` AS thirteen_body,
        `13_cover` AS thirteen_cover,
        `13_pads` AS thirteen_pads,
        `18_body` AS eighteen_body,
        `18_cover` AS eighteen_cover,
        `18_pads` AS eighteen_pads,
        `13_total` AS thirteen_total,
        `18_total` AS eighteen_total,
        total_load,
        fgtr_no,
        dpc_date,
        gs,
        operations_ph,
        load_quantity_weight,
        unit_of_measure,
        pullout_location_arrival_date,
        pullout_location_arrival_time,
        pullout_location_departure_date,
        pullout_location_departure_time,
        ph_arrival_date,
        ph_arrival_time,
        van_alpha,
        van_number,
        van_name,
        shipper,
        ecs,
        prime_mover,
        empty_pullout_location,
        loaded_van_loading_start_date,
        loaded_van_loading_start_time,
        loaded_van_loading_finish_date,
        loaded_van_loading_finish_time,
        loaded_van_delivery_departure_date,
        loaded_van_delivery_departure_time,
        loaded_van_delivery_arrival_date,
        loaded_van_delivery_arrival_time,
        genset_shutoff_date,
        genset_shutoff_time,
        end_uploading_date,
        end_uploading_time,
        dr_no,
        load_description,
        delivered_by_prime_mover,
        delivered_by_driver,
        genset_hr_meter_start,
        genset_hr_meter_end,
        genset_start_date,
        genset_start_time,
        genset_end_date,
        genset_end_time
    FROM operations
    {$operationsWhere}
    ORDER BY entry_id DESC
    LIMIT 8
",
);

$routeByType = operations_route_by_type();
$latestEntries = [];
$completeCount = 0;
$pendingCount = 0;

foreach ($latestRows as $row) {
    $normalizedType = operations_normalize_entry_type($row["entry_type"] ?? "");
    $missingFields = operations_missing_fields($row);
    $isComplete = count($missingFields) === 0;

    if ($isComplete) {
        $completeCount++;
    } else {
        $pendingCount++;
    }

    $latestEntries[] = [
        "entry_id" => (int) $row["entry_id"],
        "entry_type" => $row["entry_type"],
        "segment" => $row["segment"],
        "activity" => $row["activity"],
        "waybill" => $row["waybill"],
        "status" => $isComplete ? "Complete" : "Pending",
        "missing_count" => count($missingFields),
        "created_date" => $row["created_date"],
        "route" => $routeByType[$normalizedType] ?? "entry",
    ];
}

$todayRows = dashboard_query_all(
    $conn,
    "
    SELECT
        entry_type,
        segment,
        activity,
        waybill,
        truck,
        driver,
        remarks,
        customer_ph,
        outside,
        compound,
        total_trips,
        operations,
        deliver_from,
        delivered_to,
        cargo_date,
        evita_farmind,
        departure,
        arrival,
        tr,
        ph,
        `13_body` AS thirteen_body,
        `13_cover` AS thirteen_cover,
        `13_pads` AS thirteen_pads,
        `18_body` AS eighteen_body,
        `18_cover` AS eighteen_cover,
        `18_pads` AS eighteen_pads,
        `13_total` AS thirteen_total,
        `18_total` AS eighteen_total,
        total_load,
        fgtr_no,
        dpc_date,
        gs,
        operations_ph,
        load_quantity_weight,
        unit_of_measure,
        pullout_location_arrival_date,
        pullout_location_arrival_time,
        pullout_location_departure_date,
        pullout_location_departure_time,
        ph_arrival_date,
        ph_arrival_time,
        van_alpha,
        van_number,
        van_name,
        shipper,
        ecs,
        prime_mover,
        empty_pullout_location,
        loaded_van_loading_start_date,
        loaded_van_loading_start_time,
        loaded_van_loading_finish_date,
        loaded_van_loading_finish_time,
        loaded_van_delivery_departure_date,
        loaded_van_delivery_departure_time,
        loaded_van_delivery_arrival_date,
        loaded_van_delivery_arrival_time,
        genset_shutoff_date,
        genset_shutoff_time,
        end_uploading_date,
        end_uploading_time,
        dr_no,
        load_description,
        delivered_by_prime_mover,
        delivered_by_driver,
        genset_hr_meter_start,
        genset_hr_meter_end,
        genset_start_date,
        genset_start_time,
        genset_end_date,
        genset_end_time,
        waybill_date
    FROM operations
    " . ($operationsWhere === ""
        ? "WHERE DATE(created_date) = CURDATE()"
        : $operationsWhere . " AND DATE(created_date) = CURDATE()") . "
",
);

$todayComplete = 0;
$todayPending = 0;
foreach ($todayRows as $row) {
    if (count(operations_missing_fields($row)) === 0) {
        $todayComplete++;
    } else {
        $todayPending++;
    }
}

echo json_encode([
    "success" => true,
    "stats" => [
        "total_entries" => (int) ($stats["total_entries"] ?? 0),
        "today_entries" => (int) ($stats["today_entries"] ?? 0),
        "today_complete" => $todayComplete,
        "today_pending" => $todayPending,
        "active_users" => (int) ($userStats["active_users"] ?? 0),
        "by_type" => [
            "rv" => (int) ($stats["today_rv"] ?? 0),
            "others" => (int) ($stats["today_others"] ?? 0),
            "dpc_kdi" => (int) ($stats["today_dpc"] ?? 0),
            "dry_van" => (int) ($stats["today_dry_van"] ?? 0),
            "cargo_truck" => (int) ($stats["today_cargo"] ?? 0),
        ],
    ],
    "chart" => [
        "labels" => $chartLabels,
        "values" => $chartValues,
    ],
    "recent_activities" => $recentActivities,
    "latest_entries" => $latestEntries,
    "generated_at" => date("c"),
]);
exit();
?>
