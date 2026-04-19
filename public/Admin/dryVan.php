<?php 
include "php/session-check.php"; 
include "php/config/config.php";

$query = "SELECT entry_id, entry_type, status, customer_ph, delivered_to, return_location, size, waybill, date_hauled, driver, truck, tr, date_unloaded, remarks, waybill_empty, date_returned, type, delivered_remarks, kms, booking, seal FROM operations WHERE entry_type = 'DRY VAN ENTRY' AND DATE(created_date) = CURDATE() ORDER BY entry_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dry Van Data Entry - DataEncode System</title>
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
                    <div class="col-md-5">
                        <div class="content-header mb-4">
                            <h2 class="fw-bold">Dry Van Data Entry</h2>
                            <p class="text-muted">Create, view, and manage dry van entries efficiently.</p>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>New Dry Van Entry</h5>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#entryForm">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse" id="entryForm">
                                <div class="card-body">
                                    <form id="dataEntryForm">
                                        <input type="hidden" id="data_id" name="data_id" value="">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="status" class="form-label">Status *</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="">-- Select Status --</option>
                                                    <option value="Loaded Import / Empty Export">Loaded Import / Empty Export</option>
                                                    <option value="Empty Import / Loaded Export">Empty Import / Loaded Export</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 position-relative">
                                                <label for="customer_ph" class="form-label">Customer (PH) *</label>
                                                <input type="text" class="form-control" id="customer_ph" name="customer_ph" required>
                                                <ul id="customerList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                            </div>
                                            <div class="col-md-6 position-relative">
                                                <label for="delivered_to" class="form-label">Delivery Location</label>
                                                <input type="text" class="form-control" id="delivered_to" name="delivered_to">
                                                <ul id="deliveredToList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                            </div>
                                            <div class="col-md-6 position-relative">
                                                <label for="return_location" class="form-label">Return Location</label>
                                                <input type="text" class="form-control" id="return_location" name="return_location">
                                                <ul id="returnLocationList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="size" class="form-label">Size</label>
                                                <input type="text" class="form-control" id="size" name="size">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="kms" class="form-label">KMS</label>
                                                <input type="text" class="form-control" id="kms" name="kms">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="booking" class="form-label">Booking</label>
                                                <input type="text" class="form-control" id="booking" name="booking">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="seal" class="form-label">Seal</label>
                                                <input type="text" class="form-control" id="seal" name="seal">
                                            </div>

                                            <!-- Loaded Import / Empty Export Section -->
                                            <div id="loadedExportSection" class="col-12">
                                                <hr class="my-3">
                                                <h6 class="fw-bold mb-3">Loaded Import / Empty Export</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="waybill" class="form-label">Trip Receipt (Waybill)</label>
                                                        <input type="text" class="form-control" id="waybill" name="waybill">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="date_hauled" class="form-label">Date Hauled</label>
                                                        <input type="date" class="form-control" id="date_hauled" name="date_hauled">
                                                    </div>
                                                    <div class="col-md-6 position-relative">
                                                        <label for="driver" class="form-label">Driver</label>
                                                        <input type="text" class="form-control" id="driver" name="driver">
                                                        <ul id="driverList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                                        <input type="hidden" id="driver_idNumber" name="driver_idNumber">
                                                    </div>
                                                    <div class="col-md-6 position-relative">
                                                        <label for="truck" class="form-label">Truck</label>
                                                        <input type="text" class="form-control" id="truck" name="truck">
                                                        <ul id="truckList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                                    </div>
                                                    <div class="col-md-6 position-relative">
                                                        <label for="tr" class="form-label">Trailer</label>
                                                        <input type="text" class="form-control" id="tr" name="tr">
                                                        <ul id="trList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="date_unloaded" class="form-label">Date Unloaded</label>
                                                        <input type="date" class="form-control" id="date_unloaded" name="date_unloaded">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="remarks" class="form-label">Remarks</label>
                                                        <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Empty Import / Loaded Export Section -->
                                            <div id="emptyImportSection" class="col-12">
                                                <hr class="my-3">
                                                <h6 class="fw-bold mb-3">Empty Import / Loaded Export</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="waybill_empty" class="form-label">Trip Receipt (Waybill)</label>
                                                        <input type="text" class="form-control" id="waybill_empty" name="waybill_empty">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="date_returned" class="form-label">Date Returned</label>
                                                        <input type="date" class="form-control" id="date_returned" name="date_returned">
                                                    </div>
                                                    <div class="col-md-6 position-relative">
                                                        <label for="driver_return" class="form-label">Driver</label>
                                                        <input type="text" class="form-control" id="driver_return" name="driver_return">
                                                        <ul id="driverReturnList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                                        <input type="hidden" id="driver_return_idNumber" name="driver_return_idNumber">
                                                    </div>
                                                    <div class="col-md-6 position-relative">
                                                        <label for="truck_return" class="form-label">Truck</label>
                                                        <input type="text" class="form-control" id="truck_return" name="truck_return">
                                                        <ul id="truckReturnList" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 220px; overflow-y: auto;"></ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="type" class="form-label">Type</label>
                                                        <select class="form-select" id="type" name="type">
                                                            <option value="">-- Select Type --</option>
                                                            <option value="EXPORT">EXPORT</option>
                                                            <option value="IMPORT">IMPORT</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="delivered_remarks" class="form-label">Remarks</label>
                                                        <textarea class="form-control" id="delivered_remarks" name="delivered_remarks" rows="2"></textarea>
                                                    </div>
                                                </div>
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
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">All Entries</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="entriesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Waybill</th>
                                                <th>Van</th>
                                                <th>Driver</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['entry_id']); ?></td>
                                                        <td><?php echo htmlspecialchars(($row['date_hauled'] ?? '') !== '' ? $row['date_hauled'] : ($row['date_returned'] ?? '')); ?></td>
                                                        <td><?php echo htmlspecialchars(($row['waybill'] ?? '') !== '' ? $row['waybill'] : ($row['waybill_empty'] ?? '')); ?></td>
                                                        <td><?php echo htmlspecialchars(($row['tr'] ?? '') !== '' ? $row['tr'] : ($row['truck'] ?? '')); ?></td>
                                                        <td><?php echo htmlspecialchars($row['driver'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars(($row['remarks'] ?? '') !== '' ? $row['remarks'] : ($row['delivered_remarks'] ?? '')); ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info edit-btn" data-id="<?php echo $row['entry_id']; ?>"><i class="bi bi-pencil"></i></button>
                                                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['entry_id']; ?>"><i class="bi bi-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No entries found for today</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {

            const form = document.getElementById("dataEntryForm");
            const dataIdInput = document.getElementById("data_id");
            const statusSelect = document.getElementById("status");
            const loadedExportSection = document.getElementById("loadedExportSection");
            const emptyImportSection = document.getElementById("emptyImportSection");

            const customerPhInput = document.getElementById("customer_ph");
            const customerList = document.getElementById("customerList");
            const deliveredToInput = document.getElementById("delivered_to");
            const deliveredToList = document.getElementById("deliveredToList");
            const returnLocationInput = document.getElementById("return_location");
            const returnLocationList = document.getElementById("returnLocationList");
            const driverInput = document.getElementById("driver");
            const driverList = document.getElementById("driverList");
            const driverReturnInput = document.getElementById("driver_return");
            const driverReturnList = document.getElementById("driverReturnList");
            const truckInput = document.getElementById("truck");
            const truckList = document.getElementById("truckList");
            const truckReturnInput = document.getElementById("truck_return");
            const truckReturnList = document.getElementById("truckReturnList");
            const trInput = document.getElementById("tr");
            const trList = document.getElementById("trList");

            let allCustomers = [];
            let allDrivers = [];
            let allTrucks = [];
            let allTrailers = [];

            // Fetch lookup data
            fetch("php/fetch/get_locations.php")
                .then(res => res.json())
                .then(data => { allCustomers = Array.isArray(data) ? data : []; })
                .catch(() => { allCustomers = []; });

            fetch("php/fetch/get_drivers.php")
                .then(res => res.json())
                .then(data => { allDrivers = Array.isArray(data) ? data : []; })
                .catch(() => { allDrivers = []; });

            fetch("php/fetch/get_trucks.php")
                .then(res => res.json())
                .then(data => { allTrucks = Array.isArray(data) ? data : []; })
                .catch(() => { allTrucks = []; });

            fetch("php/fetch/get_trailers.php")
                .then(res => res.json())
                .then(data => { allTrailers = Array.isArray(data) ? data : []; })
                .catch(() => { allTrailers = []; });

            // Helper function to hide dropdown
            function hideDropdown(listElem) {
                if (listElem) {
                    listElem.style.display = "none";
                    listElem.innerHTML = "";
                }
            }

            // Helper function to show dropdown
            function showDropdown(listElem) {
                if (listElem) {
                    listElem.style.maxHeight = "240px";
                    listElem.style.overflowY = "auto";
                    listElem.style.overflowX = "hidden";
                    listElem.style.display = "block";
                }
            }

            // Generic filter function
            function filterAndDisplay(input, listElem, dataArray, getDisplay, onSelect) {
                const searchVal = input.value.trim().toLowerCase();
                listElem.innerHTML = "";
                
                if (!searchVal) {
                    hideDropdown(listElem);
                    return;
                }
                
                const filtered = dataArray.filter(item => 
                    (getDisplay(item) || "").toLowerCase().includes(searchVal)
                );
                
                if (filtered.length === 0) {
                    hideDropdown(listElem);
                    return;
                }
                
                filtered.forEach((item, index) => {
                    const li = document.createElement("li");
                    li.className = "list-group-item list-group-item-action";
                    const display = getDisplay(item);
                    li.textContent = display;
                    li.dataset.pickValue = display;
                    if (index === 0) li.classList.add("active-suggestion");
                    li.addEventListener("mousedown", (event) => {
                        event.preventDefault();
                        onSelect(item);
                        hideDropdown(listElem);
                    });
                    listElem.appendChild(li);
                });
                
                showDropdown(listElem);
            }

            function attachKeyboardNav(inputElem, listElem, onSelect) {
                if (!inputElem || !listElem) return;

                let activeIndex = 0;

                inputElem.addEventListener("keydown", function(e) {
                    const items = listElem.querySelectorAll("li");

                    if (e.key === "Escape") {
                        hideDropdown(listElem);
                        return;
                    }

                    if (!items.length) {
                        return;
                    }

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
                            onSelect(activeItem.dataset.pickValue || activeItem.textContent);
                            hideDropdown(listElem);
                        }
                    } else if (e.key === "Tab") {
                        const activeItem = items[activeIndex];
                        if (activeItem) {
                            onSelect(activeItem.dataset.pickValue || activeItem.textContent);
                            hideDropdown(listElem);
                        } else {
                            hideDropdown(listElem);
                        }
                    }
                });

                inputElem.addEventListener("input", function() {
                    activeIndex = 0;
                });

                function updateActive(items, index) {
                    items.forEach(item => item.classList.remove("active-suggestion"));
                    if (items[index]) {
                        items[index].classList.add("active-suggestion");
                        items[index].scrollIntoView({ block: "nearest" });
                    }
                }
            }

            // Customer PH
            customerPhInput.addEventListener("input", function() {
                filterAndDisplay(this, customerList, allCustomers, 
                    loc => loc.location_name || "",
                    item => { customerPhInput.value = item.location_name; }
                );
            });

            deliveredToInput.addEventListener("input", function() {
                filterAndDisplay(this, deliveredToList, allCustomers,
                    loc => loc.location_name || "",
                    item => { deliveredToInput.value = item.location_name; }
                );
            });

            returnLocationInput.addEventListener("input", function() {
                filterAndDisplay(this, returnLocationList, allCustomers,
                    loc => loc.location_name || "",
                    item => { returnLocationInput.value = item.location_name; }
                );
            });

            // Driver (Loaded Export)
            driverInput.addEventListener("input", function() {
                document.getElementById("driver_idNumber").value = "";
                filterAndDisplay(this, driverList, allDrivers,
                    drv => drv.name || "",
                    item => {
                        driverInput.value = item.name || "";
                        document.getElementById("driver_idNumber").value = item.id || "";
                    }
                );
            });

            // Driver (Empty Import)
            driverReturnInput.addEventListener("input", function() {
                document.getElementById("driver_return_idNumber").value = "";
                filterAndDisplay(this, driverReturnList, allDrivers,
                    drv => drv.name || "",
                    item => {
                        driverReturnInput.value = item.name || "";
                        document.getElementById("driver_return_idNumber").value = item.id || "";
                    }
                );
            });

            // Truck (Loaded Export)
            truckInput.addEventListener("input", function() {
                filterAndDisplay(this, truckList, allTrucks,
                    trk => trk || "",
                    item => { truckInput.value = item || ""; }
                );
            });

            // Truck (Empty Import)
            truckReturnInput.addEventListener("input", function() {
                filterAndDisplay(this, truckReturnList, allTrucks,
                    trk => trk || "",
                    item => { truckReturnInput.value = item || ""; }
                );
            });

            // Trailer
            trInput.addEventListener("input", function() {
                filterAndDisplay(this, trList, allTrailers,
                    trl => trl.trailer_name || "",
                    item => { trInput.value = item.trailer_name; }
                );
            });

            // Close dropdowns when clicking outside
            document.addEventListener("mousedown", function(event) {
                if (!event.target.closest(".position-relative")) {
                    [customerList, deliveredToList, returnLocationList, driverList, driverReturnList, truckList, truckReturnList, trList].forEach(hideDropdown);
                }
            });

            attachKeyboardNav(customerPhInput, customerList, (value) => {
                customerPhInput.value = value;
            });

            attachKeyboardNav(deliveredToInput, deliveredToList, (value) => {
                deliveredToInput.value = value;
            });

            attachKeyboardNav(returnLocationInput, returnLocationList, (value) => {
                returnLocationInput.value = value;
            });

            attachKeyboardNav(driverInput, driverList, (value) => {
                driverInput.value = value;
                const selected = allDrivers.find(item => item.name === value);
                document.getElementById("driver_idNumber").value = selected ? selected.id : "";
            });

            attachKeyboardNav(driverReturnInput, driverReturnList, (value) => {
                driverReturnInput.value = value;
                const selected = allDrivers.find(item => item.name === value);
                document.getElementById("driver_return_idNumber").value = selected ? selected.id : "";
            });

            attachKeyboardNav(truckInput, truckList, (value) => {
                truckInput.value = value;
            });

            attachKeyboardNav(truckReturnInput, truckReturnList, (value) => {
                truckReturnInput.value = value;
            });

            attachKeyboardNav(trInput, trList, (value) => {
                trInput.value = value;
            });

            // Form submission
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                
                // Validate status is selected
                if (!statusSelect.value) {
                    Swal.fire("Error!", "Please select a status.", "error");
                    return;
                }

                const formData = new FormData(form);
                formData.append("action", dataIdInput.value ? "update-dry-van" : "add-dry-van");

                fetch("php/insert/dry_van.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Success!", data.message, "success").then(() => {
                            form.reset();
                            dataIdInput.value = "";
                            location.reload();
                        });
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                })
                .catch(err => Swal.fire("Error!", "An error occurred while saving.", "error"));
            });

            // Edit button
            $(document).on("click", ".edit-btn", function() {
                const id = $(this).data("id");
                fetch("php/fetch/get_dry_van.php?id=" + id)
                    .then(res => res.json())
                    .then(record => {
                        if (record) {
                            dataIdInput.value = record.entry_id;
                            document.getElementById("status").value = record.status;
                            customerPhInput.value = record.customer_ph;
                            document.getElementById("delivered_to").value = record.delivered_to || "";
                            document.getElementById("return_location").value = record.return_location || "";
                            document.getElementById("size").value = record.size || "";
                            document.getElementById("kms").value = record.kms || "";
                            document.getElementById("booking").value = record.booking || "";
                            document.getElementById("seal").value = record.seal || "";

                            // Load both sections with data
                            document.getElementById("waybill").value = record.waybill || "";
                            document.getElementById("date_hauled").value = record.date_hauled || "";
                            driverInput.value = record.driver || "";
                            truckInput.value = record.truck || "";
                            trInput.value = record.tr || "";
                            document.getElementById("date_unloaded").value = record.date_unloaded || "";
                            document.getElementById("remarks").value = record.remarks || "";

                            document.getElementById("waybill_empty").value = record.waybill_empty || "";
                            document.getElementById("date_returned").value = record.date_returned || "";
                            driverReturnInput.value = record.driver || "";
                            truckReturnInput.value = record.truck || "";
                            document.getElementById("type").value = record.type || "";
                            document.getElementById("delivered_remarks").value = record.delivered_remarks || "";

                            document.querySelector('[data-bs-target="#entryForm"]').click();
                        }
                    });
            });

            // Delete button
            $(document).on("click", ".delete-btn", function() {
                const id = $(this).data("id");
                Swal.fire({
                    title: "Delete Entry?",
                    text: "This action cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545"
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch("php/delete/dry_van.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "entry_id=" + id
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Deleted!", "", "success").then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
