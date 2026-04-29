<?php

/**
 * Convert 0-based column index to Excel column letter(s): 0→A, 25→Z, 26→AA, …
 */
function xlsx_col_name(int $idx): string {
    $col = '';
    $n = $idx + 1;
    while ($n > 0) {
        $n--;
        $col = chr(65 + ($n % 26)) . $col;
        $n = intdiv($n, 26);
    }
    return $col;
}

/**
 * Build a minimal .xlsx binary with a single header row (bold, row 1 frozen).
 * Uses only ZipArchive + built-in string functions — no external library.
 */
function xlsx_create(array $headers): string {
    $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
    @unlink($tmpFile);

    $zip = new ZipArchive();
    if ($zip->open($tmpFile, ZipArchive::CREATE) !== true) {
        return '';
    }

    // Shared strings (one entry per header)
    $ssXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $ssXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' count="' . count($headers) . '" uniqueCount="' . count($headers) . '">';
    foreach ($headers as $h) {
        $ssXml .= '<si><t xml:space="preserve">'
               . htmlspecialchars((string)$h, ENT_XML1 | ENT_COMPAT, 'UTF-8')
               . '</t></si>';
    }
    $ssXml .= '</sst>';

    // Styles — index 0: normal, index 1: bold
    $stylesXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $stylesXml .= '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
    $stylesXml .= '<fonts count="2">'
               . '<font><sz val="11"/><name val="Calibri"/></font>'
               . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
               . '</fonts>';
    $stylesXml .= '<fills count="2">'
               . '<fill><patternFill patternType="none"/></fill>'
               . '<fill><patternFill patternType="gray125"/></fill>'
               . '</fills>';
    $stylesXml .= '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>';
    $stylesXml .= '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>';
    $stylesXml .= '<cellXfs count="2">'
               . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
               . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
               . '</cellXfs>';
    $stylesXml .= '</styleSheet>';

    // Worksheet — row 1 frozen, headers as shared strings with bold style
    $sheetXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    $sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
    $sheetXml .= '<sheetViews><sheetView tabSelected="1" workbookViewId="0">'
              . '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
              . '</sheetView></sheetViews>';
    $sheetXml .= '<sheetData><row r="1">';
    foreach ($headers as $i => $h) {
        $col = xlsx_col_name($i);
        $sheetXml .= '<c r="' . $col . '1" t="s" s="1"><v>' . $i . '</v></c>';
    }
    $sheetXml .= '</row></sheetData></worksheet>';

    // Package files
    $zip->addFromString('[Content_Types].xml',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
        '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
        '<Default Extension="xml" ContentType="application/xml"/>' .
        '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
        '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
        '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>' .
        '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
        '</Types>');

    $zip->addFromString('_rels/.rels',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
        '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
        '</Relationships>');

    $zip->addFromString('xl/workbook.xml',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
        '<bookViews><workbookView xWindow="0" yWindow="0" windowWidth="16384" windowHeight="8192"/></bookViews>' .
        '<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>' .
        '</workbook>');

    $zip->addFromString('xl/_rels/workbook.xml.rels',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
        '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
        '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>' .
        '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
        '</Relationships>');

    $zip->addFromString('xl/sharedStrings.xml', $ssXml);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->addFromString('xl/styles.xml', $stylesXml);

    $zip->close();

    $content = file_get_contents($tmpFile);
    unlink($tmpFile);
    return (string) $content;
}

/**
 * Read all rows from an .xlsx file.
 * Returns array of rows; each row is a dense array of string values.
 * Numeric cells are returned as their raw string representation.
 * Excel date serials and time fractions are left as numbers for the caller to interpret.
 */
function xlsx_read_rows(string $filepath): array {
    $zip = new ZipArchive();
    if ($zip->open($filepath) !== true) {
        return [];
    }

    // Shared strings table
    $sharedStrings = [];
    $ssRaw = $zip->getFromName('xl/sharedStrings.xml');
    if ($ssRaw !== false) {
        $ss = @simplexml_load_string($ssRaw);
        if ($ss !== false) {
            foreach ($ss->si as $si) {
                if (count($si->r) > 0) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = (string) $si->t;
                }
            }
        }
    }

    // Resolve path to the first worksheet via workbook relationships
    $sheetPath = 'xl/worksheets/sheet1.xml';
    $wbRelsRaw = $zip->getFromName('xl/_rels/workbook.xml.rels');
    if ($wbRelsRaw !== false) {
        $wbRels = @simplexml_load_string($wbRelsRaw);
        if ($wbRels !== false) {
            foreach ($wbRels->Relationship as $rel) {
                if (substr((string)$rel['Type'], -9) === 'worksheet') {
                    $target = (string) $rel['Target'];
                    $sheetPath = (strpos($target, '/') === false) ? 'xl/' . $target : ltrim($target, '/');
                    break;
                }
            }
        }
    }

    $sheetRaw = $zip->getFromName($sheetPath);
    $zip->close();

    if ($sheetRaw === false) {
        return [];
    }

    $sheet = @simplexml_load_string($sheetRaw);
    if ($sheet === false) {
        return [];
    }

    $rows = [];

    foreach ($sheet->sheetData->row as $xmlRow) {
        $cells = [];
        $maxCol = -1;

        foreach ($xmlRow->c as $cell) {
            $ref = (string) $cell['r'];
            if (!preg_match('/^([A-Z]+)(\d+)$/', $ref, $m)) continue;

            // Column letters → 0-based index
            $colIdx = 0;
            for ($i = 0, $len = strlen($m[1]); $i < $len; $i++) {
                $colIdx = $colIdx * 26 + (ord($m[1][$i]) - 64);
            }
            $colIdx--;

            $type  = (string) $cell['t'];
            $value = '';

            if ($type === 's') {
                $value = $sharedStrings[intval((string) $cell->v)] ?? '';
            } elseif ($type === 'inlineStr') {
                $value = (string) $cell->is->t;
            } elseif ($type === 'str') {
                $value = (string) $cell->v;
            } else {
                $value = (string) $cell->v;
            }

            $cells[$colIdx] = $value;
            if ($colIdx > $maxCol) $maxCol = $colIdx;
        }

        $rowData = [];
        for ($i = 0; $i <= $maxCol; $i++) {
            $rowData[] = $cells[$i] ?? '';
        }
        $rows[] = $rowData;
    }

    return $rows;
}
