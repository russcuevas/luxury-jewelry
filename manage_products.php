<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_status = isset($_POST['product_status']) ? $_POST['product_status'] : 'not active';
    $supplier_id = $_POST['supplier_id'];

    $imageName = null;
    $uploadDir = 'assets/images/products/';

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['product_image']['tmp_name'];
        $originalName = basename($_FILES['product_image']['name']);
        $imageName = time() . '_' . $originalName;
        move_uploaded_file($tmp_name, $uploadDir . $imageName);

        // Delete old image
        $stmtOld = $conn->prepare("SELECT product_image FROM add_products WHERE id = :id");
        $stmtOld->bindParam(':id', $id);
        $stmtOld->execute();
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!empty($old['product_image']) && file_exists($uploadDir . $old['product_image'])) {
            unlink($uploadDir . $old['product_image']);
        }

        // ✅ SQL with new image
        $sql = "UPDATE add_products 
                SET product_name = :product_name, 
                    product_description = :product_description, 
                    product_price = :product_price, 
                    product_status = :product_status, 
                    supplier_id = :supplier_id, 
                    product_image = :product_image 
                WHERE id = :id";
    } else {
        // ✅ SQL without new image
        $sql = "UPDATE add_products 
                SET product_name = :product_name, 
                    product_description = :product_description, 
                    product_price = :product_price, 
                    product_status = :product_status, 
                    supplier_id = :supplier_id 
                WHERE id = :id";
    }

    // ✅ Now prepare statement before binding
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':product_description', $product_description);
    $stmt->bindParam(':product_price', $product_price);
    $stmt->bindParam(':product_status', $product_status);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->bindParam(':id', $id);

    if ($imageName !== null) {
        $stmt->bindParam(':product_image', $imageName);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully.'); window.location.href='manage_products.php';</script>";
    } else {
        echo "<script>alert('Failed to update product.'); window.location.href='manage_products.php';</script>";
    }
}


$stmtSuppliers = $conn->prepare("SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");
$stmtSuppliers->execute();
$suppliers = $stmtSuppliers->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->prepare('SELECT * FROM `add_products`');
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUXURY JEWELRY - Web</title>
    <link rel="stylesheet" href="assets/extensions/simple-datatables/style.css">
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

                        <li class="sidebar-item">
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

                        <li class="sidebar-item active">
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
                                <h3>Manage Products</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav
                                    aria-label="breadcrumb"
                                    class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Manage Products
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- // Basic multiple Column Form section start -->
                    <section class="section">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                </h5>
                                <a href="add_products.php" class="btn btn-primary btn-sm">
                                    Add Products +
                                </a>
                            </div>

                            <div class="card-body">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Product Image</th>
                                            <th>Product Name</th>
                                            <th>Product Description</th>
                                            <th>Product Price</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?= $count++ ?></td>

                                                <td>
                                                    <?php if (!empty($product['product_image'])): ?>
                                                        <img src="assets/images/products/<?= htmlspecialchars($product['product_image']) ?>" alt="Product Image" width="60" height="60">
                                                    <?php else: ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                <td><?= htmlspecialchars($product['product_description']) ?></td>
                                                <td>₱<?= number_format($product['product_price'], 2) ?></td>

                                                <td>
                                                    <?php if ($product['product_status'] == 'active'): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <button class="btn btn-sm btn-warning editProductBtn"
                                                        data-id="<?= $product['id'] ?>"
                                                        data-name="<?= htmlspecialchars($product['product_name']) ?>"
                                                        data-description="<?= htmlspecialchars($product['product_description']) ?>"
                                                        data-price="<?= htmlspecialchars($product['product_price']) ?>"
                                                        data-status="<?= htmlspecialchars($product['product_status']) ?>"
                                                        data-image="<?= htmlspecialchars($product['product_image']) ?>"
                                                        data-supplier="<?= htmlspecialchars($product['supplier_id']) ?>"> <!-- ✅ new -->
                                                        Edit
                                                    </button>


                                                    <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>

                                </table>
                                <!-- Edit Product Modal -->
                                <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <form method="POST" action="" enctype="multipart/form-data">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Product</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="id" id="edit_product_id">

                                                    <div class="mb-3">
                                                        <label>Current Image</label><br>
                                                        <img id="current_image_preview" src="" alt="Current Image" width="100" height="100">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Change Image</label>
                                                        <input type="file" name="product_image" class="form-control">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Product Name</label>
                                                        <input type="text" class="form-control" name="product_name" id="edit_product_name" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Description</label>
                                                        <textarea class="form-control" name="product_description" id="edit_product_description" required></textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label>Price</label>
                                                        <input type="number" step="0.01" class="form-control" name="product_price" id="edit_product_price" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Supplier</label>
                                                        <select class="form-select" name="supplier_id" id="edit_supplier_id" required>
                                                            <option value="" disabled selected>Select a supplier</option>
                                                            <?php foreach ($suppliers as $sup): ?>
                                                                <option value="<?= htmlspecialchars($sup['id']) ?>">
                                                                    <?= htmlspecialchars($sup['supplier_name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>


                                                    <div class="mb-3">
                                                        <label>Status</label>
                                                        <div class="form-check form-switch">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                id="edit_product_status"
                                                                name="product_status"
                                                                value="active">
                                                            <label class="form-check-label" for="edit_product_status">Active</label>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
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
        <script src="assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
        <script src="assets/static/js/pages/simple-datatables.js"></script>
        <script>
            $(document).on("click", ".editProductBtn", function() {
                const id = $(this).data("id");
                const name = $(this).data("name");
                const description = $(this).data("description");
                const price = $(this).data("price");
                const status = $(this).data("status");
                const image = $(this).data("image");
                const supplier = $(this).data("supplier"); // ✅ New

                // Fill fields
                $("#edit_product_id").val(id);
                $("#edit_product_name").val(name);
                $("#edit_product_description").val(description);
                $("#edit_product_price").val(price);

                // ✅ Set supplier dropdown
                $("#edit_supplier_id").val(supplier);

                // Status checkbox
                if (status === "active") {
                    $("#edit_product_status").prop("checked", true);
                } else {
                    $("#edit_product_status").prop("checked", false);
                }

                // Product image
                if (image) {
                    $("#current_image_preview").attr("src", "assets/images/products/" + image);
                } else {
                    $("#current_image_preview").attr("src", "assets/images/products/default.jpg");
                }

                // Show modal
                $("#editProductModal").modal("show");
            });
        </script>


</body>

</html>