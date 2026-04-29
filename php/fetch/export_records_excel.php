<?php
include "../config/config.php";

$dateFrom = trim((string) ($_GET["date_from"] ?? ""));
$dateTo = trim((string) ($_GET["date_to"] ?? ""));
$entryType = trim((string) ($_GET["entry_type"] ?? ""));

if (
    $dateFrom === "" ||
    $dateTo === "" ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) ||
    $dateFrom > $dateTo
) {
    http_response_code(400);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Invalid date range.";
    exit();
}

if (!class_exists("ZipArchive")) {
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "ZipArchive is required to generate the Excel export.";
    exit();
}

$generatedAtDate = (new DateTimeImmutable("now", new DateTimeZone("Asia/Manila")))->format("Y-m-d");

function load_xml_document(string $xml): DOMDocument
{
    $document = new DOMDocument("1.0", "UTF-8");
    $document->preserveWhiteSpace = false;
    $document->formatOutput = false;
    $document->loadXML($xml);
    return $document;
}

function xml_xpath(DOMDocument $document): DOMXPath
{
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace("main", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
    $xpath->registerNamespace("rel", "http://schemas.openxmlformats.org/package/2006/relationships");
    return $xpath;
}

function excel_serial_date(?string $date): ?float
{
    if (!$date) {
        return null;
    }

    $date = trim($date);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || str_starts_with($date, '0000-')) {
        return null;
    }

    $timestamp = strtotime($date . " 00:00:00 UTC");
    if ($timestamp === false) {
        return null;
    }

    $serial = ($timestamp / 86400) + 25569;
    return $serial > 0 ? $serial : null;
}

function excel_serial_datetime(?string $date, ?string $time): ?float
{
    if (!$date) {
        return null;
    }

    $date = trim($date);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $date) || str_starts_with($date, '0000-')) {
        return null;
    }

    $timePart = trim((string) $time);
    $timestamp = strtotime($date . " " . ($timePart !== "" ? $timePart : "00:00:00"));
    if ($timestamp === false) {
        return null;
    }

    $serial = ($timestamp / 86400) + 25569;
    return $serial > 0 ? $serial : null;
}

function normalize_text($value): string
{
    return trim((string) ($value ?? ""));
}

function first_non_empty(...$values): string
{
    foreach ($values as $value) {
        $text = normalize_text($value);
        if ($text !== "") {
            return $text;
        }
    }

    return "";
}

function append_inline_string_cell(DOMDocument $document, DOMElement $row, string $reference, ?string $style, string $value): void
{
    $cell = $document->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "c");
    $cell->setAttribute("r", $reference);

    if ($style !== null && $style !== "") {
        $cell->setAttribute("s", $style);
    }

    $cell->setAttribute("t", "inlineStr");
    $inlineString = $document->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "is");
    $text = $document->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "t");
    $text->appendChild($document->createTextNode($value));
    $inlineString->appendChild($text);
    $cell->appendChild($inlineString);
    $row->appendChild($cell);
}

function append_numeric_cell(DOMDocument $document, DOMElement $row, string $reference, ?string $style, $value): void
{
    if ($value === null || $value === "") {
        return;
    }

    $cell = $document->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "c");
    $cell->setAttribute("r", $reference);

    if ($style !== null && $style !== "") {
        $cell->setAttribute("s", $style);
    }

    $valueNode = $document->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "v", rtrim(rtrim(number_format((float) $value, 10, ".", ""), "0"), "."));
    $cell->appendChild($valueNode);
    $row->appendChild($cell);
}

function append_cell(DOMDocument $document, DOMElement $row, string $reference, ?string $style, $value, string $type = "string"): void
{
    if ($type === "number" || $type === "date" || $type === "datetime") {
        append_numeric_cell($document, $row, $reference, $style, $value);
        return;
    }

    $text = normalize_text($value);
    if ($text === "") {
        return;
    }

    append_inline_string_cell($document, $row, $reference, $style, $text);
}

function column_index_from_letters(string $letters): int
{
    $letters = strtoupper($letters);
    $index = 0;

    for ($i = 0, $length = strlen($letters); $i < $length; $i++) {
        $index = ($index * 26) + (ord($letters[$i]) - 64);
    }

    return $index;
}

