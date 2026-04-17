<?php

function operations_normalize_entry_type($entryType): string
{
    return strtoupper(trim((string) $entryType));
}

function operations_is_blank($value): bool
{
    if ($value === null) {
        return true;
    }

    return trim((string) $value) === "";
}

function operations_required_fields_by_type(): array
{
    return [
        "CARGO TRUCK ENTRY" => [
            "segment" => "Segment",
            "activity" => "Activity",
            "waybill_date" => "Date",
            "waybill" => "Waybill",
            "truck" => "Truck",
            "driver" => "Driver",
            "customer_ph" => "Customer/PH",
            "outside" => "Outside",
            "compound" => "Compound",
            "total_trips" => "Total Trips",
            "operations" => "Operations",
            "deliver_from" => "Deliver From",
            "delivered_to" => "Deliver To",
            "cargo_date" => "Cargo Date",
        ],
        "DPC_KDs & OPM ENTRY" => [
            "segment" => "Segment",
            "activity" => "Activity",
            "waybill_date" => "Date",
            "waybill" => "Waybill",
            "evita_farmind" => "Evita/Farmind",
            "driver" => "Driver",
            "departure" => "Departure",
            "arrival" => "Arrival",
            "truck" => "Truck",
            "tr" => "Trailer",
            "ph" => "PH",
            "thirteen_body" => "13 Body",
            "thirteen_cover" => "13 Cover",
            "thirteen_pads" => "13 Pads",
            "eighteen_body" => "18 Body",
            "eighteen_cover" => "18 Cover",
            "eighteen_pads" => "18 Pads",
            "thirteen_total" => "13 Total",
            "eighteen_total" => "18 Total",
            "total_load" => "Total Load",
            "fgtr_no" => "FGTR's No.",
            "dpc_date" => "DPC Date",
        ],
        "OTHERS ENTRY" => [
            "segment" => "Segment",
            "activity" => "Activity",
            "waybill_date" => "Date",
            "waybill" => "Waybill",
            "truck" => "Truck",
            "driver" => "Driver",
            "tr" => "TR",
            "gs" => "GS",
            "operations_ph" => "Operations / PH",
            "load_quantity_weight" => "Load / Quantity / Weight",
            "unit_of_measure" => "Unit Of Measure",
            "deliver_from" => "Deliver From",
            "delivered_to" => "Deliver To",
        ],
        "RV ENTRY" => [
            "segment" => "Segment",
            "activity" => "Activity",
            "pullout_location_arrival_date" => "Pullout Arrival Date",
            "pullout_location_arrival_time" => "Pullout Arrival Time",
            "pullout_location_departure_date" => "Pullout Departure Date",
            "pullout_location_departure_time" => "Pullout Departure Time",
            "ph_arrival_date" => "PH Arrival Date",
            "ph_arrival_time" => "PH Arrival Time",
            "van_alpha" => "Van Alpha",
            "van_number" => "Van Number",
            "van_name" => "Van Name",
            "ph" => "PH",
            "shipper" => "Shipper",
            "ecs" => "ECS",
            "tr" => "TR",
            "gs" => "GS",
            "waybill" => "Waybill",
            "prime_mover" => "Prime Mover",
            "driver" => "Driver",
            "empty_pullout_location" => "Empty Pullout Location",
            "loaded_van_loading_start_date" => "Loading Start Date",
            "loaded_van_loading_start_time" => "Loading Start Time",
            "loaded_van_loading_finish_date" => "Loading Finish Date",
            "loaded_van_loading_finish_time" => "Loading Finish Time",
            "loaded_van_delivery_departure_date" => "Delivery Departure Date",
            "loaded_van_delivery_departure_time" => "Delivery Departure Time",
            "loaded_van_delivery_arrival_date" => "Delivery Arrival Date",
            "loaded_van_delivery_arrival_time" => "Delivery Arrival Time",
            "genset_shutoff_date" => "Genset Shutoff Date",
            "genset_shutoff_time" => "Genset Shutoff Time",
            "end_uploading_date" => "End Unloading Date",
            "end_uploading_time" => "End Unloading Time",
            "dr_no" => "DR No.",
            "load_description" => "Load",
            "delivered_by_prime_mover" => "Delivered By PM",
            "delivered_by_driver" => "Delivered By Driver",
            "delivered_to" => "Delivered To",
            "genset_hr_meter_start" => "HR Meter Start",
            "genset_hr_meter_end" => "HR Meter End",
            "genset_start_date" => "GS Start Date",
            "genset_start_time" => "GS Start Time",
            "genset_end_date" => "GS End Date",
            "genset_end_time" => "GS End Time",
        ],
    ];
}

function operations_route_by_type(): array
{
    return [
        "CARGO TRUCK ENTRY" => "cargoTruck",
        "DPC_KDs & OPM ENTRY" => "DPC_KDI",
        "OTHERS ENTRY" => "others",
        "RV ENTRY" => "rv",
    ];
}

function operations_missing_fields(array $row): array
{
    $requiredByType = operations_required_fields_by_type();
    $required = $requiredByType[
        operations_normalize_entry_type($row["entry_type"] ?? "")
    ] ?? [
        "segment" => "Segment",
        "activity" => "Activity",
        "waybill" => "Waybill",
    ];

    $missing = [];

    foreach ($required as $field => $label) {
        if (operations_is_blank($row[$field] ?? null)) {
            $missing[] = $label;
        }
    }

    return $missing;
}
