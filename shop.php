<?php
session_start();
include 'connection.php';


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';

$productsPerPage = 6;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($currentPage - 1) * $productsPerPage;

$whereClauses = ["p.product_status = 'active'"];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(p.product_name LIKE :search OR p.product_description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($categoryFilter !== '') {
    $whereClauses[] = "p.product_category = :category";
    $params[':category'] = $categoryFilter;
}

$whereSQL = implode(' AND ', $whereClauses);

$countQuery = "SELECT COUNT(*) FROM add_products p WHERE $whereSQL";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $productsPerPage);

$productQuery = "SELECT p.*, c.category 
    FROM add_products p 
    LEFT JOIN add_categories c ON p.product_category = c.id
    WHERE $whereSQL
    ORDER BY p.id DESC
    LIMIT :limit OFFSET :offset";

$productStmt = $conn->prepare($productQuery);
foreach ($params as $key => $value) {
    $productStmt->bindValue($key, $value);
}
$productStmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
$productStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$productStmt->execute();
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryStmt = $conn->query("SELECT id, category FROM add_categories ORDER BY category ASC");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$cartItemCount = 0;

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    try {
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT product_id) AS total_items FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['total_items']) {
            $cartItemCount = $row['total_items'];
        }
    } catch (PDOException $e) {
        echo "Error fetching cart count: " . $e->getMessage();
    }
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
                        <?php if (isset($_SESSION['id'])): ?>
                            <a href="user_logout.php" class="nav-item nav-link">Logout</a>
                        <?php else: ?>
                            <a href="user_login.php" class="nav-item nav-link">Login</a>
                        <?php endif; ?>
                        </div>
                    <div class="d-flex m-3 me-0">
                        <?php if (isset($_SESSION['id'])): ?>
                            <a href="cart.php" class="position-relative me-4 my-auto">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                                <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                                    style="top: -5px; left: 15px; height: 20px; min-width: 20px;">
                                    <?= $cartItemCount ?>
                                </span>
                            </a>
                        <?php endif; ?>

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


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <form method="GET" class="row g-4 align-items-end mb-5">
                            <div class="col-xl-3">
                                <div class="input-group">
                                    <input type="search" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control p-3" placeholder="Search products...">
                                    <button type="submit" class="input-group-text p-3 bg-primary text-white"><i class="fa fa-search"></i></button>
                                </div>
                            </div>

                            <div class="col-xl-3 offset-xl-6">
                                <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between">
                                    <label for="category">Category</label>
                                    <select id="category" name="category" class="form-select-sm bg-light border-0 me-3">
                                        <option value="">All</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= ($categoryFilter == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['category']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="row g-4 justify-content-center">
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <div class="col-md-6 col-lg-6 col-xl-4">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                            <img style="height: 50vh" src="assets/images/products/<?php echo htmlspecialchars($product['product_image']); ?>" class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">
                                            <?php echo htmlspecialchars($product['category']); ?>
                                            </div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                            <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                            <p><?php echo htmlspecialchars($product['product_description']); ?></p>
                                            <div class="d-flex justify-content-between flex-lg-wrap">
                                                <p class="text-dark fs-5 fw-bold mb-0">₱<?php echo number_format($product['product_price'], 2); ?></p>
                                                <?php if (isset($_SESSION['id'])): ?>
                                                    <a href="add_to_cart.php?product_id=<?= $product['id'] ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                    </a>
                                                <?php else: ?>
                                                    <a href="user_login.php" class="btn border border-secondary rounded-pill px-3 text-muted" title="Login to add to cart">
                                                        <i class="fa fa-lock me-2"></i> Login first to add cart
                                                    </a>
                                                <?php endif; ?>

                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="col-12 text-center">
                                        <p>No products found.</p>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Pagination -->
                                    <div class="col-12">
                                    <div class="pagination d-flex justify-content-center mt-5">
                                        <?php if ($currentPage > 1): ?>
                                        <a href="?page=<?php echo $currentPage - 1; ?>" class="rounded">&laquo;</a>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>" class="rounded <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                        <?php endfor; ?>

                                        <?php if ($currentPage < $totalPages): ?>
                                        <a href="?page=<?php echo $currentPage + 1; ?>" class="rounded">&raquo;</a>
                                        <?php endif; ?>
                                    </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->






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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart',
                text: 'The product has been successfully added to your cart.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });

            // Remove the success=added from the URL
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('success');
                window.history.replaceState({}, document.title, url.toString());
            }
        });
    </script>
<?php endif; ?>

</body>

</html>