function export_column_labels(): array
{
    return [
        "A" => "Entry",
        "B" => "Transmittal DATE",
        "C" => "Waybill",
        "D" => "Transaction Date",
        "E" => "Date and Time of Withdrawal of Van",
        "F" => "Alpha",
        "G" => "Numeric",
        "H" => "Shipping Line",
        "I" => "PH",
        "J" => "Customer / Shipper",
        "K" => "TRIP RECEIPT MTY",
        "L" => "ECS",
        "M" => "TR",
        "N" => "GS",
        "O" => "PM",
        "P" => "Driver",
        "Q" => "Pull-Out Location",
        "R" => "Date & Time of Van Unloading",
        "S" => "TRIP RECEIPT FCL",
        "T" => "DR No./Fleet No/TCARD",
        "U" => "Load",
        "V" => "PM2",
        "W" => "Driver2",
        "X" => "Delivered To",
        "Y" => "No. Of Trips",
        "Z" => "Remarks",
        "AA" => "Standard Kms",
        "AB" => "SKUs",
        "AC" => "OUT",
        "AD" => "IN",
        "AE" => "Volume",
        "AF" => "Van Size",
        "AG" => "HR:MN",
        "AH" => "NO. OF HOURS",
        "AI" => "HOURS START",
        "AJ" => "HOURS END",
        "AK" => "START",
        "AL" => "END",
    ];
}

function build_auto_widths(array $rows): array
{
    $labels = export_column_labels();

    $widths = [];

    foreach ($labels as $column => $label) {
        $widths[$column] = strlen($label);
    }

    foreach ($rows as $row) {
        foreach ($row as $column => $cellData) {
            $type = $cellData["type"] ?? "string";
            $value = $cellData["value"] ?? "";

            if ($type === "date") {
                $display = $value === null ? "" : "00/00/0000";
            } elseif ($type === "datetime") {
                $display = $value === null ? "" : "00/00/0000 00:00";
            } elseif ($type === "number") {
                $display = $value === null ? "" : (string) $value;
            } else {
                $display = normalize_text($value);
            }

            $widths[$column] = max($widths[$column] ?? 0, strlen($display));
        }
    }

    foreach ($widths as $column => $length) {
        $widths[$column] = min(max($length + 2, 8), 40);
    }

    return $widths;
}

