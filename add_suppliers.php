<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = trim($_POST['supplier_id']);
    $supplier_name = trim($_POST['supplier_name']);
    $supplier_address = trim($_POST['supplier_address']);
    $phone = trim($_POST['phone']); {
        $sql = "INSERT INTO suppliers (supplier_id, supplier_name, supplier_address, phone) VALUES (:supplier_id, :supplier_name, :supplier_address, :phone)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->bindParam(':supplier_name', $supplier_name);
        $stmt->bindParam(':supplier_address', $supplier_address);
        $stmt->bindParam(':phone', $phone);

        if ($stmt->execute()) {
            echo "<script>alert('Supplier added successfully!'); window.location.href='manage_suppliers.php';</script>";
        } else {
            echo "<script>alert('Error adding category.');</script>";
        }
    }
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

                        <!-- Left: Logo -->
                        <!-- Middle: Title -->
                        <div class="flex-grow-1 text-left">
                            <span class="fw-bold fs-5" style="color: #752738">LUXURY JEWELRY</span>
                        </div>
                        <!-- Right: Theme Toggle -->
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item active">
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

                        <li class="sidebar-item">
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
                            <a href="index.html" class='sidebar-link'>
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
                            <ul class="navbar-nav ms-auto mb-lg-0">


                            </ul>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600" style="color: #752738 !important;">Kimberly Baculio Nasarita</h6>
                                            <p class="mb-0 text-sm text-gray-600" style="color: #752738 !important;">
                                                Administrator</p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="./assets/compiled/jpg/1.jpg">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem;">
                                    <li><a class="dropdown-item" href="logout.php"><i
                                                class="icon-mid bi bi-box-arrow-left me-2"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Add Suppliers</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav
                                    aria-label="breadcrumb"
                                    class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Add Suppliers
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- // Basic multiple Column Form section start -->
                    <section id="multiple-column-form">
                        <div class="row match-height">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <form method="POST" action="" class="form" data-parsley-validate>
                                                <div class="row">
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Supplier ID</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Supplier ID"
                                                                name="supplier_id"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Supplier Name</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Supplier Name"
                                                                name="supplier_name"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Supplier Address</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Supplier Address"
                                                                name="supplier_address"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Phone Number</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Phone Number"
                                                                name="phone"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                                            Save
                                                        </button>
                                                        <button
                                                            type="reset"
                                                            class="btn btn-light-secondary me-1 mb-1">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <script src="assets/static/js/components/dark.js"></script>
        <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>


        <script src="assets/compiled/js/app.js"></script>



        <script src="assets/extensions/jquery/jquery.min.js"></script>
        <script src="assets/extensions/parsleyjs/parsley.min.js"></script>
        <script src="assets/static/js/pages/parsley.js"></script>

</body>

</html>