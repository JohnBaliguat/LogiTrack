<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <!-- <div class="dropdown me-3">
                            <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="#">New entry added</a></li>
                                <li><a class="dropdown-item" href="#">Report generated</a></li>
                                <li><a class="dropdown-item" href="#">User registered</a></li>
                            </ul>
                        </div> -->

                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=0D6EFD&color=fff" alt="User" class="rounded-circle me-2" width="32" height="32">
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>