function ensure_modern_styles(string $stylesXml): array
{
    $document = load_xml_document($stylesXml);
    $xpath = xml_xpath($document);
    $namespace = "http://schemas.openxmlformats.org/spreadsheetml/2006/main";

    $fontsNode = $xpath->query("//main:fonts")->item(0);
    $fillsNode = $xpath->query("//main:fills")->item(0);
    $bordersNode = $xpath->query("//main:borders")->item(0);
    $cellXfsNode = $xpath->query("//main:cellXfs")->item(0);

    if (
        !$fontsNode instanceof DOMElement ||
        !$fillsNode instanceof DOMElement ||
        !$bordersNode instanceof DOMElement ||
        !$cellXfsNode instanceof DOMElement
    ) {
        return [
            "xml" => $stylesXml,
            "styles" => [
                "title" => "1",
                "meta" => "1",
                "header" => "7",
                "body" => "24",
                "body_center" => "24",
                "body_date" => "22",
                "body_datetime" => "23",
                "body_number" => "24",
            ],
        ];
    }

    $appendFont = static function (DOMDocument $doc, DOMElement $parent, array $definition) use ($namespace): int {
        $fontIndex = $parent->childNodes->length;
        $fontNode = $doc->createElementNS($namespace, "font");

        foreach ($definition as $key => $value) {
            if ($value === true) {
                $fontNode->appendChild($doc->createElementNS($namespace, $key));
                continue;
            }

            if (is_array($value)) {
                $child = $doc->createElementNS($namespace, $key);
                foreach ($value as $attribute => $attributeValue) {
                    $child->setAttribute($attribute, (string) $attributeValue);
                }
                $fontNode->appendChild($child);
                continue;
            }

            $child = $doc->createElementNS($namespace, $key);
            $child->setAttribute("val", (string) $value);
            $fontNode->appendChild($child);
        }

        $parent->appendChild($fontNode);
        $parent->setAttribute("count", (string) $parent->childNodes->length);
        return $fontIndex;
    };

    $appendFill = static function (DOMDocument $doc, DOMElement $parent, string $rgb) use ($namespace): int {
        $fillIndex = $parent->childNodes->length;
        $fillNode = $doc->createElementNS($namespace, "fill");
        $patternNode = $doc->createElementNS($namespace, "patternFill");
        $patternNode->setAttribute("patternType", "solid");
        $fgNode = $doc->createElementNS($namespace, "fgColor");
        $fgNode->setAttribute("rgb", $rgb);
        $bgNode = $doc->createElementNS($namespace, "bgColor");
        $bgNode->setAttribute("indexed", "64");
        $patternNode->appendChild($fgNode);
        $patternNode->appendChild($bgNode);
        $fillNode->appendChild($patternNode);
        $parent->appendChild($fillNode);
        $parent->setAttribute("count", (string) $parent->childNodes->length);
        return $fillIndex;
    };

    $appendBorder = static function (DOMDocument $doc, DOMElement $parent, string $rgb) use ($namespace): int {
        $borderIndex = $parent->childNodes->length;
        $borderNode = $doc->createElementNS($namespace, "border");

        foreach (["left", "right", "top", "bottom"] as $side) {
            $sideNode = $doc->createElementNS($namespace, $side);
            $sideNode->setAttribute("style", "thin");
            $colorNode = $doc->createElementNS($namespace, "color");
            $colorNode->setAttribute("rgb", $rgb);
            $sideNode->appendChild($colorNode);
            $borderNode->appendChild($sideNode);
        }

        $borderNode->appendChild($doc->createElementNS($namespace, "diagonal"));
        $parent->appendChild($borderNode);
        $parent->setAttribute("count", (string) $parent->childNodes->length);
        return $borderIndex;
    };

    $appendXf = static function (
        DOMDocument $doc,
        DOMElement $parent,
        int $fontId,
        int $fillId,
        int $borderId,
        int $numFmtId,
        array $alignment,
        bool $applyNumberFormat
    ) use ($namespace): int {
        $xfIndex = $parent->childNodes->length;
        $xfNode = $doc->createElementNS($namespace, "xf");
        $xfNode->setAttribute("numFmtId", (string) $numFmtId);
        $xfNode->setAttribute("fontId", (string) $fontId);
        $xfNode->setAttribute("fillId", (string) $fillId);
        $xfNode->setAttribute("borderId", (string) $borderId);
        $xfNode->setAttribute("xfId", "0");
        $xfNode->setAttribute("applyFont", "1");
        $xfNode->setAttribute("applyFill", "1");
        $xfNode->setAttribute("applyBorder", "1");
        $xfNode->setAttribute("applyAlignment", "1");

        if ($applyNumberFormat) {
            $xfNode->setAttribute("applyNumberFormat", "1");
        }

        $alignmentNode = $doc->createElementNS($namespace, "alignment");
        foreach ($alignment as $key => $value) {
            $alignmentNode->setAttribute($key, (string) $value);
        }
        $xfNode->appendChild($alignmentNode);

        $parent->appendChild($xfNode);
        $parent->setAttribute("count", (string) $parent->childNodes->length);
        return $xfIndex;
    };

    $titleFontId = $appendFont($document, $fontsNode, [
        "b" => true,
        "sz" => "16",
        "color" => ["rgb" => "FFFFFFFF"],
        "name" => "Aptos Display",
        "family" => "2",
    ]);

    $metaFontId = $appendFont($document, $fontsNode, [
        "b" => true,
        "sz" => "10",
        "color" => ["rgb" => "FF334155"],
        "name" => "Aptos",
        "family" => "2",
    ]);

    $headerFontId = $appendFont($document, $fontsNode, [
        "b" => true,
        "sz" => "10",
        "color" => ["rgb" => "FFFFFFFF"],
        "name" => "Aptos",
        "family" => "2",
    ]);

    $bodyFontId = $appendFont($document, $fontsNode, [
        "sz" => "10",
        "color" => ["rgb" => "FF0F172A"],
        "name" => "Aptos",
        "family" => "2",
    ]);

    $titleFillId = $appendFill($document, $fillsNode, "FF0F172A");
    $metaFillId = $appendFill($document, $fillsNode, "FFF8FAFC");
    $headerFillId = $appendFill($document, $fillsNode, "FF2563EB");
    $bodyFillId = $appendFill($document, $fillsNode, "FFFFFFFF");

    $borderId = $appendBorder($document, $bordersNode, "FFE2E8F0");

    $titleStyleId = $appendXf($document, $cellXfsNode, $titleFontId, $titleFillId, $borderId, 0, [
        "horizontal" => "left",
        "vertical" => "center",
        "wrapText" => "1",
    ], false);

    $metaStyleId = $appendXf($document, $cellXfsNode, $metaFontId, $metaFillId, $borderId, 0, [
        "horizontal" => "left",
        "vertical" => "center",
        "wrapText" => "1",
    ], false);

    $headerStyleId = $appendXf($document, $cellXfsNode, $headerFontId, $headerFillId, $borderId, 0, [
        "horizontal" => "center",
        "vertical" => "center",
        "wrapText" => "1",
    ], false);

    $bodyStyleId = $appendXf($document, $cellXfsNode, $bodyFontId, $bodyFillId, $borderId, 0, [
        "horizontal" => "left",
        "vertical" => "center",
        "wrapText" => "1",
    ], false);

    $bodyCenterStyleId = $appendXf($document, $cellXfsNode, $bodyFontId, $bodyFillId, $borderId, 0, [
        "horizontal" => "center",
        "vertical" => "center",
        "wrapText" => "1",
    ], false);

    $bodyDateStyleId = $appendXf($document, $cellXfsNode, $bodyFontId, $bodyFillId, $borderId, 164, [
        "horizontal" => "center",
        "vertical" => "center",
        "wrapText" => "1",
    ], true);

    $bodyDateTimeStyleId = $appendXf($document, $cellXfsNode, $bodyFontId, $bodyFillId, $borderId, 167, [
        "horizontal" => "center",
        "vertical" => "center",
        "wrapText" => "1",
    ], true);

    $bodyNumberStyleId = $appendXf($document, $cellXfsNode, $bodyFontId, $bodyFillId, $borderId, 0, [
        "horizontal" => "center",
        "vertical" => "center",
        "wrapText" => "1",
    ], false);

    return [
        "xml" => $document->saveXML(),
        "styles" => [
            "title" => (string) $titleStyleId,
            "meta" => (string) $metaStyleId,
            "header" => (string) $headerStyleId,
            "body" => (string) $bodyStyleId,
            "body_center" => (string) $bodyCenterStyleId,
            "body_date" => (string) $bodyDateStyleId,
            "body_datetime" => (string) $bodyDateTimeStyleId,
            "body_number" => (string) $bodyNumberStyleId,
        ],
    ];
}

