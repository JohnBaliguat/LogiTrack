<?php 
include "php/session-check.php"; 
include "php/config/config.php";

$segment_filter = "TDCRV";
$query = "SELECT entry_id, entry_type, segment_empty, activity_empty, segment, activity, remarks, pullout_location_arrival_date, pullout_location_arrival_time, pullout_location_departure_date, pullout_location_departure_time, ph_arrival_date, ph_arrival_time, van_alpha, van_number, van_name, ph, shipper, ecs, tr, gs, waybill, waybill_empty, prime_mover, driver, empty_pullout_location, loaded_van_loading_start_date, loaded_van_loading_start_time, loaded_van_loading_finish_date, loaded_van_loading_finish_time, loaded_van_delivery_departure_date, loaded_van_delivery_departure_time, loaded_van_delivery_arrival_date, loaded_van_delivery_arrival_time, genset_shutoff_date, genset_shutoff_time, end_uploading_date, end_uploading_time, dr_no, load_description, delivered_by_prime_mover, delivered_by_driver, delivered_to, delivered_remarks, genset_hr_meter_start, genset_hr_meter_end, genset_start_date, genset_start_time, genset_end_date, genset_end_time FROM operations WHERE entry_type = 'RV ENTRY' AND segment = ? AND DATE(created_date) = CURDATE() ORDER BY entry_id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $segment_filter);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDC RV Data Entry - DataEncode System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/alert/node_modules/sweetalert2/dist/sweetalert2.min.css">
    
</head>

