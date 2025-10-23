<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$saleId = $_GET['id'] ?? 0;
if (!$saleId) {
    header("Location: manage_sales.php");
    exit;
}

// Fetch sale info
$saleStmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$saleStmt->execute([$saleId]);
$sale = $saleStmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    echo "Sale not found.";
    exit;
}

// Fetch items
$itemStmt = $conn->prepare("SELECT si.*, p.product_name FROM sales_items si JOIN add_products p ON si.product_id = p.id WHERE si.sale_id = ?");
$itemStmt->execute([$saleId]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LUXURY JEWELRY - View Sale #<?= htmlspecialchars($saleId) ?></title>
    <link rel="stylesheet" href="./assets/compiled/css/app.css" />
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css" />
    <link rel="stylesheet" href="./assets/compiled/css/iconly.css" />
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
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer" />
                                <label class="form-check-label"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item">
                            <a href="cashier_dashboard.php" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Homepage</span>
                            </a>
                        </li>

                        <li class="sidebar-item active">
                            <a href="manage_sales.php" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Sales Management</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="reports.php" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="main" class="layout-navbar navbar-fixed">
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
                                            <h6 class="mb-0 text-gray-600" style="color: #752738 !important;">
                                                <?= htmlspecialchars($_SESSION['fullname']) ?>
                                            </h6>
                                            <p class="mb-0 text-sm text-gray-600" style="color: #752738 !important;">
                                                Cashier
                                            </p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="./assets/compiled/jpg/1.jpg" alt="User Avatar" />
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

            <div id="main-content" class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>View Sale #<?= htmlspecialchars($saleId) ?></h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="manage_sales.php">Sales Management</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">View Sale</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Sale Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Date:</strong> <?= date("F j, Y, g:i A", strtotime($sale['sale_date'])) ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Cashier ID:</strong> <?= htmlspecialchars($sale['cashier_id']) ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Payment Method:</strong> <?= htmlspecialchars($sale['payment_method']) ?>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item) : ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td>₱<?= number_format($item['price'], 2) ?></td>
                                                <td>₱<?= number_format($item['subtotal'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 text-end">
                                <p><strong>Sub Total:</strong> ₱<?= number_format($sale['sub_total'], 2) ?></p>
                                <p><strong>Tax:</strong> ₱<?= number_format($sale['tax'], 2) ?></p>
                                <h5><strong>Total:</strong> ₱<?= number_format($sale['grand_total'], 2) ?></h5>
                            </div>
                                                    <a href="print/print_receipt_sale.php?id=<?= $sale['id'] ?>" target="_blank"
                                                        class="btn btn-primary">Print Receipt</a>
                            <a href="manage_sales.php" class="btn btn-secondary">Back to Sales</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
</body>

</html>
