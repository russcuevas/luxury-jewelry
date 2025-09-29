<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    if (!empty($id) && !empty($category) && !empty($description)) {
        try {
            $sql = "UPDATE add_categories SET category = :category, description = :description WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                echo "<script>alert('Category updated successfully!'); window.location.href='manage_categories.php';</script>";
            } else {
                echo "<script>alert('Error updating category.');</script>";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
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

                        <li class="sidebar-item">
                            <a href="add_categories.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Add Categories</span>
                            </a>
                        </li>

                        <li class="sidebar-item active">
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
                                <h3>Manage Categories</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav
                                    aria-label="breadcrumb"
                                    class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Manage Categories
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
                                <a href="add_categories.php" class="btn btn-primary btn-sm">
                                    Add Category +
                                </a>
                            </div>

                            <div class="card-body">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'connection.php';
                                        $stmt = $conn->query("SELECT * FROM add_categories ORDER BY id ASC");
                                        $number = 1;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                            <tr>
                                                <td><?= $number++; ?></td>
                                                <td><?= htmlspecialchars($row['category']); ?></td>
                                                <td><?= htmlspecialchars($row['description']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning editBtn"
                                                        data-id="<?= $row['id']; ?>"
                                                        data-category="<?= htmlspecialchars($row['category']); ?>"
                                                        data-description="<?= htmlspecialchars($row['description']); ?>">
                                                        Edit
                                                    </button>
                                                    <a href="delete_category.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="edit_id">
                                            <div class="mb-3">
                                                <label class="form-label">Category Name</label>
                                                <input type="text" name="category" id="edit_category" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" name="description" id="edit_description" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
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
            $(document).on("click", ".editBtn", function() {
                let id = $(this).data("id");
                let category = $(this).data("category");
                let description = $(this).data("description");

                $("#edit_id").val(id);
                $("#edit_category").val(category);
                $("#edit_description").val(description);

                $("#editModal").modal("show");
            });
        </script>

</body>

</html>