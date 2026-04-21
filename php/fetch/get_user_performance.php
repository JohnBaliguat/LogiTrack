<?php
include "../config/config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"]) || ucfirst(strtolower((string) ($_SESSION["user_type"] ?? ""))) !== "Admin") {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized",
    ]);
    exit();
}

$sql = "
    SELECT
        u.user_id,
        u.user_idNumber,
        u.user_name,
        u.user_fname,
        u.user_lname,
        u.user_type,
        u.user_accountStat,
        COUNT(o.entry_id) AS total_entries,
        SUM(CASE WHEN DATE(o.created_date) = CURDATE() THEN 1 ELSE 0 END) AS today_entries,
        SUM(CASE WHEN o.entry_type = 'RV ENTRY' THEN 1 ELSE 0 END) AS rv_entries,
        SUM(CASE WHEN o.entry_type = 'OTHERS ENTRY' THEN 1 ELSE 0 END) AS others_entries,
        SUM(CASE WHEN o.entry_type = 'DPC_KDs & OPM ENTRY' THEN 1 ELSE 0 END) AS dpc_entries,
        SUM(CASE WHEN o.entry_type = 'CARGO TRUCK ENTRY' THEN 1 ELSE 0 END) AS cargo_entries,
        SUM(CASE WHEN o.entry_type = 'DRY VAN ENTRY' THEN 1 ELSE 0 END) AS dry_van_entries,
        MAX(o.created_date) AS last_entry_date
    FROM user u
    LEFT JOIN operations o
        ON o.created_by = u.user_idNumber
    WHERE u.user_type <> 'Admin'
    GROUP BY
        u.user_id,
        u.user_idNumber,
        u.user_name,
        u.user_fname,
        u.user_lname,
        u.user_type,
        u.user_accountStat
    ORDER BY total_entries DESC, u.user_lname ASC, u.user_fname ASC
";

$result = mysqli_query($conn, $sql);
$rows = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fullName = trim(($row["user_fname"] ?? "") . " " . ($row["user_lname"] ?? ""));
        $rows[] = [
            "user_id" => (int) ($row["user_id"] ?? 0),
            "user_idNumber" => $row["user_idNumber"] ?? "",
            "user_name" => $row["user_name"] ?? "",
            "full_name" => $fullName,
            "user_type" => $row["user_type"] ?? "",
            "user_accountStat" => $row["user_accountStat"] ?? "",
            "total_entries" => (int) ($row["total_entries"] ?? 0),
            "today_entries" => (int) ($row["today_entries"] ?? 0),
            "rv_entries" => (int) ($row["rv_entries"] ?? 0),
            "others_entries" => (int) ($row["others_entries"] ?? 0),
            "dpc_entries" => (int) ($row["dpc_entries"] ?? 0),
            "cargo_entries" => (int) ($row["cargo_entries"] ?? 0),
            "dry_van_entries" => (int) ($row["dry_van_entries"] ?? 0),
            "last_entry_date" => $row["last_entry_date"] ?? null,
        ];
    }
}

$usersWithEntries = array_values(array_filter($rows, function ($row) {
    return ($row["total_entries"] ?? 0) > 0;
}));

$topPerformer = $usersWithEntries[0] ?? null;
$totalEntries = array_sum(array_map(function ($row) {
    return (int) ($row["total_entries"] ?? 0);
}, $rows));
$todayEntries = array_sum(array_map(function ($row) {
    return (int) ($row["today_entries"] ?? 0);
}, $rows));

$chartRows = array_slice($usersWithEntries, 0, 10);
$chartLabels = array_map(function ($row) {
    return $row["full_name"] ?: $row["user_name"] ?: $row["user_idNumber"];
}, $chartRows);
$chartValues = array_map(function ($row) {
    return (int) ($row["total_entries"] ?? 0);
}, $chartRows);

echo json_encode([
    "success" => true,
    "summary" => [
        "total_users" => count($rows),
        "users_with_entries" => count($usersWithEntries),
        "total_entries" => $totalEntries,
        "today_entries" => $todayEntries,
        "top_performer" => $topPerformer,
    ],
    "chart" => [
        "labels" => $chartLabels,
        "values" => $chartValues,
    ],
    "rows" => $rows,
    "generated_at" => date("c"),
]);
exit();
?>
