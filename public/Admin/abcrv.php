<?php 
include "php/session-check.php"; 
include "php/config/config.php";

$segment_filter = "ABCRV";
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
    <title>ABC RV Data Entry - DataEncode System</title>
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
                    <div class="col-md-5">
                        <div class="content-header mb-4">
                            <h2 class="fw-bold">ABC RV Data Entry</h2>
                            <p class="text-muted">Create, view, and manage ABC RV segment entries efficiently.</p>
                        </div>
                    </div>
                    <div class="col-md-7 text-end">
                        <div class="btn-group" role="group" aria-label="Basic outlined example">
                            <a class="btn btn-outline-secondary" href="abcrv">ABC RV</a>
                            <a class="btn btn-outline-secondary" href="doleRv">Dole RV</a>
                            <a class="btn btn-outline-secondary" href="sumiRv">Sumi RV</a>
                            <a class="btn btn-outline-secondary" href="tdcRv">TDC RV</a>
                            <a class="btn btn-outline-secondary" href="others">Others</a>
                            <a class="btn btn-outline-secondary" href="DPC_KDI">DPC_KDI & OPM</a>
                            <a class="btn btn-outline-secondary" href="cargoTruck">Cargo Truck</a>
                            <a class="btn btn-outline-secondary" href="dryVan">Dry Van</a>
                        </div>
                    </div>
                </div>


                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>New ABC RV Entry</h5>
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
                                            <input type="text" class="form-control" id="segment_empty" name="segment_empty" value="ABCRV" required>
                                            <ul id="segmentEmptyList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="mb-3 position-relative">
                                            <label for="activity_empty" class="form-label">Activity</label>
                                            <input type="text" class="form-control" id="activity_empty" name="activity_empty" required>
                                            <ul id="activityEmptyList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="waybill_empty" class="form-label">WAYBILL</label>
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
                                            <input type="text" class="form-control" id="segment" name="segment" value="ABCRV" required>
                                            <ul id="segmentList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="mb-3 position-relative">
                                            <label for="activity" class="form-label">Activity</label>
                                            <input type="text" class="form-control" id="activity" name="activity" required>
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
                                <h5 class="mb-0">ABC RV Entries</h5>
                            </div>
                            <div class="col-md-6" hidden>
                                <div class="d-flex gap-2 justify-content-md-end">

                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-filter"></i> Filter
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-filter="all">All</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="completed">Completed</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="pending">Pending</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="in-progress">In Progress</a></li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-success">
                                        <i class="bi bi-download"></i> Export
                                    </button>
                                </div>
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
                                    <tr data-id="<?php echo $row['entry_id']; ?>" data-segment="<?php echo htmlspecialchars($row['segment']); ?>" data-activity="<?php echo htmlspecialchars($row['activity']); ?>" data-waybill="<?php echo htmlspecialchars($row['waybill']); ?>" data-driver="<?php echo htmlspecialchars($row['driver']); ?>" data-remarks="<?php echo htmlspecialchars($row['remarks']); ?>">
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
         $(document).ready(function () {
            $("#enav").attr({
					"class" : "nav-link active"
				});
         });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ===== Segment & Activity Search =====
            const segmentEmptyInput = document.getElementById("segment_empty");
            const segmentEmptyList = document.getElementById("segmentEmptyList");
            const activityEmptyInput = document.getElementById("activity_empty");
            const activityEmptyList = document.getElementById("activityEmptyList");
            const segmentInput = document.getElementById("segment");
            const segmentList = document.getElementById("segmentList");
            const activityInput = document.getElementById("activity");
            const activityList = document.getElementById("activityList");
            let allSegmentActivity = [];
            let selectedSegment = "ABCRV";
            let selectedEmptySegment = "ABCRV";

            fetch("php/fetch/get_segment_activity.php")
                .then(res => res.json())
                .then(data => {
                    allSegmentActivity = data;
                });

            if (activityEmptyInput && activityEmptyList) {
                activityEmptyInput.addEventListener("input", function() {
                    if (!selectedEmptySegment) {
                        Swal.fire({ title: "Segment required", text: "Please select an empty segment first.", icon: "warning" });
                        this.value = "";
                        return;
                    }

                    const searchVal = this.value.toLowerCase();
                    const filteredActivities = [...new Set(allSegmentActivity
                        .filter(item => item.segment === selectedEmptySegment && item.activity.toLowerCase().includes(searchVal))
                        .map(item => item.activity))];

                    filterDropdown(this, activityEmptyList, filteredActivities, (name) => {
                        activityEmptyInput.value = name;
                    });
                });
            }

            if (activityInput && activityList) {
                activityInput.addEventListener("input", function() {
                    if (!selectedSegment) {
                        Swal.fire({ title: "Segment required", text: "Please select a segment first.", icon: "warning" });
                        this.value = "";
                        return;
                    }

                    const searchVal = this.value.toLowerCase();
                    const filteredActivities = [...new Set(allSegmentActivity
                        .filter(item => item.segment === selectedSegment && item.activity.toLowerCase().includes(searchVal))
                        .map(item => item.activity))];

                    filterDropdown(this, activityList, filteredActivities, (name) => {
                        activityInput.value = name;
                    });
                });
            }

            // ===== Driver Search =====
            const driverInput = document.getElementById("driver");
            const driverList = document.getElementById("driverList");
            const driver2Input = document.getElementById("driver2");
            const driver2List = document.getElementById("driver2List");
            const driverIdInput = document.getElementById("driver_idNumber");
            let allDrivers = [];

            fetch("php/fetch/get_drivers.php")
                .then(res => res.json())
                .then(data => {
                    allDrivers = data;
                });

            if (driverInput && driverList) {
                driverInput.addEventListener("input", function() {
                    filterDropdown(this, driverList, allDrivers.map(d => d.name), (name) => {
                        driverInput.value = name;
                        const selected = allDrivers.find(d => d.name === name);
                        driverIdInput.value = selected ? selected.id : "";
                    });
                });
            }

            const driverId2Input = document.getElementById("driver_idNumber2");
            if (driver2Input && driver2List) {
                driver2Input.addEventListener("input", function() {
                    filterDropdown(this, driver2List, allDrivers.map(d => d.name), (name) => {
                        driver2Input.value = name;
                        const selected = allDrivers.find(d => d.name === name);
                        driverId2Input.value = selected ? selected.id : "";
                    });
                });
            }

            // ===== Location / PH (Packing House) search =====
            const phInput = document.getElementById("ph");
            const phList = document.getElementById("phList");
            const deliveredToInput = document.getElementById("delivered_to");
            const deliveredToList = document.getElementById("deliveredToList");
            let allLocations = [];

            fetch("php/fetch/get_locations.php")
                .then(res => res.json())
                .then(data => {
                    allLocations = Array.isArray(data) ? data : [];
                })
                .catch(() => { allLocations = []; });

            if (phInput && phList) {
                phInput.addEventListener("input", function() {
                    filterDropdownRecords(
                        this,
                        phList,
                        allLocations,
                        (loc) => loc.location_name || "",
                        (loc) => loc.location_name || "",
                        (name) => { phInput.value = name; }
                    );
                });
            }

            if (deliveredToInput && deliveredToList) {
                deliveredToInput.addEventListener("input", function() {
                    filterDropdownRecords(
                        this,
                        deliveredToList,
                        allLocations,
                        (loc) => loc.location_name || "",
                        (loc) => loc.location_name || "",
                        (name) => { deliveredToInput.value = name; }
                    );
                });
            }

            // ===== Truck Search (prime mover) =====
            const truckInput = document.getElementById("prime_mover");
            const truckList = document.getElementById("truckList");
            const pm2Input = document.getElementById("pm2");
            const pm2List = document.getElementById("pm2List");
            let allTrucks = [];

            fetch("php/fetch/get_trucks.php")
                .then(res => res.json())
                .then(data => {
                    allTrucks = data;
                });

            if (truckInput && truckList) {
                truckInput.addEventListener("input", function() {
                    filterDropdown(this, truckList, allTrucks, (name) => {
                        truckInput.value = name;
                    });
                });
            }

            if (pm2Input && pm2List) {
                pm2Input.addEventListener("input", function() {
                    filterDropdown(this, pm2List, allTrucks, (name) => {
                        pm2Input.value = name;
                    });
                });
            }

            // ===== Genset (GS) search — units with unit_name GS* =====
            const gensetInput = document.getElementById("gs");
            const gensetList = document.getElementById("gensetList");
            let allGensets = [];

            fetch("php/fetch/get_gensets.php")
                .then(res => res.json())
                .then(data => {
                    allGensets = Array.isArray(data) ? data : [];
                });

            if (gensetInput && gensetList) {
                gensetInput.addEventListener("input", function() {
                    filterDropdownRecords(
                        this,
                        gensetList,
                        allGensets,
                        (u) => u.unit_name || "",
                        (u) => {
                            const bits = [u.unit_name];
                            if (u.unit_model) bits.push(u.unit_model);
                            if (u.unit_cluster) bits.push(u.unit_cluster);
                            return bits.join(" — ");
                        },
                        (name) => { gensetInput.value = name; }
                    );
                });
            }

            // ===== Trailer (TR) search =====
            const trailerInput = document.getElementById("tr");
            const trailerList = document.getElementById("trailerList");
            let allTrailers = [];
            
            // ===== Shipper search =====
            const shipperInput = document.getElementById("shipper");
            const shipperList = document.getElementById("shipperList");
            let allShippers = [];
            
            const allSearchLists = [segmentEmptyList, activityEmptyList, segmentList, activityList, phList, deliveredToList, driverList, driver2List, truckList, pm2List, gensetList, trailerList, shipperList].filter(Boolean);

            fetch("php/fetch/get_trailers.php")
                .then(res => res.json())
                .then(data => {
                    allTrailers = Array.isArray(data) ? data : [];
                });

            if (trailerInput && trailerList) {
                trailerInput.addEventListener("input", function() {
                    filterDropdownRecords(
                        this,
                        trailerList,
                        allTrailers,
                        (t) => t.trailer_name || "",
                        (t) => t.trailer_name,
                        (name) => { trailerInput.value = name; }
                    );
                });
            }

            // ===== Shipper search =====
            fetch("php/fetch/get_shippers.php")
                .then(res => res.json())
                .then(data => {
                    allShippers = Array.isArray(data) ? data : [];
                })
                .catch(() => { allShippers = []; });

            if (shipperInput && shipperList) {
                shipperInput.addEventListener("input", function() {
                    filterDropdownRecords(
                        this,
                        shipperList,
                        allShippers,
                        (s) => s.shipper || "",
                        (s) => s.shipper || "",
                        (name) => { 
                            shipperInput.value = name.trim();
                            updateActivitiesForShipper(name.trim());
                        }
                    );
                });
                
                // Handle click on dropdown items
                shipperList.addEventListener("click", function(e) {
                    const item = e.target.closest(".list-group-item");
                    if (item) {
                        const shipperName = item.textContent.trim();
                        shipperInput.value = shipperName;
                        updateActivitiesForShipper(shipperName);
                        hideDropdown(shipperList);
                    }
                });
            }

            // Shipper to Activities Mapping
            function updateActivitiesForShipper(shipper) {
                const shipperMapping = {
                    "ABC Cateel": { empty: "ABCCat.RV.Empty", loaded: "ABCCat.RV.Loaded" },
                    "ABC Donmar": { empty: "ABCDonMar.RV.Empty", loaded: "ABCDonMar.RV.Loaded" },
                    "ABC Lupon": { empty: "ABCLup.RV.Empty", loaded: "ABCLup.RV.Loaded" },
                    "ABC PANTUKAN": { empty: "ABCPan.RV.Empty", loaded: "ABCPan.RV.Loaded" }
                };
                
                const trimmedShipper = shipper ? shipper.trim() : "";
                
                if (trimmedShipper && shipperMapping[trimmedShipper]) {
                    const activities = shipperMapping[trimmedShipper];
                    const activityEmptyElem = document.getElementById("activity_empty");
                    const activityElem = document.getElementById("activity");
                    
                    if (activityEmptyElem) {
                        activityEmptyElem.value = activities.empty;
                    }
                    if (activityElem) {
                        activityElem.value = activities.loaded;
                    }
                }
            }

            function hideDropdown(listElem) {
                if (!listElem) return;
                listElem.style.display = "none";
                listElem.innerHTML = "";
            }

            function showDropdown(listElem) {
                if (!listElem) return;
                listElem.style.maxHeight = "240px";
                listElem.style.overflowY = "auto";
                listElem.style.overflowX = "hidden";
                listElem.style.display = "block";
            }

            // ===== Shared: string list =====
            function filterDropdown(inputElem, listElem, dataArr, onSelect) {
                const searchVal = inputElem.value.toLowerCase();

                if (!searchVal) {
                    hideDropdown(listElem);
                    return;
                }

                listElem.innerHTML = "";

                const filtered = dataArr.filter(item => String(item).toLowerCase().includes(searchVal));

                if (filtered.length === 0) {
                    hideDropdown(listElem);
                    return;
                }

                filtered.forEach((item, index) => {
                    const li = document.createElement("li");
                    li.className = "list-group-item list-group-item-action";
                    const label = String(item);
                    li.textContent = label;
                    li.dataset.pickValue = label;

                    if (index === 0) {
                        li.classList.add("active-suggestion");
                    }

                    li.addEventListener("mousedown", function(event) {
                        event.preventDefault();
                        onSelect(label);
                        hideDropdown(listElem);
                    });
                    listElem.appendChild(li);
                });

                showDropdown(listElem);
            }

            // ===== Records (location, trailer row, genset row) =====
            function filterDropdownRecords(inputElem, listElem, records, getValue, formatLine, onPick) {
                const searchVal = inputElem.value.toLowerCase();

                if (!searchVal) {
                    hideDropdown(listElem);
                    return;
                }

                listElem.innerHTML = "";

                const filtered = records.filter((r) => getValue(r).toLowerCase().includes(searchVal));

                if (filtered.length === 0) {
                    hideDropdown(listElem);
                    return;
                }

                filtered.forEach((r, index) => {
                    const li = document.createElement("li");
                    li.className = "list-group-item list-group-item-action";
                    const val = getValue(r);
                    li.textContent = formatLine(r);
                    li.dataset.pickValue = val;

                    if (index === 0) {
                        li.classList.add("active-suggestion");
                    }

                    li.addEventListener("mousedown", function(event) {
                        event.preventDefault();
                        onPick(val);
                        hideDropdown(listElem);
                    });
                    listElem.appendChild(li);
                });

                showDropdown(listElem);
            }

             // ===== Autofill + Navigation Support =====
        function attachKeyboardNav(inputElem, listElem, onSelect) {
          let activeIndex = 0;

          inputElem.addEventListener("keydown", function(e) {
            const items = listElem.querySelectorAll("li");
            if (e.key === "Escape") {
              hideDropdown(listElem);
              return;
            }
            if (!items.length) return;

            if (e.key === "ArrowDown") {
              e.preventDefault();
              activeIndex = (activeIndex + 1) % items.length;
              updateActive(items, activeIndex);
            } else if (e.key === "ArrowUp") {
              e.preventDefault();
              activeIndex = (activeIndex - 1 + items.length) % items.length;
              updateActive(items, activeIndex);
            } else if (e.key === "Enter") {
              const activeItem = items[activeIndex];
              if (activeItem) {
                e.preventDefault();
                const v = activeItem.dataset.pickValue !== undefined ? activeItem.dataset.pickValue : activeItem.textContent;
                onSelect(v);
                hideDropdown(listElem);
              }
            } else if (e.key === "Tab") {
              const activeItem = items[activeIndex];
              if (activeItem) {
                const v = activeItem.dataset.pickValue !== undefined ? activeItem.dataset.pickValue : activeItem.textContent;
                onSelect(v);
                hideDropdown(listElem);
              } else {
                hideDropdown(listElem);
              }
            }
          });

          function updateActive(items, index) {
            items.forEach(i => i.classList.remove("active-suggestion"));
            items[index].classList.add("active-suggestion");
            items[index].scrollIntoView({ block: 'nearest' });
          }
        }

            document.addEventListener("mousedown", function(event) {
                const clickedInsideSearch = event.target.closest(".position-relative");
                if (clickedInsideSearch) return;
                allSearchLists.forEach(hideDropdown);
            });

            // Attach keyboard nav to each input/list
            if (activityEmptyInput && activityEmptyList) {
                attachKeyboardNav(activityEmptyInput, activityEmptyList, (val) => {
                    activityEmptyInput.value = val;
                });
            }

            if (activityInput && activityList) {
                attachKeyboardNav(activityInput, activityList, (val) => {
                    activityInput.value = val;
                });
            }

            if (phInput && phList) {
                attachKeyboardNav(phInput, phList, (val) => {
                    phInput.value = val;
                });
            }

            if (deliveredToInput && deliveredToList) {
                attachKeyboardNav(deliveredToInput, deliveredToList, (val) => {
                    deliveredToInput.value = val;
                });
            }

            if (driverInput && driverList) {
                attachKeyboardNav(driverInput, driverList, (val) => {
                    driverInput.value = val;
                    const selected = allDrivers.find(d => d.name === val);
                    driverIdInput.value = selected ? selected.id : "";
                });
            }

            if (driver2Input && driver2List) {
                attachKeyboardNav(driver2Input, driver2List, (val) => {
                    driver2Input.value = val;
                    const selected = allDrivers.find(d => d.name === val);
                    driverId2Input.value = selected ? selected.id : "";
                });
            }

            if (trailerInput && trailerList) {
                attachKeyboardNav(trailerInput, trailerList, (val) => {
                    trailerInput.value = val;
                });
            }

            if (truckInput && truckList) {
                attachKeyboardNav(truckInput, truckList, (val) => {
                    truckInput.value = val;
                });
            }

            if (pm2Input && pm2List) {
                attachKeyboardNav(pm2Input, pm2List, (val) => {
                    pm2Input.value = val;
                });
            }

            if (gensetInput && gensetList) {
                attachKeyboardNav(gensetInput, gensetList, (val) => {
                    gensetInput.value = val;
                });
            }

            if (shipperInput && shipperList) {
                attachKeyboardNav(shipperInput, shipperList, (val) => {
                    shipperInput.value = val;
                    updateActivitiesForShipper(val);
                });
            }

            if (phInput && phList) {
                attachKeyboardNav(phInput, phList, (val) => {
                    phInput.value = val;
                });
            }
        });

        $(document).ready(function() {
            let rvTable;

            rvTable = $('#entriesTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 10,
                "pagingType": "full_numbers",
                "language": {
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });

            function getValues() {
                return {
                    id: document.getElementById("data_id").value,
                    segment_empty: document.getElementById("segment_empty").value,
                    activity_empty: document.getElementById("activity_empty").value,
                    segment: document.getElementById("segment").value,
                    activity: document.getElementById("activity").value,
                    remarks: document.getElementById("remarks").value,
                    pullout_location_arrival_date: document.getElementById("pullout_location_arrival_date").value,
                    pullout_location_arrival_time: document.getElementById("pullout_location_arrival_time").value,
                    pullout_location_departure_date: document.getElementById("pullout_location_departure_date").value,
                    pullout_location_departure_time: document.getElementById("pullout_location_departure_time").value,
                    ph_arrival_date: document.getElementById("ph_arrival_date").value,
                    ph_arrival_time: document.getElementById("ph_arrival_time").value,
                    van_alpha: document.getElementById("van_alpha").value,
                    van_number: document.getElementById("van_number").value,
                    van_name: document.getElementById("van_name").value,
                    ph: document.getElementById("ph").value,
                    shipper: document.getElementById("shipper").value,
                    ecs: document.getElementById("ecs").value,
                    tr: document.getElementById("tr").value,
                    gs: document.getElementById("gs").value,
                    waybill: document.getElementById("waybill").value,
                    waybill_empty: document.getElementById("waybill_empty").value,
                    prime_mover: document.getElementById("prime_mover").value,
                    driver: document.getElementById("driver").value,
                    driver_idNumber: document.getElementById("driver_idNumber").value,
                    empty_pullout_location: document.getElementById("empty_pullout_location").value,
                    loaded_van_loading_start_date: document.getElementById("loading_start_date").value,
                    loaded_van_loading_start_time: document.getElementById("loading_start_time").value,
                    loaded_van_loading_finish_date: document.getElementById("loading_finish_date").value,
                    loaded_van_loading_finish_time: document.getElementById("loading_finish_time").value,
                    loaded_van_delivery_departure_date: document.getElementById("delivery_departure_date").value,
                    loaded_van_delivery_departure_time: document.getElementById("delivery_departure_time").value,
                    loaded_van_delivery_arrival_date: document.getElementById("delivery_arrival_date").value,
                    loaded_van_delivery_arrival_time: document.getElementById("delivery_arrival_time").value,
                    genset_shutoff_date: document.getElementById("genset_shut_off_start_date").value,
                    genset_shutoff_time: document.getElementById("genset_shut_off_start_time").value,
                    end_uploading_date: document.getElementById("end_unloading_start_date").value,
                    end_uploading_time: document.getElementById("end_unloading_start_time").value,
                    dr_no: document.getElementById("dr_no").value,
                    load_description: document.getElementById("load").value,
                    delivered_by_prime_mover: document.getElementById("pm2").value,
                    delivered_by_driver: document.getElementById("driver2").value,
                    delivered_by_driverIdNumber: document.getElementById("driver_idNumber2").value,
                    delivered_to: document.getElementById("delivered_to").value,
                    genset_hr_meter_start: document.getElementById("hr_meter_start").value,
                    genset_hr_meter_end: document.getElementById("hr_meter_end").value,
                    genset_start_date: document.getElementById("gs_start_date").value,
                    genset_start_time: document.getElementById("gs_start_time").value,
                    genset_end_date: document.getElementById("gs_end_date").value,
                    genset_end_time: document.getElementById("gs_end_time").value
                };
            }

            function getTableRowData(record) {
                return [
                    '',
                    '#' + record.entry_id,
                    record.segment,
                    record.activity,
                    record.waybill,
                    record.driver,
                    record.remarks,
                    ''
                ];
            }

            function setRowDataAttributes(row, record) {
                row.dataset.id = record.entry_id;
                row.dataset.segment = record.segment;
                row.dataset.activity = record.activity;
                row.dataset.waybill = record.waybill;
                row.dataset.driver = record.driver;
                row.dataset.remarks = record.remarks;
            }

            function clearForm() {
                document.getElementById("dataEntryForm").reset();
                document.getElementById("segment_empty").value = "ABCRV";
                document.getElementById("segment").value = "ABCRV";
                document.getElementById("data_id").value = "";
            }

            const form = document.getElementById("dataEntryForm");
            if (form) {
                form.addEventListener("reset", function() {
                    document.getElementById("segment_empty").value = "ABCRV";
                    document.getElementById("segment").value = "ABCRV";
                });
            }

            function fillFormFromRecord(record) {
                const fieldMap = {
                    segment_empty: 'segment_empty',
                    activity_empty: 'activity_empty',
                    segment: 'segment',
                    activity: 'activity',
                    remarks: 'remarks',
                    pullout_location_arrival_date: 'pullout_location_arrival_date',
                    pullout_location_arrival_time: 'pullout_location_arrival_time',
                    pullout_location_departure_date: 'pullout_location_departure_date',
                    pullout_location_departure_time: 'pullout_location_departure_time',
                    ph_arrival_date: 'ph_arrival_date',
                    ph_arrival_time: 'ph_arrival_time',
                    van_alpha: 'van_alpha',
                    van_number: 'van_number',
                    van_name: 'van_name',
                    ph: 'ph',
                    shipper: 'shipper',
                    ecs: 'ecs',
                    tr: 'tr',
                    gs: 'gs',
                    waybill: 'waybill',
                    waybill_empty: 'waybill_empty',
                    prime_mover: 'prime_mover',
                    driver: 'driver',
                    driver_idNumber: 'driver_idNumber',
                    empty_pullout_location: 'empty_pullout_location',
                    loaded_van_loading_start_date: 'loading_start_date',
                    loaded_van_loading_start_time: 'loading_start_time',
                    loaded_van_loading_finish_date: 'loading_finish_date',
                    loaded_van_loading_finish_time: 'loading_finish_time',
                    loaded_van_delivery_departure_date: 'delivery_departure_date',
                    loaded_van_delivery_departure_time: 'delivery_departure_time',
                    loaded_van_delivery_arrival_date: 'delivery_arrival_date',
                    loaded_van_delivery_arrival_time: 'delivery_arrival_time',
                    genset_shutoff_date: 'genset_shut_off_start_date',
                    genset_shutoff_time: 'genset_shut_off_start_time',
                    end_uploading_date: 'end_unloading_start_date',
                    end_uploading_time: 'end_unloading_start_time',
                    dr_no: 'dr_no',
                    load_description: 'load',
                    delivered_by_prime_mover: 'pm2',
                    delivered_by_driver: 'driver2',
                    delivered_to: 'delivered_to',
                    genset_hr_meter_start: 'hr_meter_start',
                    genset_hr_meter_end: 'hr_meter_end',
                    genset_start_date: 'gs_start_date',
                    genset_start_time: 'gs_start_time',
                    genset_end_date: 'gs_end_date',
                    genset_end_time: 'gs_end_time',
                    driver_idNumber: 'driver_idNumber',
                    delivered_by_driverIdNumber: 'driver_idNumber2'
                };

                Object.entries(fieldMap).forEach(([recordKey, inputId]) => {
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.value = record[recordKey] ?? '';
                    }
                });
            }

            function loadRecordIntoForm(row) {
                const id = row?.dataset?.id;
                if (!id) return;

                fetch(`php/fetch/get_rv.php?id=${encodeURIComponent(id)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire('Error', data.message || 'Unable to load record.', 'error');
                            return;
                        }
                        fillFormFromRecord(data.record);
                        const collapseEl = document.querySelector('#entryForm');
                        if (collapseEl) {
                            collapseEl.classList.add('show');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Unable to fetch record.', 'error');
                    });
            }

            let rowActionsAttached = false;

            function refreshTable() {
                if (rvTable) {
                    rvTable.draw(false);
                }
            }

            function attachRowActions() {
                if (rowActionsAttached) return;
                const rowActionsRoot = document.querySelector('#entriesTable')?.closest('.table-responsive') || document.body;
                if (!rowActionsRoot) return;

                rowActionsRoot.addEventListener('click', async (e) => {
                    const btn = e.target.closest('.btn-edit, .btn-delete');
                    if (!btn) return;
                    const row = btn.closest('tr');
                    if (!row) return;

                    if (btn.classList.contains('btn-edit')) {
                        loadRecordIntoForm(row);
                        return;
                    }

                    if (btn.classList.contains('btn-delete')) {
                        const id = row.dataset.id;
                        const confirmed = await Swal.fire({
                            title: 'Confirm delete',
                            text: 'Delete row #' + id + ' ? This cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel'
                        });

                        if (!confirmed.isConfirmed) return;

                        $.ajax({
                            url: 'php/delete/rv.php',
                            type: 'POST',
                            data: {
                                action: 'delete-rv',
                                data_id: id
                            },
                            dataType: 'json',
                            success: function(result) {
                                if (result.success) {
                                    rvTable.row(row).remove().draw(false);
                                    Swal.fire('Deleted!', 'Entry deleted.', 'success');
                                } else {
                                    Swal.fire('Error', result.message || 'Delete failed', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Unable to reach server', 'error');
                            }
                        });
                    }
                });

                rowActionsAttached = true;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const values = getValues();

                if (!values.segment_empty) {
                    Swal.fire('Missing field', 'Empty segment is required.', 'warning');
                    return;
                }
                if (!values.activity_empty) {
                    Swal.fire('Missing field', 'Empty activity is required.', 'warning');
                    return;
                }
                if (!values.segment) {
                    Swal.fire('Missing field', 'Loaded segment is required.', 'warning');
                    return;
                }
                if (!values.activity) {
                    Swal.fire('Missing field', 'Loaded activity is required.', 'warning');
                    return;
                }
                if (!values.waybill) {
                    Swal.fire('Missing field', 'Waybill is required.', 'warning');
                    return;
                }
                if (!values.ph) {
                    Swal.fire('Missing field', 'PH (Packing House / Location) is required. Use the search list from the location master.', 'warning');
                    return;
                }
                if (!values.tr) {
                    Swal.fire('Missing field', 'Trailer (TR) is required. Pick from the trailer list.', 'warning');
                    return;
                }
                if (!values.gs) {
                    Swal.fire('Missing field', 'Genset (GS) is required. Pick a unit with name starting with GS.', 'warning');
                    return;
                }
                if (!values.prime_mover) {
                    Swal.fire('Missing field', 'Prime mover is required.', 'warning');
                    return;
                }
                if (!values.driver) {
                    Swal.fire('Missing field', 'Driver is required.', 'warning');
                    return;
                }

                const action = values.id ? 'update-rv' : 'add-rv';
                const endpoint = values.id ? 'php/update/rv.php' : 'php/insert/rv.php';

                const payload = {
                    action: action,
                    data_id: values.id || '',
                    ...values
                };
                delete payload.id;

                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    data: payload,
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            if (action === 'add-rv') {
                                const addedRow = rvTable.row.add(getTableRowData(data.record));
                                addedRow.draw(false);
                                setRowDataAttributes(addedRow.node(), data.record);
                            } else {
                                const row = document.querySelector(`#entriesTable tbody tr[data-id="${values.id}"]`);
                                if (row) {
                                    const rowApi = rvTable.row(row);
                                    if (rowApi.any()) {
                                        rowApi.data(getTableRowData(data.record)).draw(false);
                                        setRowDataAttributes(rowApi.node(), data.record);
                                    }
                                }
                            }
                            refreshTable();
                            clearForm();
                            Swal.fire('Success', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message || 'Operation failed.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to reach server', 'error');
                    }
                });
            });

            refreshTable();
            attachRowActions();
        });

    </script>
</body>

</html>
