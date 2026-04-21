<?php $role = ucfirst(
    strtolower((string) ($_SESSION["user_type"] ?? "User")),
); ?>
<nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="bi bi-database-fill-gear"></i>
                    <span>DataEncode</span>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-section">
                    <small class="menu-title">MAIN</small>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard" id="dnav">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <?php if ($role === "Admin" || $role === "User") { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="entry" id="enav">
                                <i class="bi bi-table"></i>
                                <span>Data Entry</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="monitoring" id="mnav">
                                <i class="bi bi-broadcast-pin"></i>
                                <span>Monitoring</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($role === "Admin") { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="payroll" id="paynav">
                                <i class="bi bi-cash-coin"></i>
                                <span>Payroll</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="performance" id="pfnav">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Performance</span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($role === "Admin" || $role === "Billing") { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="billing" id="bnav">
                                <i class="bi bi-receipt"></i>
                                <span>Billing</span>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="menu-section">
                    <small class="menu-title">ACCOUNT</small>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="profile" id="pnav">
                                <i class="bi bi-person"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <?php if ($role === "Admin") { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="users" id="unav">
                                <i class="bi bi-people"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="drivers" id="drnav">
                                <i class="bi bi-person-vcard"></i>
                                <span>Drivers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings" id="snav">
                                <i class="bi bi-sliders"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout">
                                <i class="bi bi-box-arrow-left"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
