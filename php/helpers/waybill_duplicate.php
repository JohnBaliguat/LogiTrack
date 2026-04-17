<?php

/**
 * True if another operations row already uses this waybill (non-empty).
 * On update, pass $excludeEntryId so the current row is ignored.
 */
function operations_waybill_exists(mysqli $conn, string $waybill, ?int $excludeEntryId = null): bool
{
    $waybill = trim($waybill);
    if ($waybill === '') {
        return false;
    }

    if ($excludeEntryId !== null && $excludeEntryId > 0) {
        $stmt = $conn->prepare('SELECT 1 FROM operations WHERE waybill = ? AND entry_id != ? LIMIT 1');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('si', $waybill, $excludeEntryId);
    } else {
        $stmt = $conn->prepare('SELECT 1 FROM operations WHERE waybill = ? LIMIT 1');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('s', $waybill);
    }

    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    return $exists;
}