$params = [$dateFrom, $dateTo];
$types = "ss";
$entryTypeSql = "";
$entryTypeLabel = "ALL";

if ($entryType !== "" && strtoupper($entryType) !== "ALL") {
    $entryTypeSql = " AND entry_type = ?";
    $params[] = $entryType;
    $types .= "s";
    $entryTypeLabel = preg_replace('/[^A-Za-z0-9_-]+/', '_', $entryType);
}

$sql = "SELECT
    entry_id,
    entry_type,
    customer_ph,
    ph,
    operations_ph,
    shipper,
    waybill,
    waybill_empty,
    waybill_date,
    van_alpha,
    van_number,
    van_name,
    ecs,
    tr,
    tr2,
    gs,
    prime_mover,
    truck,
    truck2,
    driver,
    driver_return,
    empty_pullout_location,
    pullout_location,
    pullout_date,
    eir_outDate,
    eir_outTime,
    date_hauled,
    date_unloaded,
    date_returned,
    departure_time,
    arrival_time,
    segment,
    activity,
    deliver_from,
    pullout_location_departure_date,
    pullout_location_departure_time,
    loaded_van_delivery_arrival_date,
    loaded_van_delivery_arrival_time,
    dr_no,
    slp_no,
    load_description,
    delivered_to,
    total_trips,
    remarks,
    delivered_remarks,
    kms,
    billing_sku,
    ph_departure_date,
    ph_departure_time,
    wharf_arrival_date,
    wharf_arrival_time,
    load_quantity_weight,
    unit_of_measure,
    total_load,
    size,
    destination,
    genset_hr_meter_start,
    genset_hr_meter_end,
    genset_start_date,
    genset_start_time,
    genset_end_date,
    genset_end_time,
    created_date,
    modified_date
FROM operations
WHERE DATE(created_date) BETWEEN ? AND ?" . $entryTypeSql . "
ORDER BY created_date DESC, entry_id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Failed to prepare export.";
    exit();
}

$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    $stmt->close();
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Failed to generate export.";
    exit();
}

$result = $stmt->get_result();
$rows = [];

