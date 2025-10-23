<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$filterDate = $_GET['date'] ?? '';

$sql = "SELECT * FROM sales";
$params = [];

if ($filterDate) {
    $sql .= " WHERE DATE(sale_date) = ?";
    $params[] = $filterDate;
}

$sql .= " ORDER BY sale_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LUXURY JEWELRY - Manage Sales</title>
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
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer" />
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
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                            aria-label="Toggle navigation">
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
                            <h3>Sales Management</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="cashier_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Sales Management</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <a href="cashier_dashboard.php" class="btn btn-primary">Add sales in dashboard</a>
                            <form method="get" class="d-flex align-items-center" style="gap: 0.5rem;">
                                <input type="date" name="date" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($filterDate) ?>" />
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            </form>
                        </div>

                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Cashier ID</th>
                                        <th>Payment Method</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sales)) : ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No sales found.</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($sales as $sale) : ?>
                                            <tr>
                                                <td><?= $sale['id'] ?></td>
                                                <td><?= date("F j, Y, g:i A", strtotime($sale['sale_date'])) ?></td>
                                                <td><?= htmlspecialchars($sale['cashier_id']) ?></td>
                                                <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                                                <td>₱<?= number_format($sale['grand_total'], 2) ?></td>
                                                <td>
                                                    <a href="view_sale.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-info">View</a>
                                                    <a href="update_sale.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="delete_sale.php?id=<?= $sale['id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete this sale?')">Delete</a>
                                                    <a href="print/print_receipt_sale.php?id=<?= $sale['id'] ?>" target="_blank"
                                                        class="btn btn-sm btn-success">Print</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
