<?php 
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUXURY JEWELRY - Web</title>
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./assets/compiled/css/iconly.css">
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">

                        <div class="flex-grow-1 text-left">
                            <span class="fw-bold fs-5" style="color: #752738">LUXURY JEWELRY</span>
                        </div>

                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item active ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="add_suppliers.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Add Suppliers</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="manage_suppliers.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Manage Suppliers</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="add_categories.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Add Categories</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="manage_categories.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Manage Categories</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="add_products.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Add Products</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="manage_products.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Manage Products</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="add_users.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Add Users</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="manage_users.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Manage Users</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block d-xl-none">
                            <i class="bi bi-justify fs-3"></i>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-lg-0"></ul>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <!-- ✅ Dynamic user -->
                                            <h6 class="mb-0 text-gray-600" style="color: #752738 !important;">
                                                <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                                            </h6>
                                            <p class="mb-0 text-sm text-gray-600" style="color: #752738 !important;">
                                                Administrator
                                            </p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="./assets/compiled/jpg/1.jpg" alt="User Avatar">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem;">
                                    <li>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>

            <div id="main-content">
                <div class="page-heading">
                    <h3>Dashboard</h3>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="assets/static/js/pages/dashboard.js"></script>

</body>
</html>