while ($row = $result->fetch_assoc()) {
    $remarks = first_non_empty($row["remarks"] ?? "", $row["delivered_remarks"] ?? "");
    $entryTypeValue = normalize_text($row["entry_type"] ?? "");
    $isDryVan = $entryTypeValue === "DRY VAN ENTRY";
    $isOthers = $entryTypeValue === "OTHERS ENTRY";

    $customerOrShipper = first_non_empty(
        $row["customer_ph"] ?? "",
        $row["shipper"] ?? "",
        $row["operations_ph"] ?? ""
    );

    if ($entryTypeValue === "DPC_KDs & OPM ENTRY") {
        $customerOrShipper = "DPC";
    }

    // Column D — Transaction Date
    if ($isDryVan) {
        $transactionDate = excel_serial_date(normalize_text($row["date_hauled"] ?? ""));
    } elseif ($isOthers) {
        $transactionDate = excel_serial_date(normalize_text($row["waybill_date"] ?? ""));
    } else {
        $transactionDate = excel_serial_date(normalize_text($row["genset_end_date"] ?? $row["waybill_date"] ?? ""));
    }

    // Column E — Date and Time of Withdrawal of Van
    if ($isDryVan) {
        $withdrawalDateTime = excel_serial_datetime(
            normalize_text($row["eir_outDate"] ?? ""),
            normalize_text($row["eir_outTime"] ?? "")
        );
    } elseif ($isOthers) {
        $withdrawalDateTime = excel_serial_datetime(normalize_text($row["waybill_date"] ?? ""), "");
    } else {
        $withdrawalDateTime = excel_serial_datetime(
            first_non_empty($row["pullout_location_departure_date"] ?? "", substr((string) ($row["created_date"] ?? ""), 0, 10), $row["waybill_date"] ?? ""),
            first_non_empty($row["pullout_location_departure_time"] ?? "", substr((string) ($row["created_date"] ?? ""), 11, 8))
        );
    }

    // Column O — PM / Prime Mover
    $pmValue = ($isDryVan || $isOthers)
        ? first_non_empty($row["truck"] ?? "")
        : first_non_empty($row["prime_mover"] ?? "", $row["truck"] ?? "");

    // Column Q — Pull-Out Location
    if ($isDryVan) {
        $pulloutLocation = $row["pullout_location"] ?? "";
    } elseif ($isOthers) {
        $pulloutLocation = $row["deliver_from"] ?? "";
    } else {
        $pulloutLocation = $row["empty_pullout_location"] ?? "";
    }

    // Column R — Date & Time of Van Unloading
    if ($isDryVan) {
        $unloadingDateTime = excel_serial_datetime(
            normalize_text($row["date_unloaded"] ?? ""),
            normalize_text($row["arrival_time"] ?? "")
        );
    } else {
        $unloadingDateTime = excel_serial_datetime(
            $row["ph_departure_date"] ?? "",
            $row["ph_departure_time"] ?? ""
        );
    }

    // Column T — DR No. / SLP No.
    $drValue = $isDryVan ? ($row["slp_no"] ?? "") : ($row["dr_no"] ?? "");

    // Column U — Load
    if ($isDryVan) {
        $loadValue = $row["destination"] ?? "";
    } elseif ($isOthers) {
        $qty = normalize_text($row["load_quantity_weight"] ?? "");
        $uom = normalize_text($row["unit_of_measure"] ?? "");
        $loadValue = $uom !== "" ? trim($qty . " " . $uom) : $qty;
    } else {
        $loadValue = first_non_empty($row["load_description"] ?? "", $row["total_load"] ?? "");
    }

    // Column AF — Van Size
    $vanSize = $isDryVan ? ($row["size"] ?? "") : "";

    $rows[] = [
        "A" => ["value" => $row["entry_type"] ?? "-", "type" => "string"],
        "B" => ["value" => excel_serial_date($generatedAtDate), "type" => "date"],
        "C" => ["value" => first_non_empty($row["waybill"] ?? "-"), "type" => "string"],
        "D" => ["value" => $transactionDate, "type" => "date"],
        "E" => ["value" => $withdrawalDateTime, "type" => "datetime"],
        "F" => ["value" => $row["van_alpha"] ?? "-", "type" => "string"],
        "G" => ["value" => $row["van_number"] ?? "-", "type" => "string"],
        "H" => ["value" => $row["van_name"] ?? "-", "type" => "string"],
        "I" => ["value" => $row["ph"] ?? "-", "type" => "string"],
        "J" => ["value" => $customerOrShipper ?? "-", "type" => "string"],
        "K" => ["value" => first_non_empty($row["waybill_empty"] ?? "-"), "type" => "string"],
        "L" => ["value" => $row["ecs"] ?? "-", "type" => "string"],
        "M" => ["value" => $row["tr"] ?? "-", "type" => "string"],
        "N" => ["value" => $row["gs"] ?? "-", "type" => "string"],
        "O" => ["value" => $pmValue, "type" => "string"],
        "P" => ["value" => $row["driver"] ?? "-", "type" => "string"],
        "Q" => ["value" => $pulloutLocation, "type" => "string"],
        "R" => ["value" => $unloadingDateTime, "type" => "datetime"],
        "S" => ["value" => $row["waybill"] ?? "-", "type" => "string"],
        "T" => ["value" => $drValue, "type" => "string"],
        "U" => ["value" => $loadValue, "type" => "string"],
        "V" => ["value" => $row["truck2"] ?? "-", "type" => "string"],
        "W" => ["value" => $row["driver_return"] ?? "-", "type" => "string"],
        "X" => ["value" => $row["delivered_to"] ?? "-", "type" => "string"],
        "Y" => ["value" => $row["total_trips"] ?? null, "type" => "number"],
        "Z" => ["value" => $remarks ?? "-", "type" => "string"],
        "AA" => ["value" => $row["kms"] ?? "-", "type" => "string"],
        "AB" => ["value" => $row["billing_sku"] ?? "-", "type" => "string"],
        "AC" => ["value" => "-", "type" => "string"],
        "AD" => ["value" => "-", "type" => "string"],
        "AE" => ["value" => "-", "type" => "string"],
        "AF" => ["value" => $vanSize, "type" => "string"],
        "AG" => ["value" => "-", "type" => "string"],
        "AH" => ["value" => "-", "type" => "string"],
        "AI" => ["value" => $row["genset_hr_meter_start"] ?? null, "type" => "number"],
        "AJ" => ["value" => $row["genset_hr_meter_end"] ?? null, "type" => "number"],
        "AK" => ["value" => excel_serial_datetime($row["genset_start_date"] ?? "", $row["genset_start_time"] ?? ""), "type" => "datetime"],
        "AL" => ["value" => excel_serial_datetime($row["genset_end_date"] ?? "", $row["genset_end_time"] ?? ""), "type" => "datetime"],
    ];
}

