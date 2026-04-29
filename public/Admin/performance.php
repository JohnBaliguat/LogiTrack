<?php include "php/session-check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Performance - DataEncode System</title>
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
                <div class="content-header mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <h2 class="fw-bold mb-1">User Performance</h2>
                        <p class="text-muted mb-0">Performance is based on entries where <code>operations.created_by = user.user_idNumber</code>.</p>
                    </div>
                    <div class="text-lg-end">
                        <small class="text-muted d-block">Last refresh</small>
                        <strong id="lastUpdated">Waiting for data...</strong>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-card-blue">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-content">
                                <h3 id="totalUsersCount">0</h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-card-green">
                            <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                            <div class="stat-content">
                                <h3 id="usersWithEntriesCount">0</h3>
                                <p>Users With Entries</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-card-orange">
                            <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="stat-content">
                                <h3 id="totalEntriesCount">0</h3>
                                <p>Total Entries</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card stat-card-purple">
                            <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
                            <div class="stat-content">
                                <h3 id="todayEntriesCount">0</h3>
                                <p>Entries Today</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Top User Entries</h5>
                                <small class="text-muted">Top 10 by total entries</small>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceChart" height="110"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Top Performer</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-muted" id="topPerformerEmpty">Loading performer...</div>
                                <div id="topPerformerCard" style="display:none;">
                                    <h4 class="fw-bold mb-1" id="topPerformerName">-</h4>
                                    <p class="text-muted mb-2" id="topPerformerMeta">-</p>
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-primary" id="topPerformerRole">-</span>
                                        <span class="badge bg-success" id="topPerformerStatus">-</span>
                                    </div>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Entries</span>
                                            <strong id="topPerformerTotal">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span>Today's Entries</span>
                                            <strong id="topPerformerToday">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span>Last Entry</span>
                                            <strong id="topPerformerLast">-</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Entry Performance by User</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="performanceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>ID Number</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Total Entries</th>
                                        <th>Today</th>
                                        <th>RV</th>
                                        <th>Others</th>
                                        <th>DPC_KDI</th>
                                        <th>Cargo</th>
                                        <th>Dry Van</th>
                                        <th>Avg Speed <small class="text-muted fw-normal">(entries/day)</small></th>
                                        <th>Last Entry</th>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        $(document).ready(function () {
            $("#pfnav").attr({ "class": "nav-link active" });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = new DataTable('#performanceTable', {
                order: [[4, 'desc']],
                pageLength: 10
            });

            const chartCanvas = document.getElementById('performanceChart');
            let performanceChart = null;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatDateTime(value) {
                if (!value) return '-';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return escapeHtml(value);
                return date.toLocaleString();
            }

            function updateSummary(summary) {
                document.getElementById('totalUsersCount').textContent = String(summary.total_users ?? 0);
                document.getElementById('usersWithEntriesCount').textContent = String(summary.users_with_entries ?? 0);
                document.getElementById('totalEntriesCount').textContent = String(summary.total_entries ?? 0);
                document.getElementById('todayEntriesCount').textContent = String(summary.today_entries ?? 0);

                const top = summary.top_performer;
                const emptyEl = document.getElementById('topPerformerEmpty');
                const cardEl = document.getElementById('topPerformerCard');

                if (!top) {
                    emptyEl.style.display = '';
                    emptyEl.textContent = 'No user entries found.';
                    cardEl.style.display = 'none';
                    return;
                }

                emptyEl.style.display = 'none';
                cardEl.style.display = '';
                document.getElementById('topPerformerName').textContent = top.full_name || top.user_name || top.user_idNumber;
                document.getElementById('topPerformerMeta').textContent = `${top.user_name || '-'} | ${top.user_idNumber || '-'}`;
                document.getElementById('topPerformerRole').textContent = top.user_type || '-';
                document.getElementById('topPerformerStatus').textContent = top.user_accountStat || '-';
                document.getElementById('topPerformerTotal').textContent = String(top.total_entries ?? 0);
                document.getElementById('topPerformerToday').textContent = String(top.today_entries ?? 0);
                document.getElementById('topPerformerLast').textContent = formatDateTime(top.last_entry_date);
            }

            function updateChart(chart) {
                if (performanceChart) {
                    performanceChart.destroy();
                }

                performanceChart = new Chart(chartCanvas, {
                    type: 'bar',
                    data: {
                        labels: chart.labels || [],
                        datasets: [{
                            label: 'Total Entries',
                            data: chart.values || [],
                            backgroundColor: 'rgba(13, 110, 253, 0.7)',
                            borderColor: '#0d6efd',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            function updateTable(rows) {
                table.clear();

                rows.forEach(row => {
                    const statusClass = row.user_accountStat === 'Active' ? 'bg-success' : 'bg-secondary';

                    table.row.add([
                        `<strong>${escapeHtml(row.full_name || '-')}</strong><br><small class="text-muted">${escapeHtml(row.user_name || '-')}</small>`,
                        escapeHtml(row.user_idNumber || '-'),
                        `<span class="badge bg-primary">${escapeHtml(row.user_type || '-')}</span>`,
                        `<span class="badge ${statusClass}">${escapeHtml(row.user_accountStat || '-')}</span>`,
                        String(row.total_entries ?? 0),
                        String(row.today_entries ?? 0),
                        String(row.rv_entries ?? 0),
                        String(row.others_entries ?? 0),
                        String(row.dpc_entries ?? 0),
                        String(row.cargo_entries ?? 0),
                        String(row.dry_van_entries ?? 0),
                        row.avg_entries_per_day > 0 ? Number(row.avg_entries_per_day).toFixed(2) : '-',
                        formatDateTime(row.last_entry_date)
                    ]);
                });

                table.draw();
            }

            async function loadPerformance() {
                try {
                    const response = await fetch('php/fetch/get_user_performance.php', { cache: 'no-store' });
                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load user performance.');
                    }

                    updateSummary(data.summary || {});
                    updateChart(data.chart || {});
                    updateTable(data.rows || []);
                    document.getElementById('lastUpdated').textContent = formatDateTime(data.generated_at);
                } catch (error) {
                    document.getElementById('lastUpdated').textContent = 'Refresh failed';
                    console.error(error);
                }
            }

            loadPerformance();
            window.setInterval(loadPerformance, 30000);
        });
    </script>
</body>
</html>
