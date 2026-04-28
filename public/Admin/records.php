<?php include "php/session-check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records - DataEncode System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="wrapper">
        <?php include "sidebar.php"; ?>

        <div id="content">
            <?php include "navbar.php"; ?>

            <div class="main-content">
                <div class="content-header mb-4">
                    <h2 class="fw-bold">Records</h2>
                    <p class="text-muted">View saved records by entry type and export the filtered result to Excel.</p>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Record Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="recordsFilterForm" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="dateFrom" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="dateFrom" required>
                            </div>
                            <div class="col-md-3">
                                <label for="dateTo" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="dateTo" required>
                            </div>
                            <div class="col-md-3">
                                <label for="entryType" class="form-label">Entry Type</label>
                                <select class="form-select" id="entryType">
                                    <option value="ALL">All Entries</option>
                                    <option value="RV ENTRY">RV Entry</option>
                                    <option value="DRY VAN ENTRY">Dry Van Entry</option>
                                    <option value="OTHERS ENTRY">Others Entry</option>
                                    <option value="DPC_KDs & OPM ENTRY">DPC_KDs & OPM Entry</option>
                                    <option value="CARGO TRUCK ENTRY">Cargo Truck Entry</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" id="previewButton">
                                    <i class="bi bi-search me-1"></i>Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Generate Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card-simple">
                            <div class="stat-icon-simple bg-primary"><i class="bi bi-archive"></i></div>
                            <div><h3 id="totalRecords">0</h3><p>Total Records</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card-simple">
                            <div class="stat-icon-simple bg-success"><i class="bi bi-tags"></i></div>
                            <div><h3 id="selectedType">All Entries</h3><p>Selected Entry</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card-simple">
                            <div class="stat-icon-simple bg-warning"><i class="bi bi-calendar-range"></i></div>
                            <div><h3 id="coveredDates">-</h3><p>Covered Dates</p></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Record List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="recordsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Entry Type</th>
                                        <th>Customer / Location</th>
                                        <th>Waybill</th>
                                        <th>Van</th>
                                        <th>Driver</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
    <script>
        $(document).ready(function () {
            $("#rnav").attr({ "class" : "nav-link active" });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('recordsFilterForm');
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            const entryTypeInput = document.getElementById('entryType');
            const previewButton = document.getElementById('previewButton');
            const totalRecords = document.getElementById('totalRecords');
            const selectedType = document.getElementById('selectedType');
            const coveredDates = document.getElementById('coveredDates');

            const table = new DataTable('#recordsTable', {
                order: [[8, 'desc'], [0, 'desc']],
                pageLength: 25
            });

            const today = new Date().toISOString().slice(0, 10);
            dateFromInput.value = today;
            dateToInput.value = today;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatStamp(value) {
                if (!value) return '-';

                const date = new Date(value);
                if (Number.isNaN(date.getTime())) {
                    return escapeHtml(value);
                }

                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');

                return `${month}/${day}/${year} ${hours}:${minutes}:${seconds}`;
            }

            function buildParams() {
                return new URLSearchParams({
                    date_from: dateFromInput.value,
                    date_to: dateToInput.value,
                    entry_type: entryTypeInput.value
                });
            }

            function renderMultiLine(values, emptyLabel = '-') {
                if (!Array.isArray(values) || !values.length) {
                    return `<span class="text-muted">${escapeHtml(emptyLabel)}</span>`;
                }

                return values.map(value => `<div>${escapeHtml(value)}</div>`).join('');
            }

            function updateSummary(records) {
                totalRecords.textContent = String(records.length);
                selectedType.textContent = entryTypeInput.value === 'ALL' ? 'All Entries' : entryTypeInput.value;
                coveredDates.textContent = `${dateFromInput.value} to ${dateToInput.value}`;
            }

            async function loadRecords() {
                const response = await fetch(`php/fetch/get_records.php?${buildParams().toString()}`, {
                    cache: 'no-store'
                });
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load records.');
                }

                table.clear();

                data.records.forEach(record => {
                    table.row.add([
                        `<strong>#${record.entry_id}</strong>`,
                        escapeHtml(record.entry_type),
                        record.customer ? escapeHtml(record.customer) : '<span class="text-muted">-</span>',
                        renderMultiLine(record.waybills, 'No waybill'),
                        record.van ? escapeHtml(record.van) : '<span class="text-muted">-</span>',
                        renderMultiLine(record.drivers),
                        record.status ? escapeHtml(record.status) : '<span class="text-muted">-</span>',
                        record.remarks ? escapeHtml(record.remarks) : '<span class="text-muted">-</span>',
                        formatStamp(record.created_date),
                        formatStamp(record.modified_date || record.created_date)
                    ]);
                });

                table.draw();
                updateSummary(data.records || []);
            }

            previewButton.addEventListener('click', function () {
                loadRecords().catch(error => {
                    coveredDates.textContent = error.message;
                });
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                window.location.href = `php/fetch/export_records_excel.php?${buildParams().toString()}`;
            });

            loadRecords().catch(console.error);
        });
    </script>
</body>
</html>