$stmt->close();

$templatePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "records-template.xlsx";
if (!is_file($templatePath)) {
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Template file not found.";
    exit();
}

$temporaryPath = tempnam(sys_get_temp_dir(), "records_export_");
if ($temporaryPath === false) {
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Failed to create temporary export file.";
    exit();
}

$exportPath = $temporaryPath . ".xlsx";
@unlink($exportPath);

if (!copy($templatePath, $exportPath)) {
    @unlink($temporaryPath);
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Failed to prepare template export.";
    exit();
}

@unlink($temporaryPath);

$zip = new ZipArchive();
if ($zip->open($exportPath) !== true) {
    @unlink($exportPath);
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Failed to open template export.";
    exit();
}

$sheetXml = $zip->getFromName("xl/worksheets/sheet1.xml");
$stylesXml = $zip->getFromName("xl/styles.xml");
$workbookXml = $zip->getFromName("xl/workbook.xml");
$workbookRelsXml = $zip->getFromName("xl/_rels/workbook.xml.rels");

if ($sheetXml === false || $stylesXml === false || $workbookXml === false || $workbookRelsXml === false) {
    $zip->close();
    @unlink($exportPath);
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Template structure is invalid.";
    exit();
}

$sheetDocument = load_xml_document($sheetXml);
$sheetXpath = xml_xpath($sheetDocument);
$sheetData = $sheetXpath->query("//main:sheetData")->item(0);
$styleBundle = ensure_modern_styles($stylesXml);
$styleMap = $styleBundle["styles"];

if (!$sheetData instanceof DOMElement) {
    $zip->close();
    @unlink($exportPath);
    http_response_code(500);
    header("Content-Type: text/plain; charset=utf-8");
    echo "Template worksheet is invalid.";
    exit();
}

$rowsToRemove = [];
foreach ($sheetXpath->query("//main:sheetData/main:row") as $rowNode) {
    if (!$rowNode instanceof DOMElement) {
        continue;
    }

    if ((int) $rowNode->getAttribute("r") >= 4) {
        $rowsToRemove[] = $rowNode;
    }
}