<body>
    <div class="wrapper">
        <?php include "sidebar.php"; ?>

        <div id="content">

            <?php include "navbar.php"; ?>

            <div class="main-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="content-header mb-4">
                            <h2 class="fw-bold">TDC RV Data Entry</h2>
                            <p class="text-muted">Create, view, and manage TDC RV segment entries efficiently.</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group" aria-label="Basic outlined example">
                            <a class="btn btn-outline-secondary" href="abcrv">ABC RV</a>
                            <a class="btn btn-outline-secondary" href="doleRv">Dole RV</a>
                            <a class="btn btn-outline-secondary" href="sumiRv">Sumi RV</a>
                            <a class="btn btn-outline-secondary" href="tdcRv">TDC RV</a>
                            <a class="btn btn-outline-secondary" href="others">Others</a>
                            <a class="btn btn-outline-secondary" href="DPC_KDI">DPC_KDI & OPM</a>
                            <a class="btn btn-outline-secondary" href="cargoTruck">Cargo Truck</a>
                        </div>
                    </div>
                </div>


                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>New TDC RV Entry</h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#entryForm">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse" id="entryForm">
                        <div class="card-body">
                            <form id="dataEntryForm">
                                <input type="hidden" id="data_id" name="data_id" value="" />
                                <div class="row g-3">
                                    <h5>EMPTY CONTAINER VAN</h5>
                                    <div class="col-md-6" hidden>
                                        <div class="mb-3 position-relative">
                                            <label for="segment_empty" class="form-label">Segment</label>
                                            <input type="text" class="form-control" id="segment_empty" name="segment_empty" value="TDCRV" required>
                                            <ul id="segmentEmptyList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="mb-3 position-relative">
                                            <label for="activity_empty" class="form-label">Activity</label>
                                            <input type="text" class="form-control" id="activity_empty" name="activity_empty" value="TDC-DICT.RV.Empty" required>
                                            <ul id="activityEmptyList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="waybill_empty" class="form-label">WAYBILL EMPTY</label>
                                        <input type="text" class="form-control" id="waybill_empty" name="waybill_empty">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pullout_location_arrival_date" class="form-label">Pullout Location - Arrival Date</label>
                                        <input type="text" class="form-control" id="pullout_location_arrival_date" name="pullout_location_arrival_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pullout_location_arrival_time" class="form-label">Pullout Location - Arrival Time</label>
                                        <input type="text" class="form-control" id="pullout_location_arrival_time" name="pullout_location_arrival_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pullout_location_departure_date" class="form-label">Pullout Location - Departure Date</label>
                                        <input type="text" class="form-control" id="pullout_location_departure_date" name="pullout_location_departure_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pullout_location_departure_time" class="form-label">Pullout Location - Departure Time</label>
                                        <input type="text" class="form-control" id="pullout_location_departure_time" name="pullout_location_departure_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ph_arrival_date" class="form-label">PH Arrival Date</label>
                                        <input type="text" class="form-control" id="ph_arrival_date" name="ph_arrival_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ph_arrival_time" class="form-label">PH Arrival Time</label>
                                        <input type="text" class="form-control" id="ph_arrival_time" name="ph_arrival_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="van_alpha" class="form-label">VAN - ALPHA</label>
                                        <input type="text" class="form-control" id="van_alpha" name="van_alpha">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="van_number" class="form-label">VAN - NUMBER</label>
                                        <input type="text" class="form-control" id="van_number" name="van_number">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="empty_container_van" class="form-label">EMPTY CONTAINER VAN (WITHDRAWAL)</label>
                                        <input type="text" class="form-control" id="empty_container_van" name="empty_container_van">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="withdrawal_date" class="form-label">DATE</label>
                                        <input type="text" class="form-control" id="withdrawal_date" name="withdrawal_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="withdrawal_time" class="form-label">TIME</label>
                                        <input type="text" class="form-control" id="withdrawal_time" name="withdrawal_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="van_name" class="form-label">VAN NAME</label>
                                        <input type="text" class="form-control" id="van_name" name="van_name">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="ph" class="form-label">PH (Packing House / Location)</label>
                                            <input type="text" class="form-control" id="ph" name="ph" required autocomplete="off" placeholder="Search location or PH…">
                                            <ul id="phList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="shipper" class="form-label">SHIPPER</label>
                                            <input type="text" class="form-control" id="shipper" name="shipper" autocomplete="off" placeholder="Search shipper…">
                                            <ul id="shipperList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1040; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ecs" class="form-label">ECS</label>
                                        <input type="text" class="form-control" id="ecs" name="ecs">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="tr" class="form-label">TR</label>
                                            <input type="text" class="form-control" id="tr" name="tr" required autocomplete="off" placeholder="Search trailer…">
                                            <ul id="trailerList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1040; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="gs" class="form-label">GS</label>
                                            <input type="text" class="form-control" id="gs" name="gs" required autocomplete="off" placeholder="Search genset (GS)…">
                                            <ul id="gensetList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1030; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="prime_mover" class="form-label">PRIME MOVER</label>
                                            <input type="text" class="form-control" id="prime_mover" name="prime_mover" required autocomplete="off">
                                            <ul id="truckList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1020; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="driver" class="form-label">DRIVER</label>
                                            <input type="text" class="form-control" id="driver" name="driver" required autocomplete="off">
                                            <ul id="driverList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1010; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                            <input type="hidden" id="driver_idNumber" name="driver_idNumber">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="empty_pullout_location" class="form-label">EMPTY PULLOUT LOCATION</label>
                                        <input type="text" class="form-control" id="empty_pullout_location" name="empty_pullout_location">
                                    </div>
                                    <h5>LOADED CONTAINER VAN</h5>
                                    <div class="col-md-6" hidden>
                                        <div class="mb-3 position-relative">
                                            <label for="segment" class="form-label">Segment</label>
                                            <input type="text" class="form-control" id="segment" name="segment" value="TDCRV" required>
                                            <ul id="segmentList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="mb-3 position-relative">
                                            <label for="activity" class="form-label">Activity</label>
                                            <input type="text" class="form-control" id="activity" name="activity" value="TDC-DICT.RV.Loaded" required>
                                            <ul id="activityList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="waybill" class="form-label">WAYBILL</label>
                                        <input type="text" class="form-control" id="waybill" name="waybill" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="loading_start_date" class="form-label">LOADING SCHEDULE START DATE</label>
                                        <input type="text" class="form-control" id="loading_start_date" name="loading_start_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="loading_start_time" class="form-label">LOADING SCHEDULE START TIME</label>
                                        <input type="text" class="form-control" id="loading_start_time" name="loading_start_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="loading_finish_date" class="form-label">LOADING SCHEDULE FINISH DATE</label>
                                        <input type="text" class="form-control" id="loading_finish_date" name="loading_finish_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="loading_finish_time" class="form-label">LOADING SCHEDULE FINISH TIME</label>
                                        <input type="text" class="form-control" id="loading_finish_time" name="loading_finish_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="delivery_departure_date" class="form-label">DELIVERY DEPARTURE DATE</label>
                                        <input type="text" class="form-control" id="delivery_departure_date" name="delivery_departure_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="delivery_departure_time" class="form-label">DELIVERY DEPARTURE TIME</label>
                                        <input type="text" class="form-control" id="delivery_departure_time" name="delivery_departure_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="delivery_arrival_date" class="form-label">DELIVERY ARRIVAL DATE</label>
                                        <input type="text" class="form-control" id="delivery_arrival_date" name="delivery_arrival_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="delivery_arrival_time" class="form-label">DELIVERY ARRIVAL TIME</label>
                                        <input type="text" class="form-control" id="delivery_arrival_time" name="delivery_arrival_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="genset_shut_off_start_date" class="form-label">GENSET SHUT OFF START DATE</label>
                                        <input type="text" class="form-control" id="genset_shut_off_start_date" name="genset_shut_off_start_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="genset_shut_off_start_time" class="form-label">GENSET SHUT OFF START TIME</label>
                                        <input type="text" class="form-control" id="genset_shut_off_start_time" name="genset_shut_off_start_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="genset_shut_off_finish_date" class="form-label">GENSET SHUT OFF FINISH DATE</label>
                                        <input type="text" class="form-control" id="genset_shut_off_finish_date" name="genset_shut_off_finish_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="genset_shut_off_finish_time" class="form-label">GENSET SHUT OFF FINISH TIME</label>
                                        <input type="text" class="form-control" id="genset_shut_off_finish_time" name="genset_shut_off_finish_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_unloading_start_date" class="form-label">END OF UNLOADING START DATE</label>
                                        <input type="text" class="form-control" id="end_unloading_start_date" name="end_unloading_start_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_unloading_start_time" class="form-label">END OF UNLOADING START TIME</label>
                                        <input type="text" class="form-control" id="end_unloading_start_time" name="end_unloading_start_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_unloading_finish_date" class="form-label">END OF UNLOADING FINISH DATE</label>
                                        <input type="text" class="form-control" id="end_unloading_finish_date" name="end_unloading_finish_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_unloading_finish_time" class="form-label">END OF UNLOADING FINISH TIME</label>
                                        <input type="text" class="form-control" id="end_unloading_finish_time" name="end_unloading_finish_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="reference_documents" class="form-label">REFERENCE DOCUMENTS</label>
                                        <input type="text" class="form-control" id="reference_documents" name="reference_documents">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="dr_no" class="form-label">DR. NO.</label>
                                        <input type="text" class="form-control" id="dr_no" name="dr_no">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="load" class="form-label">LOAD</label>
                                        <input type="text" class="form-control" id="load" name="load">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="pm2" class="form-label">PM (Prime Mover)</label>
                                            <input type="text" class="form-control" id="pm2" name="pm2" autocomplete="off">
                                            <ul id="pm2List" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1020; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label for="driver2" class="form-label">DRIVER</label>
                                            <input type="text" class="form-control" id="driver2" name="driver2" autocomplete="off">
                                            <ul id="driver2List" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1010; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                            <input type="hidden" id="driver_idNumber2" name="driver_idNumber2">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3 position-relative">
                                            <label for="delivered_to" class="form-label">DELIVERED TO: STATE THE LOCATION</label>
                                            <input type="text" class="form-control" id="delivered_to" name="delivered_to" autocomplete="off" placeholder="Search location">
                                            <ul id="deliveredToList" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display: none; max-height: 240px; overflow-y: auto;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="remarks" class="form-label">REMARKS</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="genset_hr_meter" class="form-label">GENSET HR METER</label>
                                        <input type="text" class="form-control" id="genset_hr_meter" name="genset_hr_meter">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="hr_meter_start" class="form-label">HR METER START</label>
                                        <input type="text" class="form-control" id="hr_meter_start" name="hr_meter_start">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="hr_meter_end" class="form-label">HR METER END</label>
                                        <input type="text" class="form-control" id="hr_meter_end" name="hr_meter_end">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="genset_hr_reading" class="form-label">GENSET HR READING</label>
                                        <input type="text" class="form-control" id="genset_hr_reading" name="genset_hr_reading">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gs_start_date" class="form-label">GS START DATE</label>
                                        <input type="text" class="form-control" id="gs_start_date" name="gs_start_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gs_start_time" class="form-label">GS START TIME</label>
                                        <input type="text" class="form-control" id="gs_start_time" name="gs_start_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gs_end_date" class="form-label">GS END DATE</label>
                                        <input type="text" class="form-control" id="gs_end_date" name="gs_end_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gs_end_time" class="form-label">GS END TIME</label>
                                        <input type="text" class="form-control" id="gs_end_time" name="gs_end_time" data-manual-time="true" inputmode="numeric" autocomplete="off" placeholder="HHMM">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="refueled" class="form-label">REFUELED</label>
                                        <input type="text" class="form-control" id="refueled" name="refueled">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Save Entry</button>
                                        <button type="reset" class="btn btn-secondary"><i class="bi bi-x-circle me-2"></i>Clear</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">TDC RV Entries</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="entriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>ID</th>
                                        <th>Segment</th>
                                        <th>Activity</th>
                                        <th>Waybill</th>
                                        <th>Driver</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr data-id="<?php echo $row['entry_id']; ?>">
                                        <td><input type="checkbox" class="form-check-input row-checkbox"></td>
                                        <td><strong>#<?php echo $row['entry_id']; ?></strong></td>
                                        <td><?php echo $row['segment']; ?></td>
                                        <td><?php echo $row['activity']; ?></td>
                                        <td><?php echo $row['waybill']; ?></td>
                                        <td><?php echo $row['driver']; ?></td>
                                        <td><?php echo $row['remarks']; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
                                                <button type="button" class="btn btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/alert/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            let rvTable = $('#entriesTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 10,
                "pagingType": "full_numbers"
            });

            const form = document.getElementById("dataEntryForm");
            document.getElementById("segment_empty").value = "TDCRV";
            document.getElementById("segment").value = "TDCRV";

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                $.ajax({
                    url: form.dataset.id ? 'php/update/rv.php' : 'php/insert/rv.php',
                    type: 'POST',
                    data: Object.fromEntries(formData),
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            rvTable.draw(false);
                            form.reset();
                            document.getElementById("segment_empty").value = "TDCRV";
                            document.getElementById("segment").value = "TDCRV";
                            Swal.fire('Success', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message || 'Operation failed.', 'error');
                        }
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-delete')) {
                    const row = e.target.closest('tr');
                    const id = row.dataset.id;
                    Swal.fire({
                        title: 'Confirm',
                        text: 'Delete this entry?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes'
                    }).then(result => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'php/delete/rv.php',
                                type: 'POST',
                                data: { action: 'delete-rv', data_id: id },
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success) {
                                        rvTable.row(row).remove().draw(false);
                                        Swal.fire('Deleted!', 'Entry deleted.', 'success');
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
