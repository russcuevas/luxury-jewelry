<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_status = isset($_POST['product_status']) && $_POST['product_status'] === 'active' ? 'active' : 'not active';
    $product_category = $_POST['product_category'];
    $supplier_id = $_POST['supplier_id']; // ✅ New line

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileNameClean = preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($fileName));
        $uploadFileDir = './assets/images/products/';
        $destPath = $uploadFileDir . $fileNameClean;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // ✅ Updated query to include supplier_id
            $query = "INSERT INTO add_products 
                      (product_image, product_name, product_description, product_price, product_status, product_category, supplier_id) 
                      VALUES 
                      (:product_image, :product_name, :product_description, :product_price, :product_status, :product_category, :supplier_id)";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':product_image', $fileNameClean);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':product_description', $product_description);
            $stmt->bindParam(':product_price', $product_price);
            $stmt->bindParam(':product_status', $product_status);
            $stmt->bindParam(':product_category', $product_category);
            $stmt->bindParam(':supplier_id', $supplier_id); // ✅ New binding

            if ($stmt->execute()) {
                echo "<script>alert('Product added successfully.'); window.location.href='manage_products.php';</script>";
                exit;
            } else {
                echo "<script>alert('Database insert failed.');</script>";
            }
        } else {
            echo "<script>alert('File upload failed.');</script>";
        }
    } else {
        echo "<script>alert('Please select a product image.');</script>";
    }
}


$stmt = $conn->query("SELECT id, category FROM add_categories ORDER BY category ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtSuppliers = $conn->query("SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");
$suppliers = $stmtSuppliers->fetchAll(PDO::FETCH_ASSOC);
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

                        <li class="sidebar-item active">
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
                                <h3>Add Products</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav
                                    aria-label="breadcrumb"
                                    class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Add Products
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
                                            <form method="POST" action="" enctype="multipart/form-data" class="form" data-parsley-validate>
                                                <div class="row">
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="product_image" class="form-label">Product Image</label>
                                                            <input
                                                                type="file"
                                                                class="form-control"
                                                                id="product_image"
                                                                name="product_image"
                                                                accept="image/*"
                                                                onchange="previewImage(event)" />

                                                            <!-- Preview Box -->
                                                            <div id="image_preview_box" class="card mt-3 shadow-sm border-0" style="display: none;">
                                                                <div class="card-body text-center">
                                                                    <p class="text-muted mb-2">Image Preview</p>
                                                                    <img id="image_preview" src="#" alt="Image Preview" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                                                                        <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Product Categories</label>
                                                                <select name="product_category" id="product_category" class="form-select" required>
                                                                    <option value="" disabled selected>Select a category</option>
                                                                    <?php foreach ($categories as $cat): ?>
                                                                        <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['category']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Product Name</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Product Name"
                                                                name="product_name"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                                                                        <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Product Description</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Product Description"
                                                                name="product_description"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="" class="form-label">Product Price</label>
                                                            <input
                                                                type="text"
                                                                id=""
                                                                class="form-control"
                                                                placeholder="Product Price"
                                                                name="product_price"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                        <div class="col-md-6">
                                                            <input type="hidden" name="product_status" value="not active">
                                                            <div class="form-group form-check form-switch mt-4">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    id="product_status"
                                                                    name="product_status"
                                                                    value="active">
                                                                <label class="form-check-label" for="product_status">Product Status (Active)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
    <div class="form-group mandatory">
        <label for="supplier_id" class="form-label">Supplier</label>
        <select name="supplier_id" id="supplier_id" class="form-select" required>
            <option value="" disabled selected>Select a supplier</option>
            <?php foreach ($suppliers as $sup): ?>
                <option value="<?= htmlspecialchars($sup['id']) ?>">
                    <?= htmlspecialchars($sup['supplier_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
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
        <script>
            function previewImage(event) {
                const input = event.target;
                const previewBox = document.getElementById('image_preview_box');
                const previewImage = document.getElementById('image_preview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        previewBox.style.display = 'block';
                    };

                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewBox.style.display = 'none';
                }
            }
        </script>

</body>

</html>