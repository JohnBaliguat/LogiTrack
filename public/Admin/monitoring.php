<?php include "php/session-check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring - DataEncode System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .monitor-card {
            border: 1px solid rgba(13, 110, 253, 0.08);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .monitor-stat {
            border-radius: 1rem;
            border: 1px solid #e9ecef;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 1rem 1.25rem;
        }

        .monitor-status {
            min-width: 110px;
        }

        .monitor-row-incomplete > * {
            background-color: #fff3cd !important;
        }

        .monitor-row-complete > * {
            background-color: #f8f9fa !important;
        }

        .monitor-muted {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include "sidebar.php"; ?>

        <div id="content">
            <?php include "navbar.php"; ?>

            <div class="main-content">
                <div class="content-header mb-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h2 class="fw-bold mb-1">Monitoring</h2>
                            <p class="text-muted mb-0">Realtime view of all today's entries. Incomplete rows are highlighted and sorted by waybill.</p>
                        </div>
                        <div class="text-lg-end">
                            <div class="monitor-muted">Auto refresh every 5 seconds</div>
                            <div class="monitor-muted">Last update: <span id="lastUpdated">Waiting for data...</span></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="monitor-stat">
                            <div class="monitor-muted">Total Entries</div>
                            <div class="fs-3 fw-bold" id="totalEntries">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="monitor-stat">
                            <div class="monitor-muted">Incomplete Entries</div>
                            <div class="fs-3 fw-bold text-warning" id="incompleteEntries">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="monitor-stat">
                            <div class="monitor-muted">Complete Entries</div>
                            <div class="fs-3 fw-bold text-success" id="completeEntries">0</div>
                        </div>
                    </div>
                </div>

                <div class="card monitor-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-broadcast-pin me-2"></i>Entry Monitoring</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="refreshButton">
                            <i class="bi bi-arrow-repeat me-1"></i>Refresh now
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="monitoringTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Entry Type</th>
                                        <th>Waybill</th>
                                        <th>Segment</th>
                                        <th>Activity</th>
                                        <th>Driver</th>
                                        <th>Status</th>
                                        <th>Missing Fields</th>
                                        <th>Updated</th>
                                        <th>Open</th>
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
            $("#mnav").attr({
                "class" : "nav-link active"
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalEntries = document.getElementById('totalEntries');
            const incompleteEntries = document.getElementById('incompleteEntries');
            const completeEntries = document.getElementById('completeEntries');
            const lastUpdated = document.getElementById('lastUpdated');
            const refreshButton = document.getElementById('refreshButton');

            const table = new DataTable('#monitoringTable', {
                order: [[2, 'asc'], [0, 'desc']],
                pageLength: 25,
                columns: [
                    { data: 'id' },
                    { data: 'entry_type' },
                    { data: 'waybill' },
                    { data: 'segment' },
                    { data: 'activity' },
                    { data: 'driver' },
                    { data: 'status' },
                    { data: 'missing_fields' },
                    { data: 'updated_at' },
                    { data: 'open' }
                ]
            });

            let isLoading = false;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatStamp(value) {
                if (!value) {
                    return '-';
                }

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

            function buildRow(record) {
                const safeWaybill = record.waybill ? escapeHtml(record.waybill) : '<span class="text-muted">No waybill</span>';
                const safeMissing = record.missing_fields.length
                    ? escapeHtml(record.missing_fields.join(', '))
                    : '<span class="text-success">Complete</span>';
                const statusBadge = record.is_complete
                    ? '<span class="badge bg-success monitor-status">Complete</span>'
                    : '<span class="badge bg-warning text-dark monitor-status">Incomplete</span>';
                const rowClass = record.is_complete ? 'monitor-row-complete' : 'monitor-row-incomplete';

                return {
                    id: `<strong>#${record.entry_id}</strong>`,
                    entry_type: escapeHtml(record.entry_type),
                    waybill: safeWaybill,
                    segment: escapeHtml(record.segment || '-'),
                    activity: escapeHtml(record.activity || '-'),
                    driver: escapeHtml(record.driver || '-'),
                    status: statusBadge,
                    missing_fields: safeMissing,
                    updated_at: formatStamp(record.modified_date || record.created_date),
                    open: `<a class="btn btn-sm btn-outline-primary" href="${escapeHtml(record.route)}"><i class="bi bi-box-arrow-up-right"></i></a>`,
                    DT_RowClass: rowClass
                };
            }

            async function loadEntries() {
                if (isLoading) {
                    return;
                }

                isLoading = true;
                refreshButton.disabled = true;

                try {
                    const response = await fetch('php/fetch/get_monitoring_entries.php', { cache: 'no-store' });
                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load monitoring data.');
                    }

                    const rows = (data.records || []).map(buildRow);
                    const incomplete = (data.records || []).filter(record => !record.is_complete).length;

                    table.clear();
                    table.rows.add(rows);
                    table.draw();

                    totalEntries.textContent = String(data.records.length);
                    incompleteEntries.textContent = String(incomplete);
                    completeEntries.textContent = String(data.records.length - incomplete);
                    lastUpdated.textContent = formatStamp(data.generated_at);
                } catch (error) {
                    lastUpdated.textContent = 'Refresh failed';
                    console.error(error);
                } finally {
                    isLoading = false;
                    refreshButton.disabled = false;
                }
            }

            refreshButton.addEventListener('click', loadEntries);
            loadEntries();
            window.setInterval(loadEntries, 5000);
        });
    </script>
</body>
</html>
