<?php
session_start();
include 'connection.php'; // Your DB connection

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Fetch user info
$stmtUser = $conn->prepare("SELECT fullname, address, phone_number, email FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Fetch cart items
$stmt = $conn->prepare("SELECT c.id AS cart_id, c.quantity, p.product_name, p.product_price, p.product_image
                        FROM cart c
                        JOIN add_products p ON c.product_id = p.id
                        WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['product_price'] * $item['quantity'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LUXURY JEWELRY - Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#"
                            class="text-white">Sample Address</a></small>
                    <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#"
                            class="text-white">sample@gmail.com</a></small>
                </div>
                <div class="top-link pe-2">
                    <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                    <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                    <a href="#" class="text-white"><small class="text-white ms-2">Sales and Refunds</small></a>
                </div>
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="index.php" class="navbar-brand">
                    <h1 class="text-primary display-6">LUXURY JEWELRY</h1>
                </a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="shop.php" class="nav-item nav-link active">Shop</a>
                        <a href="user_login.php" class="nav-item nav-link">Login</a>
                    </div>
                    <div class="d-flex m-3 me-0">
                        <a href="cart.php" class="position-relative me-4 my-auto">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                            <span
                                class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                                style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                        </a>
                        <!-- <a href="#" class="my-auto">
                                <i class="fas fa-user fa-2x"></i>
                            </a> -->
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->


    <!-- Modal Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="keywords"
                            aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Search End -->


    <!-- Single Page Header start -->
    <div class="container py-5">

    </div>
    <!-- Single Page Header End -->


    <!-- Checkout Page Start -->
    <div class="container-fluid py-5">
            <div class="container py-5">
        <h1 class="mb-4">Checkout</h1>

        <?php if (count($cartItems) === 0): ?>
            <div class="alert alert-warning">Your cart is empty. <a href="shop.php">Shop Now</a></div>
        <?php else: ?>

        <form action="place_order.php" method="POST">
            <div class="row g-5">

                <!-- User Info -->
                <div class="col-md-6">
                    <h3>Your Information</h3>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Fullname <sup>*</sup></label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required
                                value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address <sup>*</sup></label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="House Number Street Name" required
                                value="<?= htmlspecialchars($user['address'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile <sup>*</sup></label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" required
                                value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <sup>*</sup></label>
                            <input type="email" class="form-control" id="email" name="email" required
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                        </div>
                </div>

                <!-- Order Summary -->
                <div class="col-md-6">
                    <h3>Order Summary</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><img style="height: 100px; width: 100px;" src="assets/images/products/<?= htmlspecialchars($item['product_image']) ?>"
                                        alt="<?= htmlspecialchars($item['product_name']) ?>" class="rounded-circle img-product" /></td>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td>₱<?= number_format($item['product_price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>₱<?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
                            </tr>
                            <!-- Pass cart item ids and quantities as hidden inputs -->
                            <input type="hidden" name="cart_id[]" value="<?= $item['cart_id'] ?>">
                            <input type="hidden" name="quantity[]" value="<?= $item['quantity'] ?>">
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="fw-bold">₱<?= number_format($total, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Shipping -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Shipping</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping" id="shippingFree" value="Free Shipping" checked>
                            <label class="form-check-label" for="shippingFree">Free Shipping</label>
                        </div>
                        <!-- Add more shipping options if needed -->
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method <sup>*</sup></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="paymentCOD" value="Cash On Delivery" required>
                            <label class="form-check-label" for="paymentCOD">Cash On Delivery</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="paymentGCash" value="GCash" required>
                            <label class="form-check-label" for="paymentGCash">GCash</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 text-uppercase py-3">Place Order</button>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
    </div>
    <!-- Checkout Page End -->







    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>