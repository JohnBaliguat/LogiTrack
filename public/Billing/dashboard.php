<?php include "php/session-check.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Dashboard - DataEncode System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="wrapper">
        <?php include "sidebar.php"; ?>

        <div id="content">
            <?php include "navbar.php"; ?>

            <div class="main-content">
                <div class="content-header mb-4">
                    <h2 class="fw-bold">Billing Dashboard</h2>
                    <p class="text-muted">Billing export overview and quick access to CSV generation.</p>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card stat-card-blue">
                            <div class="stat-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                            <div class="stat-content">
                                <h3 id="todayRows">0</h3>
                                <p>Today's Billing Rows</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card stat-card-green">
                            <div class="stat-icon"><i class="bi bi-upc-scan"></i></div>
                            <div class="stat-content">
                                <h3 id="todaySkus">0</h3>
                                <p>Today's Billing SKU</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="billing" class="text-decoration-none">
                            <div class="stat-card stat-card-orange">
                                <div class="stat-icon"><i class="bi bi-download"></i></div>
                                <div class="stat-content">
                                    <h3>Export</h3>
                                    <p>Generate billing CSV</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        $(document).ready(function () {
            $("#dnav").attr({ "class" : "nav-link active" });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            try {
                const now = new Date();
                const today = `${String(now.getMonth() + 1).padStart(2, '0')}/${String(now.getDate()).padStart(2, '0')}/${now.getFullYear()}`;
                const params = new URLSearchParams({ date_from: today, date_to: today });
                const response = await fetch(`php/fetch/get_billing_summary.php?${params.toString()}`, { cache: 'no-store' });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('todayRows').textContent = String(data.summary?.total_rows ?? 0);
                    document.getElementById('todaySkus').textContent = String(data.summary?.total_skus ?? 0);
                }
            } catch (error) {
                console.error(error);
            }
        });
    </script>
</body>
</html>
