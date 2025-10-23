<?php 
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Fetch categories from DB
$catStmt = $conn->query("SELECT id, category FROM add_categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Selected category (via GET)
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch products based on category & search
$sql = "SELECT * FROM add_products WHERE product_status = 'Active'";
$params = [];

if ($selectedCategory > 0) {
    $sql .= " AND product_category = ?";
    $params[] = $selectedCategory;
}

if ($searchTerm !== '') {
    $sql .= " AND product_name LIKE ?";
    $params[] = '%' . $searchTerm . '%';
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cart from session
$cartDetails = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $cartStmt = $conn->prepare("SELECT * FROM add_products WHERE id IN ($ids)");
    $cartStmt->execute();
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartItems as $row) {
        $pid = $row['id'];
        $qty = $_SESSION['cart'][$pid];
        $price = floatval($row['product_price']);
        $subtotal = $price * $qty;
        $total += $subtotal;
        $cartDetails[] = [
            'id' => $pid,
            'name' => $row['product_name'],
            'price' => $price,
            'quantity' => $qty,
            'subtotal' => $subtotal,
            'image' => $row['product_image'] ?? ''
        ];
    }
}

$tax = $total * 0.10;
$grandTotal = $total + $tax;
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
                            <a href="cashier_dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Homepage</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="manage_sales.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Sales Management</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="reports.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Reports</span>
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
                                                Cashier
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
                <h3 class="mb-4">Cashier Dashboard</h3>

      <div class="row gx-4">
        <!-- Products Section -->
        <section class="col-lg-8 mb-4">
          <!-- Search Form -->
          <form method="get" class="mb-3 d-flex" role="search" aria-label="Search products">
            <input
              type="search"
              name="search"
              class="form-control me-2"
              placeholder="Search items here..."
              value="<?= htmlspecialchars($searchTerm) ?>"
              aria-label="Search items"
            />
            <input type="hidden" name="category" value="<?= $selectedCategory ?>" />
            <button type="submit" class="btn btn-primary">Search</button>
          </form>

          <!-- Categories -->
          <div class="mb-3">
            <div class="btn-group" role="group" aria-label="Categories">
              <a
                href="cashier_dashboard.php?category=0<?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>"
                class="btn <?= $selectedCategory == 0 ? 'btn-primary' : 'btn-outline-secondary' ?>"
                >All</a
              >
              <?php foreach ($categories as $cat): ?>
                <a
                  href="cashier_dashboard.php?category=<?= $cat['id'] ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>"
                  class="btn <?= $selectedCategory == $cat['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>"
                  ><?= htmlspecialchars($cat['category']) ?></a
                >
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Products Grid -->
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
            <?php foreach ($products as $product): ?>
              <div class="col">
                <div class="card h-100 shadow-sm">
                  <?php if (!empty($product['product_image'])): ?>
                    <img
                      src="assets/images/products/<?= htmlspecialchars($product['product_image']) ?>"
                      class="card-img-top"
                      alt="<?= htmlspecialchars($product['product_name']) ?>"
                      style="height: 130px; object-fit: contain;"
                    />
                  <?php else: ?>
                    <img
                      src="https://via.placeholder.com/150x130?text=No+Image"
                      class="card-img-top"
                      alt="No image"
                      style="height: 130px; object-fit: contain;"
                    />
                  <?php endif; ?>
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                    <p class="card-text text-primary fw-bold">₱<?= number_format($product['product_price'], 2) ?></p>
                    <form method="post" action="add_to_cart.php" class="mt-auto">
                      <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
                      <button type="submit" class="btn btn-sm btn-primary w-100">+</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- Cart Sidebar -->
        <aside class="col-lg-4">
          <div class="card shadow-sm sticky-top" style="top: 1rem;">
            <div class="card-header text-white">
              <h5 class="mb-0">Bills</h5>
            </div>
            <div class="card-body d-flex flex-column" style="min-height: 400px;">
              <?php if (!empty($cartDetails)): ?>
                <div class="list-group mb-3">
                  <?php foreach ($cartDetails as $item): ?>
                    <div class="list-group-item d-flex align-items-center gap-3">
                      <img
                        src="<?= !empty($item['image']) ? "assets/images/products/" . htmlspecialchars($item['image']) : 'https://via.placeholder.com/50' ?>"
                        alt="<?= htmlspecialchars($item['name']) ?>"
                        class="rounded"
                        style="width: 50px; height: 50px; object-fit: cover;"
                      />
                      <div class="flex-grow-1">
                        <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                        <small class="text-muted">₱<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?></small><br />
                        <small class="text-muted">Subtotal: ₱<?= number_format($item['subtotal'], 2) ?></small>
                      </div>
                      <form method="post" action="update_cart.php" class="d-flex flex-column gap-1 m-0">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>" />
                        <button
                          type="submit"
                          name="increment"
                          class="btn btn-sm btn-success py-0"
                          aria-label="Increase quantity"
                          title="Increase quantity"
                        >
                          +
                        </button>
                        <button
                          type="submit"
                          name="decrement"
                          class="btn btn-sm btn-danger py-0"
                          aria-label="Decrease quantity"
                          title="Decrease quantity"
                        >
                          −
                        </button>
                      </form>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="mt-auto">
                  <form method="post" action="add_sale.php">
  <input type="hidden" name="cart" value="<?= htmlspecialchars(json_encode($_SESSION['cart'])) ?>" />
  <div class="mb-3">
    <label for="payment_method" class="form-label">Payment Method</label>
    <select name="payment_method" id="payment_method" class="form-select" required>
      <option value="" disabled selected>Select payment method</option>
      <option value="GCASH">GCASH</option>
      <option value="COD">COD</option>
    </select>
  </div>

  <div class="d-flex justify-content-between fw-semibold mb-2">
    <span>Sub Total</span>
    <span>₱<?= number_format($total, 2) ?></span>
  </div>
  <div class="d-flex justify-content-between fw-semibold mb-2">
    <span>Tax 10% (VAT Included)</span>
    <span>₱<?= number_format($tax, 2) ?></span>
  </div>
  <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
    <span>Total</span>
    <span>₱<?= number_format($grandTotal, 2) ?></span>
  </div>
  <button type="submit" class="btn btn-primary w-100" <?= empty($cartDetails) ? 'disabled' : '' ?>>
    Place Order
  </button>
</form>
                </div>
              <?php else: ?>
                <p class="text-muted">Your cart is empty.</p>
              <?php endif; ?>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="assets/static/js/pages/dashboard.js"></script>

</body>
</html>
