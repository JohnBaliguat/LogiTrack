<?php 
include "php/session-check.php"; 
include "php/config/config.php";

$query = "SELECT entry_id, entry_type, segment, activity, remarks, waybill, waybill_date, truck, driver, customer_ph, outside, compound, total_trips, operations, deliver_from, delivered_to, cargo_date FROM operations WHERE entry_type = 'CARGO TRUCK ENTRY' AND DATE(created_date) = CURDATE() ORDER BY entry_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Truck Data Entry - DataEncode System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                            <h2 class="fw-bold">Cargo Truck Data Entry</h2>
                            <p class="text-muted">Create, view, and manage cargo truck data entries efficiently.</p>
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
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>New Cargo Truck Entry</h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#entryForm">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse" id="entryForm">
                        <div class="card-body">
                            <form id="dataEntryForm">
                                <input type="hidden" id="data_id" name="data_id" value="">
                                <div class="row g-3">
                                    <div class="col-md-6 position-relative">
                                        <label for="segment" class="form-label">Segment</label>
                                        <input type="text" class="form-control" id="segment" required>
                                        <ul id="segmentList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                    </div>
                                    <div class="col-md-6 position-relative">
                                        <label for="activity" class="form-label">Activity</label>
                                        <input type="text" class="form-control" id="activity" required>
                                        <ul id="activityList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date" class="form-label">DATE</label>
                                        <input type="text" class="form-control" id="date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="waybill" class="form-label">WAYBILL</label>
                                        <input type="text" class="form-control" id="waybill">
                                    </div>
                                    <div class="col-md-6 position-relative">
                                        <label for="truck" class="form-label">TRUCK</label>
                                        <input type="text" class="form-control" id="truck">
                                        <ul id="truckList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                    </div>
                                    <div class="col-md-6 position-relative">
                                        <label for="driver" class="form-label">DRIVER</label>
                                        <input type="text" class="form-control" id="driver">
                                        <ul id="driverList" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                                        <input type="hidden" id="driver_idNumber" name="driver_idNumber">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_ph" class="form-label">CUSTOMER/PH</label>
                                        <input type="text" class="form-control" id="customer_ph">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="outside" class="form-label">OUTSIDE</label>
                                        <input type="text" class="form-control" id="outside">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="compound" class="form-label">COMPOUND</label>
                                        <input type="text" class="form-control" id="compound">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="total_trips" class="form-label">TOTAL TRIPS</label>
                                        <input type="text" class="form-control" id="total_trips">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="operations" class="form-label">OPERATIONS</label>
                                        <input type="text" class="form-control" id="operations">
                                    </div>
                                    <div class="col-md-6 position-relative">
                                        <label for="deliver_from" class="form-label">DELIVER FROM</label>
                                        <input type="text" class="form-control" id="deliver_from" autocomplete="off" placeholder="Search location">
                                        <ul id="deliverFromList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                    </div>
                                    <div class="col-md-6 position-relative">
                                        <label for="deliver_to" class="form-label">DELIVER TO</label>
                                        <input type="text" class="form-control" id="deliver_to" autocomplete="off" placeholder="Search location">
                                        <ul id="deliverToList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <label for="cargo_date" class="form-label">CARGO DATE</label>
                                        <input type="text" class="form-control" id="cargo_date" data-manual-date="true" inputmode="numeric" autocomplete="off" placeholder="M/D or M/D/YYYY">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="remarks" class="form-label">REMARKS</label>
                                        <textarea class="form-control" id="remarks" rows="3"></textarea>
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
                                <h5 class="mb-0">All Entries</h5>
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
                                                <button class="btn btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
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
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = new DataTable('#entriesTable');
            const form = document.getElementById('dataEntryForm');
            const dataIdInput = document.getElementById('data_id');
            const segmentInput = document.getElementById('segment');
            const activityInput = document.getElementById('activity');
            const dateInput = document.getElementById('date');
            const waybillInput = document.getElementById('waybill');
            const truckInput = document.getElementById('truck');
            const driverInput = document.getElementById('driver');
            const driverIdInput = document.getElementById('driver_idNumber');
            const customerPhInput = document.getElementById('customer_ph');
            const outsideInput = document.getElementById('outside');
            const compoundInput = document.getElementById('compound');
            const totalTripsInput = document.getElementById('total_trips');
            const operationsInput = document.getElementById('operations');
            const deliverFromInput = document.getElementById('deliver_from');
            const deliverToInput = document.getElementById('deliver_to');
            const remarksInput = document.getElementById('remarks');
            const cargoDateInput = document.getElementById('cargo_date');
            const segmentList = document.getElementById('segmentList');
            const activityList = document.getElementById('activityList');
            const truckList = document.getElementById('truckList');
            const driverList = document.getElementById('driverList');
            const deliverFromList = document.getElementById('deliverFromList');
            const deliverToList = document.getElementById('deliverToList');
            const allSearchLists = [segmentList, activityList, truckList, driverList, deliverFromList, deliverToList].filter(Boolean);
            let allSegmentActivity = [];
            let allDrivers = [];
            let allTrucks = [];
            let allLocations = [];
            let selectedSegment = '';

            fetch('php/fetch/get_segment_activity.php')
                .then(res => res.json())
                .then(data => {
                    allSegmentActivity = data || [];
                })
                .catch(() => {
                    allSegmentActivity = [];
                });

            fetch('php/fetch/get_drivers.php')
                .then(res => res.json())
                .then(data => {
                    allDrivers = data || [];
                })
                .catch(() => {
                    allDrivers = [];
                });

            fetch('php/fetch/get_trucks.php')
                .then(res => res.json())
                .then(data => {
                    allTrucks = data || [];
                })
                .catch(() => {
                    allTrucks = [];
                });

            fetch('php/fetch/get_locations.php')
                .then(res => res.json())
                .then(data => {
                    allLocations = Array.isArray(data) ? data : [];
                })
                .catch(() => {
                    allLocations = [];
                });

            function hideDropdown(listElem) {
                if (!listElem) return;
                listElem.style.display = 'none';
                listElem.innerHTML = '';
            }

            function showDropdown(listElem) {
                if (!listElem) return;
                listElem.style.maxHeight = '220px';
                listElem.style.overflowY = 'auto';
                listElem.style.overflowX = 'hidden';
                listElem.style.display = 'block';
            }

            function filterDropdown(inputElem, listElem, dataArr, onSelect) {
                const searchVal = inputElem.value.toLowerCase();
                if (!searchVal) {
                    hideDropdown(listElem);
                    return;
                }
                const filtered = dataArr.filter(item => item.toLowerCase().includes(searchVal));
                if (filtered.length === 0) {
                    hideDropdown(listElem);
                    return;
                }

                listElem.innerHTML = filtered.map(item => `<li class="list-group-item list-group-item-action">${item}</li>`).join('');
                showDropdown(listElem);

                listElem.querySelectorAll('li').forEach(item => {
                    item.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        onSelect(this.textContent);
                        hideDropdown(listElem);
                    });
                });
            }

            function filterDropdownRecords(inputElem, listElem, records, getValue, formatLine, onSelect) {
                const searchVal = inputElem.value.toLowerCase();
                if (!searchVal) {
                    hideDropdown(listElem);
                    return;
                }
                const filtered = records.filter(record => getValue(record).toLowerCase().includes(searchVal));

                if (filtered.length === 0) {
                    hideDropdown(listElem);
                    return;
                }

                listElem.innerHTML = filtered
                    .map(record => {
                        const value = getValue(record);
                        const label = formatLine(record);
                        return `<li class="list-group-item list-group-item-action" data-pick-value="${value}">${label}</li>`;
                    })
                    .join('');
                showDropdown(listElem);

                listElem.querySelectorAll('li').forEach(item => {
                    item.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        onSelect(this.dataset.pickValue || this.textContent);
                        hideDropdown(listElem);
                    });
                });
            }

            function attachKeyboardNav(inputElem, listElem, onSelect) {
                inputElem.addEventListener('keydown', function (event) {
                    const items = listElem.querySelectorAll('li');
                    if (event.key === 'Escape') {
                        hideDropdown(listElem);
                        return;
                    }
                    if (!items.length) return;
                    let activeIndex = Array.from(items).findIndex(item => item.classList.contains('active-suggestion'));

                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        activeIndex = activeIndex < items.length - 1 ? activeIndex + 1 : 0;
                        updateActive(items, activeIndex);
                    }

                    if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        activeIndex = activeIndex > 0 ? activeIndex - 1 : items.length - 1;
                        updateActive(items, activeIndex);
                    }

                    if (event.key === 'Enter') {
                        if (activeIndex >= 0) {
                            event.preventDefault();
                            const value = items[activeIndex].dataset.pickValue !== undefined
                                ? items[activeIndex].dataset.pickValue
                                : items[activeIndex].textContent;
                            onSelect(value);
                            hideDropdown(listElem);
                        }
                    }

                    if (event.key === 'Tab') {
                        if (activeIndex >= 0) {
                            const value = items[activeIndex].dataset.pickValue !== undefined
                                ? items[activeIndex].dataset.pickValue
                                : items[activeIndex].textContent;
                            onSelect(value);
                            hideDropdown(listElem);
                        } else {
                            hideDropdown(listElem);
                        }
                    }
                });
            }

            function updateActive(items, index) {
                items.forEach(i => i.classList.remove('active-suggestion'));
                items[index].classList.add('active-suggestion');
                items[index].scrollIntoView({ block: 'nearest' });
            }

            document.addEventListener('mousedown', function (event) {
                const clickedInsideSearch = event.target.closest('.position-relative');
                if (clickedInsideSearch) return;
                allSearchLists.forEach(hideDropdown);
            });

            if (segmentInput && segmentList) {
                segmentInput.addEventListener('input', function () {
                    const searchVal = this.value.toLowerCase();
                    selectedSegment = this.value.trim();
                    const filteredSegments = [...new Set(allSegmentActivity
                        .filter(item => item.segment.toLowerCase().includes(searchVal))
                        .map(item => item.segment))];

                    filterDropdown(this, segmentList, filteredSegments, (name) => {
                        segmentInput.value = name;
                        selectedSegment = name;
                        activityInput.value = '';
                        activityList.innerHTML = '';
                        activityList.style.display = 'none';
                    });
                });
                attachKeyboardNav(segmentInput, segmentList, (name) => {
                    segmentInput.value = name;
                    selectedSegment = name;
                    activityInput.value = '';
                    activityList.innerHTML = '';
                    activityList.style.display = 'none';
                });
                segmentInput.addEventListener('change', function () {
                    selectedSegment = this.value.trim();
                    activityInput.value = '';
                    activityList.innerHTML = '';
                    activityList.style.display = 'none';
                });
            }

            if (activityInput && activityList) {
                activityInput.addEventListener('input', function () {
                    const activeSegment = selectedSegment || segmentInput.value.trim();
                    if (!activeSegment) {
                        return;
                    }

                    const searchVal = this.value.toLowerCase();
                    const filteredActivities = [...new Set(allSegmentActivity
                        .filter(item => item.segment === activeSegment && item.activity.toLowerCase().includes(searchVal))
                        .map(item => item.activity))];

                    filterDropdown(this, activityList, filteredActivities, (name) => {
                        activityInput.value = name;
                    });
                });
                attachKeyboardNav(activityInput, activityList, (name) => {
                    activityInput.value = name;
                });
            }

            if (driverInput && driverList) {
                driverInput.addEventListener('input', function () {
                    const searchVal = this.value.toLowerCase();
                    const filteredDrivers = allDrivers
                        .map(d => d.name)
                        .filter(name => name.toLowerCase().includes(searchVal));

                    filterDropdown(this, driverList, filteredDrivers, (name) => {
                        driverInput.value = name;
                        const selected = allDrivers.find(d => d.name === name);
                        if (selected) {
                            driverIdInput.value = selected.id;
                        }
                    });
                });
                attachKeyboardNav(driverInput, driverList, (name) => {
                    driverInput.value = name;
                    const selected = allDrivers.find(d => d.name === name);
                    if (selected) {
                        driverIdInput.value = selected.id;
                    }
                });
            }

            if (truckInput && truckList) {
                truckInput.addEventListener('input', function () {
                    const searchVal = this.value.toLowerCase();
                    const filteredTrucks = allTrucks.filter(name => name.toLowerCase().includes(searchVal));

                    filterDropdown(this, truckList, filteredTrucks, (name) => {
                        truckInput.value = name;
                    });
                });
                attachKeyboardNav(truckInput, truckList, (name) => {
                    truckInput.value = name;
                });
            }

            if (deliverFromInput && deliverFromList) {
                deliverFromInput.addEventListener('input', function () {
                    filterDropdownRecords(
                        this,
                        deliverFromList,
                        allLocations,
                        (location) => location.location_name || '',
                        (location) => location.location_name || '',
                        (name) => {
                            deliverFromInput.value = name;
                        }
                    );
                });
                attachKeyboardNav(deliverFromInput, deliverFromList, (name) => {
                    deliverFromInput.value = name;
                });
            }

            if (deliverToInput && deliverToList) {
                deliverToInput.addEventListener('input', function () {
                    filterDropdownRecords(
                        this,
                        deliverToList,
                        allLocations,
                        (location) => location.location_name || '',
                        (location) => location.location_name || '',
                        (name) => {
                            deliverToInput.value = name;
                        }
                    );
                });
                attachKeyboardNav(deliverToInput, deliverToList, (name) => {
                    deliverToInput.value = name;
                });
            }

            function showAlert(message, icon = 'info') {
                return Swal.fire({
                    title: icon === 'success' ? 'Success' : icon === 'error' ? 'Error' : 'Notice',
                    text: message,
                    icon: icon,
                    confirmButtonText: 'OK'
                });
            }

            function resetForm() {
                form.reset();
                dataIdInput.value = '';
            }

            function fillFormFromRecord(record) {
                dataIdInput.value = record.entry_id || '';
                segmentInput.value = record.segment || '';
                selectedSegment = record.segment || '';
                activityInput.value = record.activity || '';
                dateInput.value = record.waybill_date || '';
                waybillInput.value = record.waybill || '';
                truckInput.value = record.truck || '';
                driverInput.value = record.driver || '';
                driverIdInput.value = record.driver_idNumber || '';
                customerPhInput.value = record.customer_ph || '';
                outsideInput.value = record.outside || '';
                compoundInput.value = record.compound || '';
                totalTripsInput.value = record.total_trips || '';
                operationsInput.value = record.operations || '';
                deliverFromInput.value = record.deliver_from || '';
                deliverToInput.value = record.delivered_to || '';
                remarksInput.value = record.remarks || '';
                cargoDateInput.value = record.cargo_date || '';
                const collapseEl = document.getElementById('entryForm');
                bootstrap.Collapse.getOrCreateInstance(collapseEl, {toggle:false}).show();
            }

            function getTableRowData(record) {
                return [
                    '<input type="checkbox" class="form-check-input row-checkbox">',
                    `<strong>#${record.entry_id}</strong>`,
                    record.segment || '',
                    record.activity || '',
                    record.waybill || '',
                    record.driver || '',
                    record.remarks || '',
                    `<div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="btn btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
                    </div>`
                ];
            }

            function setRowDataAttributes(row, record) {
                if (!row) return;
                row.dataset.id = record.entry_id || '';
            }

            function attachRowActions() {
                const tbody = document.querySelector('#entriesTable tbody');
                if (!tbody) return;
                tbody.addEventListener('click', function (event) {
                    const btn = event.target.closest('.btn-edit, .btn-delete');
                    if (!btn) return;
                    const row = btn.closest('tr');
                    if (!row) return;
                    const id = row.dataset.id;
                    if (!id) return;

                    if (btn.classList.contains('btn-edit')) {
                        fetch(`php/fetch/get_cargo_truck.php?id=${encodeURIComponent(id)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (!data.success) {
                                    showAlert(data.message || 'Unable to fetch record.', 'error');
                                    return;
                                }
                                fillFormFromRecord(data.record);
                            })
                            .catch(() => showAlert('Unable to fetch record.', 'error'));
                        return;
                    }

                    if (btn.classList.contains('btn-delete')) {
                        Swal.fire({
                            title: 'Delete this entry?',
                            text: 'This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (!result.isConfirmed) return;

                            const payload = new FormData();
                            payload.append('action', 'delete-cargo-truck');
                            payload.append('data_id', id);

                            fetch('php/delete/cargo_truck.php', {
                                method: 'POST',
                                body: payload
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    const rowApi = table.row(row);
                                    if (rowApi.any()) {
                                        rowApi.remove().draw(false);
                                    }
                                    showAlert(data.message || 'Record deleted.', 'success');
                                } else {
                                    showAlert(data.message || 'Delete failed.', 'error');
                                }
                            })
                            .catch(() => showAlert('Unable to delete record.', 'error'));
                        });
                    }
                });
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const action = dataIdInput.value ? 'update-cargo-truck' : 'add-cargo-truck';
                const endpoint = dataIdInput.value ? 'php/update/cargo_truck.php' : 'php/insert/cargo_truck.php';
                const payload = new FormData();
                if (dataIdInput.value) payload.append('data_id', dataIdInput.value);
                payload.append('action', action);
                payload.append('segment', segmentInput.value.trim());
                payload.append('activity', activityInput.value.trim());
                payload.append('date', dateInput.value);
                payload.append('waybill', waybillInput.value.trim());
                payload.append('truck', truckInput.value.trim());
                payload.append('driver', driverInput.value.trim());
                payload.append('driver_idNumber', driverIdInput.value.trim());
                payload.append('customer_ph', customerPhInput.value.trim());
                payload.append('outside', outsideInput.value.trim());
                payload.append('compound', compoundInput.value.trim());
                payload.append('total_trips', totalTripsInput.value.trim());
                payload.append('operations', operationsInput.value.trim());
                payload.append('deliver_from', deliverFromInput.value.trim());
                payload.append('deliver_to', deliverToInput.value.trim());
                payload.append('remarks', remarksInput.value.trim());
                payload.append('cargo_date', cargoDateInput.value);

                fetch(endpoint, {
                    method: 'POST',
                    body: payload
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        showAlert(data.message || 'Operation failed.');
                        return;
                    }
                    if (action === 'add-cargo-truck') {
                        const newRow = table.row.add(getTableRowData(data.record)).draw(false).node();
                        setRowDataAttributes(newRow, data.record);
                    } else {
                        const row = document.querySelector(`#entriesTable tbody tr[data-id="${data.record.entry_id}"]`);
                        if (row) {
                            const rowApi = table.row(row);
                            if (rowApi.any()) {
                                rowApi.data(getTableRowData(data.record)).draw(false);
                                setRowDataAttributes(rowApi.node(), data.record);
                            }
                        }
                    }
                    resetForm();
                    showAlert(data.message || 'Saved successfully.', 'success');
                })
                .catch(() => showAlert('Unable to reach server.', 'error'));
            });

            attachRowActions();
            const nav = document.querySelector('#enav');
            if (nav) nav.className = 'nav-link active';
        });
    </script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/jquery.min.js"></script>
</body>
</html>
