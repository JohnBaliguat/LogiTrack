<?php

function operations_lookup_piece_rate(mysqli $conn, string $segment, string $activity): string
{
    $segment = trim($segment);
    $activity = trim($activity);

    if ($activity === '') {
        return '';
    }

    $sql = "SELECT totalRates
            FROM trip_rates
            WHERE activity = ?
              AND (? = '' OR segment = ?)
            ORDER BY CASE WHEN segment = ? THEN 0 ELSE 1 END, id DESC
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return '';
    }

    $stmt->bind_param('ssss', $activity, $segment, $segment, $segment);
    if (!$stmt->execute()) {
        $stmt->close();
        return '';
    }

    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return trim((string) ($row['totalRates'] ?? ''));
}
