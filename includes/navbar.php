<?php
$current_page = basename($_SERVER['PHP_SELF']);
// Determine the current category slug if we are on the category page
$current_category_slug = ($current_page == 'category.php' && isset($_GET['category'])) ? $_GET['category'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ZAKKstore</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link href="img/favicon.ico" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Your existing styles for .active class */
        .navbar-nav .nav-item.nav-link.active, /* Target <a> elements directly */
        .navbar-nav .nav-link.active { /* Added this for good measure */
            color: #007bff !important; /* Added !important if needed to override bootstrap */
            font-weight: bold;
            /* border-bottom: 2px solid #007bff; */ /* Consider removing border if it looks odd on non-dropdowns */
        }

        .navbar-nav .nav-item.nav-link.active:hover,
        .navbar-nav .nav-link.active:hover {
             color: #0056b3 !important; /* Added !important if needed */
            /* border-bottom-color: #0056b3; */
        }
    </style>

    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="img/ZAKKstore.png" alt="ZAKKstore Logo" style="height: 90px;">
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0 d-flex align-items-center">
                <a href="index.php" class="nav-item nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <a href="about.php" class="nav-item nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a>

                <!-- Electronics Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Electronics</a>
                    <div class="dropdown-menu fade-down m-0">
                        <?php
                        // Assuming $pdo is available (e.g., included via require 'conn.php';)
                        // Make sure the connection file is included before this point if $pdo isn't already global/available.
                        if (isset($pdo)) { // Check if $pdo is actually available
                            $conn = $pdo->open(); // Open the database connection
                            try {
                                // Prepare and execute the query to get all categories
                                $stmt = $conn->prepare("SELECT name, cat_slug FROM category ORDER BY name ASC");
                                $stmt->execute();

                                // Loop through each fetched category
                                foreach ($stmt as $row) {
                                    // Check if the current category matches the page being viewed
                                    $isActive = ($current_category_slug === $row['cat_slug']);
                                    $activeClass = $isActive ? ' active' : ''; // Set active class if it matches

                                    // Output a navigation link for each category inside the dropdown
                                    // Using htmlspecialchars() for security
                                    echo '<a href="category.php?category=' . htmlspecialchars($row['cat_slug']) . '" class="dropdown-item' . $activeClass . '">' . htmlspecialchars($row['name']) . '</a>';
                                }

                            } catch (PDOException $e) {
                                // Handle potential database errors gracefully
                                echo '<span class="dropdown-item text-danger">Error fetching categories</span>'; // Display a simple error indicator
                                // Log the error for debugging: error_log("Database Error: " . $e->getMessage());

                            } finally {
                                // Always ensure the database connection is closed
                                $pdo->close();
                            }
                        } else {
                            echo '<span class="dropdown-item text-warning">DB Connection Error</span>'; // Indicate DB connection object issue
                        }
                        ?>
                    </div>
                </div>







                <!-- Electronics Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Clothing</a>
                    <div class="dropdown-menu fade-down m-0">
                        <?php
                        // Assuming $pdo is available (e.g., included via require 'conn.php';)
                        // Make sure the connection file is included before this point if $pdo isn't already global/available.
                        if (isset($pdo)) { // Check if $pdo is actually available
                            $conn = $pdo->open(); // Open the database connection
                            try {
                                // Prepare and execute the query to get all categories
                                $stmt = $conn->prepare("SELECT name, cat_slug FROM category1 ORDER BY name ASC");
                                $stmt->execute();

                                // Loop through each fetched category
                                foreach ($stmt as $row) {
                                    // Check if the current category matches the page being viewed
                                    $isActive = ($current_category_slug === $row['cat_slug']);
                                    $activeClass = $isActive ? ' active' : ''; // Set active class if it matches

                                    // Output a navigation link for each category inside the dropdown
                                    // Using htmlspecialchars() for security
                                    echo '<a href="category_clothing.php?category=' . htmlspecialchars($row['cat_slug']) . '" class="dropdown-item' . $activeClass . '">' . htmlspecialchars($row['name']) . '</a>';
                                }

                            } catch (PDOException $e) {
                                // Handle potential database errors gracefully
                                echo '<span class="dropdown-item text-danger">Error fetching categories</span>'; // Display a simple error indicator
                                // Log the error for debugging: error_log("Database Error: " . $e->getMessage());

                            } finally {
                                // Always ensure the database connection is closed
                                $pdo->close();
                            }
                        } else {
                            echo '<span class="dropdown-item text-warning">DB Connection Error</span>'; // Indicate DB connection object issue
                        }
                        ?>
                    </div>
                </div>











                <a href="contact.php" class="nav-item nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
                <form method="POST" class="d-flex ms-3" action="search.php">
                    <input type="text" class="form-control" id="navbar-search-input" name="keyword" placeholder="Search..." required style="width: 150px;">
                    <button type="submit" class="btn btn-primary ms-1"><i class="fa fa-search"></i></button>
                </form>

                <a href="cart_view.php" class="nav-item nav-link d-flex align-items-center ms-3 <?php echo ($current_page == 'cart_view.php') ? 'active' : ''; ?>">
                    <i class="fa fa-shopping-cart me-1"></i> Cart
                </a>

                <div class="navbar-nav ms-3">
                    <?php
                    // Start session if not already started (place session_start() at the very top of your script usually)
                    // if (session_status() === PHP_SESSION_NONE) {
                    //     session_start();
                    // }

                    if (isset($_SESSION['user'])) {
                        // Assuming $user is populated correctly when session exists
                        $image = (!empty($user['photo'])) ? 'images/' . $user['photo'] : 'images/profile.jpg'; // Ensure these paths are correct
                        echo '
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                    <img src="' . $image . '" class="user-image rounded-circle me-2" alt="User Image" style="height: 30px; width: 30px; object-fit: cover;"> 
                                    <span class="d-none d-sm-inline">' . htmlspecialchars($user['firstname']) . '</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end fade-down m-0">
                                    <a href="profile.php" class="dropdown-item">Profile</a>
                                    <a href="logout.php" class="dropdown-item">Sign out</a>
                                </div>
                            </div>
                        ';
                    } else {
                        echo "
                            <a href='login.php' class='nav-item nav-link " . (($current_page == 'login.php') ? 'active' : '') . "'>Login</a>
                            <a href='signup.php' class='nav-item nav-link " . (($current_page == 'signup.php') ? 'active' : '') . "'>Sign Up</a>
                        ";
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="js/main.js"></script>
</body>
</html>