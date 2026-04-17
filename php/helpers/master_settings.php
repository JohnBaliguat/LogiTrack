<?php

function master_settings_config(): array
{
    return [
        "location" => [
            "table" => "location",
            "primary_key" => "location_id",
            "label" => "Location",
            "fields" => [
                "location_name" => [
                    "label" => "Location Name",
                    "required" => true,
                ],
                "latitude" => ["label" => "Latitude", "required" => false],
                "longitude" => ["label" => "Longitude", "required" => false],
            ],
            "default_sort" => "location_name ASC",
        ],
        "trailer" => [
            "table" => "trailer",
            "primary_key" => "trailer_id",
            "label" => "Trailer",
            "fields" => [
                "trailer_name" => [
                    "label" => "Trailer Name",
                    "required" => true,
                ],
            ],
            "default_sort" => "trailer_name ASC",
        ],
        "trip_rates" => [
            "table" => "trip_rates",
            "primary_key" => "id",
            "label" => "Trip Rate",
            "fields" => [
                "segment" => ["label" => "Segment", "required" => true],
                "activity" => ["label" => "Activity", "required" => true],
                "baseRate" => ["label" => "Base Rate", "required" => false],
                "additional" => ["label" => "Additional", "required" => false],
                "totalRates" => ["label" => "Total Rates", "required" => false],
            ],
            "default_sort" => "segment ASC, activity ASC",
        ],
        "units" => [
            "table" => "units",
            "primary_key" => "unit_id",
            "label" => "Unit",
            "fields" => [
                "unit_name" => ["label" => "Unit Name", "required" => true],
                "unit_std" => ["label" => "Unit STD", "required" => false],
                "unit_model" => ["label" => "Unit Model", "required" => false],
                "unit_cluster" => [
                    "label" => "Unit Cluster",
                    "required" => false,
                ],
            ],
            "default_sort" => "unit_name ASC",
        ],
    ];
}

function master_settings_entity(string $entity): ?array
{
    $config = master_settings_config();
    return $config[$entity] ?? null;
}

function master_settings_clean_value($value): string
{
    return trim((string) ($value ?? ""));
}

function master_settings_decimal_value($value): float
{
    $clean = master_settings_clean_value($value);
    if ($clean === "" || !is_numeric($clean)) {
        return 0.0;
    }

    return (float) $clean;
}

function master_settings_format_decimal(float $value): string
{
    $formatted = number_format($value, 2, ".", "");
    return rtrim(rtrim($formatted, "0"), ".");
}

function master_settings_validate_payload(string $entity, array $payload): array
{
    $definition = master_settings_entity($entity);

    if ($definition === null) {
        return [
            "valid" => false,
            "message" => "Invalid settings entity.",
            "values" => [],
        ];
    }

    $values = [];

    foreach ($definition["fields"] as $field => $meta) {
        $value = master_settings_clean_value($payload[$field] ?? "");
        if (!empty($meta["required"]) && $value === "") {
            return [
                "valid" => false,
                "message" => $meta["label"] . " is required.",
                "values" => [],
            ];
        }
        $values[$field] = $value;
    }

    if ($entity === "trip_rates") {
        $baseRate = master_settings_decimal_value($values["baseRate"] ?? 0);
        $additional = master_settings_decimal_value($values["additional"] ?? 0);
        $values["baseRate"] = master_settings_format_decimal($baseRate);
        $values["additional"] = master_settings_format_decimal($additional);
        $values["totalRates"] = master_settings_format_decimal(
            $baseRate + $additional,
        );
    }

    return ["valid" => true, "message" => "", "values" => $values];
}