foreach ($rowsToRemove as $rowNode) {
    $sheetData->removeChild($rowNode);
}

$labels = export_column_labels();
$columnWidths = build_auto_widths($rows);
$existingCols = $sheetXpath->query("//main:cols")->item(0);
if ($existingCols instanceof DOMElement) {
    $existingCols->parentNode?->removeChild($existingCols);
}

$sheetFormatPr = $sheetXpath->query("//main:sheetFormatPr")->item(0);
$colsNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "cols");
foreach ($columnWidths as $column => $width) {
    $columnIndex = column_index_from_letters($column);
    $colNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "col");
    $colNode->setAttribute("min", (string) $columnIndex);
    $colNode->setAttribute("max", (string) $columnIndex);
    $colNode->setAttribute("width", number_format($width, 2, ".", ""));
    $colNode->setAttribute("bestFit", "1");
    $colNode->setAttribute("customWidth", "1");
    $colsNode->appendChild($colNode);
}

if ($sheetFormatPr instanceof DOMElement && $sheetFormatPr->parentNode instanceof DOMNode) {
    $sheetFormatPr->parentNode->insertBefore($colsNode, $sheetData);
}

$sheetView = $sheetXpath->query("//main:sheetViews/main:sheetView")->item(0);
if ($sheetView instanceof DOMElement) {
    while ($sheetView->firstChild) {
        $sheetView->removeChild($sheetView->firstChild);
    }

    $paneNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "pane");
    $paneNode->setAttribute("ySplit", "3");
    $paneNode->setAttribute("topLeftCell", "A4");
    $paneNode->setAttribute("activePane", "bottomLeft");
    $paneNode->setAttribute("state", "frozen");
    $sheetView->appendChild($paneNode);

    $selectionNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "selection");
    $selectionNode->setAttribute("pane", "bottomLeft");
    $selectionNode->setAttribute("activeCell", "A4");
    $selectionNode->setAttribute("sqref", "A4");
    $sheetView->appendChild($selectionNode);
}

$titleRow = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "row");
$titleRow->setAttribute("r", "1");
$titleRow->setAttribute("spans", "1:38");
$titleRow->setAttribute("customFormat", "1");
$titleRow->setAttribute("ht", "28");
$titleRow->setAttribute("customHeight", "1");
$titleRow->setAttributeNS("http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac", "x14ac:dyDescent", "0.3");
append_cell($sheetDocument, $titleRow, "A1", $styleMap["title"], "Operations Records Export", "string");
$sheetData->appendChild($titleRow);

$metaRow = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "row");
$metaRow->setAttribute("r", "2");
$metaRow->setAttribute("spans", "1:38");
$metaRow->setAttribute("customFormat", "1");
$metaRow->setAttribute("ht", "20");
$metaRow->setAttribute("customHeight", "1");
$metaRow->setAttributeNS("http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac", "x14ac:dyDescent", "0.3");
$entryTypeDisplay = strtoupper($entryType) === "ALL" || $entryType === "" ? "All Entries" : $entryType;
$metaText = sprintf(
    "Date Range: %s to %s   |   Entry Type: %s   |   Generated: %s",
    $dateFrom,
    $dateTo,
    $entryTypeDisplay,
    $generatedAtDate
);
append_cell($sheetDocument, $metaRow, "A2", $styleMap["meta"], $metaText, "string");
$sheetData->appendChild($metaRow);

$headerRow = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "row");
$headerRow->setAttribute("r", "3");
$headerRow->setAttribute("spans", "1:38");
$headerRow->setAttribute("customFormat", "1");
$headerRow->setAttribute("ht", "28");
$headerRow->setAttribute("customHeight", "1");
$headerRow->setAttributeNS("http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac", "x14ac:dyDescent", "0.3");
foreach ($labels as $column => $label) {
    append_cell($sheetDocument, $headerRow, $column . "3", $styleMap["header"], $label, "string");
}
$sheetData->appendChild($headerRow);

$centeredColumns = ["A", "C", "F", "G", "H", "I", "K", "L", "M", "N", "O", "P", "R", "S", "V", "W", "Y", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH"];
$dateColumns = ["B", "D"];
$dateTimeColumns = ["E", "R", "AK", "AL"];
$numberColumns = ["Y", "AI", "AJ"];

$lastRowNumber = 3;
foreach ($rows as $index => $rowData) {
    $rowNumber = 4 + $index;
    $lastRowNumber = $rowNumber;

    $rowNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "row");
    $rowNode->setAttribute("r", (string) $rowNumber);
    $rowNode->setAttribute("spans", "1:38");
    $rowNode->setAttribute("customFormat", "1");
    $rowNode->setAttribute("ht", "20");
    $rowNode->setAttribute("customHeight", "1");
    $rowNode->setAttributeNS("http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac", "x14ac:dyDescent", "0.3");

    foreach ($rowData as $column => $cellData) {
        if (in_array($column, $dateColumns, true)) {
            $style = $styleMap["body_date"];
        } elseif (in_array($column, $dateTimeColumns, true)) {
            $style = $styleMap["body_datetime"];
        } elseif (in_array($column, $numberColumns, true)) {
            $style = $styleMap["body_number"];
        } elseif (in_array($column, $centeredColumns, true)) {
            $style = $styleMap["body_center"];
        } else {
            $style = $styleMap["body"];
        }

        append_cell(
            $sheetDocument,
            $rowNode,
            $column . $rowNumber,
            $style,
            $cellData["value"] ?? "",
            $cellData["type"] ?? "string"
        );
    }

    $sheetData->appendChild($rowNode);
}

$dimensionNode = $sheetXpath->query("//main:dimension")->item(0);
if ($dimensionNode instanceof DOMElement) {
    $dimensionNode->setAttribute("ref", "A1:AL" . max($lastRowNumber, 3));
}

foreach ($sheetXpath->query("//main:mergeCells") as $mergeCellsNode) {
    $mergeCellsNode->parentNode?->removeChild($mergeCellsNode);
}

foreach ($sheetXpath->query("//main:conditionalFormatting") as $conditionalNode) {
    $conditionalNode->parentNode?->removeChild($conditionalNode);
}

foreach ($sheetXpath->query("//main:autoFilter") as $autoFilterNode) {
    $autoFilterNode->parentNode?->removeChild($autoFilterNode);
}

$pageMarginsNode = $sheetXpath->query("//main:pageMargins")->item(0);
$autoFilterNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "autoFilter");
$autoFilterNode->setAttribute("ref", "A3:AL" . max($lastRowNumber, 3));
if ($pageMarginsNode instanceof DOMElement && $pageMarginsNode->parentNode instanceof DOMNode) {
    $pageMarginsNode->parentNode->insertBefore($autoFilterNode, $pageMarginsNode);
}

$mergeCellsNode = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "mergeCells");
$mergeCellsNode->setAttribute("count", "2");
$mergeOne = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "mergeCell");
$mergeOne->setAttribute("ref", "A1:AL1");
$mergeTwo = $sheetDocument->createElementNS("http://schemas.openxmlformats.org/spreadsheetml/2006/main", "mergeCell");
$mergeTwo->setAttribute("ref", "A2:AL2");
$mergeCellsNode->appendChild($mergeOne);
$mergeCellsNode->appendChild($mergeTwo);
if ($pageMarginsNode instanceof DOMElement && $pageMarginsNode->parentNode instanceof DOMNode) {
    $pageMarginsNode->parentNode->insertBefore($mergeCellsNode, $pageMarginsNode);
}

$workbookDocument = load_xml_document($workbookXml);
$workbookXpath = xml_xpath($workbookDocument);
foreach ($workbookXpath->query("//main:externalReferences") as $node) {
    $node->parentNode?->removeChild($node);
}

$workbookRelsDocument = load_xml_document($workbookRelsXml);
$workbookRelsXpath = xml_xpath($workbookRelsDocument);
foreach ($workbookRelsXpath->query('//rel:Relationship[contains(@Type, "externalLink") or contains(@Type, "calcChain")]') as $relationship) {
    $relationship->parentNode?->removeChild($relationship);
}

$zip->addFromString("xl/worksheets/sheet1.xml", $sheetDocument->saveXML());
$zip->addFromString("xl/styles.xml", $styleBundle["xml"]);
$zip->addFromString("xl/workbook.xml", $workbookDocument->saveXML());
$zip->addFromString("xl/_rels/workbook.xml.rels", $workbookRelsDocument->saveXML());
$zip->close();

$filename = sprintf("records_%s_%s_%s.xlsx", $entryTypeLabel, $dateFrom, $dateTo);

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Content-Length: " . (string) filesize($exportPath));
header("Cache-Control: max-age=0");

readfile($exportPath);
@unlink($exportPath);
exit();
?>
