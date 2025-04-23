<?php
// Assuming 'includes/session.php' starts the session and potentially includes db connection setup ($pdo)
include 'includes/session.php'; // Must call session_start()
include 'includes/header.php'; // Includes <head> tag start, meta tags, common CSS links
include 'includes/navbar.php'; // Includes the navigation bar

// --- Pagination & Random Seed Logic ---
$limit = 8; // Products per page

// Get current page number, default to 1
$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT, ["options" => ["default" => 1, "min_range" => 1]]) : 1;
if ($page === false) $page = 1; // Ensure $page is an integer

// Generate or retrieve the random seed for this session
if (!isset($_SESSION['product_random_seed'])) {
    $_SESSION['product_random_seed'] = rand(1, 1000000); // Generate a seed
}
$seed = $_SESSION['product_random_seed'];

// Calculate the offset for the query
$offset = ($page - 1) * $limit;

// We need the total count for pagination calculation (run this before fetching the limited set)
$totalProducts = 0;
$totalPages = 0;
if (isset($pdo)) { // Check PDO object availability
    $conn_count = $pdo->open();
    try {
        $stmt_count = $conn_count->prepare("SELECT COUNT(*) AS total FROM products");
        $stmt_count->execute();
        $totalProducts = $stmt_count->fetchColumn();
        $totalPages = ceil($totalProducts / $limit);
    } catch (PDOException $e) {
        error_log("Pagination Count Error: " . $e->getMessage());
        // Handle error - perhaps disable pagination or show an error message
        $totalPages = 0; // Set to 0 or 1 to prevent broken pagination links
    } finally {
         if($conn_count) $pdo->close(); // Close this specific connection instance
    }
}
// --- End Pagination Logic ---


// Determine the current category slug (might be needed by navbar or header) - Copied from previous code
$current_page_basename = basename($_SERVER['PHP_SELF']);
$current_category_slug = ($current_page_basename == 'category.php' && isset($_GET['category'])) ? $_GET['category'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZAKKstore</title>

    <?php // Assuming header.php includes essential CSS like Bootstrap. ?>

    <style>
        /* --- Fonts --- */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

        /* --- Base Styles --- */
        body { font-family: 'Poppins', sans-serif; }

        /* --- Carousel CSS (Keep As Before) --- */
        .carousel-container { width: 100%; overflow: hidden; position: relative; }
        .carousel { width: 100%; height: 70vh; overflow: hidden; position: relative; }
        .carousel .list { display: flex; transition: transform 0.5s ease-in-out; width: 100%; height: 100%; }
        .carousel .list .item { flex: 1 0 100%; height: 100%; width: 100%; position: relative; background-position: center; background-size: cover; background-repeat: no-repeat; display: flex; align-items: center; justify-content: center; }
        .carousel .list .item .content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 450px; color: #fff; text-align: center; z-index: 10; background-color: rgba(0, 0, 0, 0.6); padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .carousel .content .title { font-size: 1.5rem; color: #ffffff; font-weight: 500; line-height: 1.3; margin-bottom: 5px; text-shadow: 1px 1px 3px rgba(0,0,0,0.7); }
        .carousel .content .name { font-size: 2.2rem; text-transform: uppercase; font-weight: bold; line-height: 1.2; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); color: #f0f0f0; }
        .carousel .content .des { font-size: 1rem; margin-bottom: 20px; line-height: 1.6; }
        .carousel .content .apply-btn { padding: 10px 25px; border: none; cursor: pointer; font-size: 1rem; background: #4791bf; color: #fff; border-radius: 5px; transition: background-color 0.3s ease; text-transform: uppercase; font-weight: 600; margin-top: 10px; }
        .carousel .content .apply-btn:hover { background-color: #357ca5; }
        .carousel .arrows { position: absolute; top: 50%; width: 100%; display: flex; justify-content: space-between; transform: translateY(-50%); z-index: 20; padding: 0 15px; pointer-events: none; }
        .carousel .arrows button { pointer-events: auto; width: 45px; height: 45px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.4); color: #fff; border: none; font-size: 24px; font-family: monospace; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease; display: flex; align-items: center; justify-content: center; line-height: 1; }
        .carousel .arrows button:hover { background-color: rgba(0, 0, 0, 0.7); }
        .carousel .arrows button.prev { margin-left: 10px; }
        .carousel .arrows button.next { margin-right: 10px; }
        .carousel-link { display: block; color: inherit; text-decoration: none; height: 100%; width: 100%; position: relative; }

        /* --- Carousel Media Queries (Keep As Before) --- */
        @media screen and (max-width: 992px) { .carousel { height: 65vh; } .carousel .list .item .content { max-width: 400px; padding: 15px; } .carousel .content .name { font-size: 1.8rem; } .carousel .content .des { font-size: 0.9rem; } .carousel .content .apply-btn { padding: 8px 20px; font-size: 0.9rem; } }
        @media screen and (max-width: 768px) { .carousel { height: 60vh; } .carousel .list .item .content { max-width: 350px; } .carousel .content .title { font-size: 1.3rem; } .carousel .content .name { font-size: 1.6rem; } .carousel .content .des { display: none; } .carousel .arrows button { width: 35px; height: 35px; font-size: 18px; } }
        @media screen and (max-width: 576px) { .carousel { height: 55vh; } .carousel .content .name { font-size: 1.4rem; } .carousel .content .title { font-size: 1.1rem; } .carousel .content .apply-btn { padding: 6px 15px; font-size: 0.8rem; } .carousel .arrows { padding: 0 5px; } .carousel .arrows button { width: 30px; height: 30px; font-size: 16px; } }

        /* --- Product Card CSS (Keep As Before) --- */
        .product-card { background-color: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; display: flex; flex-direction: column; height: 100%; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
        .product-image-link { display: block; text-align: center; padding: 15px; background-color: #fdfdfd; }
        .product-image { max-width: 100%; max-height: 200px; height: auto; object-fit: contain; }
        .product-card-body { padding: 1rem; text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .product-title { font-size: 1rem; font-weight: 600; color: #333; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4em; max-height: 2.8em; }
        .product-title:hover { color: #007bff; }
        .product-footer { padding: 0.75rem 1rem; text-align: center; border-top: 1px solid #eee; background-color: #f9f9f9; }
        .product-price { font-size: 1.1rem; font-weight: bold; color: #28a745; display: block; margin-bottom: 0.5rem; }
        .product-footer .btn { /* width: 100%; */ }

        /* --- Pagination CSS --- */
        .pagination { justify-content: center; } /* Center pagination links */
        .pagination .page-item.active .page-link { background-color: #007bff; border-color: #007bff; } /* Active page style */
        .pagination .page-item.disabled .page-link { color: #6c757d; pointer-events: none; background-color: #fff; border-color: #dee2e6; } /* Disabled page style */

    </style>
</head>
<body>

    <?php // Navbar included above ?>

    <section class="carousel-container">
        <div class="carousel">
            <div class="list">
                <?php // Carousel items (hardcoded) ?>
                <div class="item" style="background-image: url('img/1.jpg');"><a href="#" target="_blank" class="carousel-link"><div class="content"><div class="title">ZAKKstore</div><div class="name">Exclusive Collection</div><div class="des">...</div><button class="apply-btn">Shop Now</button></div></a></div>
                <div class="item" style="background-image: url('img/2.jpg');"><a href="#" target="_blank" class="carousel-link"><div class="content"><div class="title">Best Sellers</div><div class="name">Top Rated Products</div><div class="des">...</div><button class="apply-btn">Shop Now</button></div></a></div>
                <div class="item" style="background-image: url('img/3.jpg');"><a href="#" target="_blank" class="carousel-link"><div class="content"><div class="title">New Arrivals</div><div class="name">Fresh Styles</div><div class="des">...</div><button class="apply-btn">Shop Now</button></div></a></div>
                <div class="item" style="background-image: url('img/4.jpg');"><a href="#" target="_blank" class="carousel-link"><div class="content"><div class="title">Limited Offers</div><div class="name">Big Discounts</div><div class="des">...</div><button class="apply-btn">Shop Now</button></div></a></div>
            </div>
            <div class="arrows"><button class="prev">&#10094;</button><button class="next">&#10095;</button></div>
        </div>
    </section>
    <div class="container mt-5 mb-5">
        <h2 class="text-center mb-4">Featured Products</h2>

        <div class="row product-grid">
            <?php
                if (isset($pdo)) {
                    $conn = $pdo->open();
                    try {
                        // --- MODIFIED HOMEPAGE QUERY with RAND() and LIMIT/OFFSET ---
                        // Uses the session seed for consistent random order within the session
                        // Uses LIMIT and OFFSET for pagination
                        // WARNING: ORDER BY RAND() can be slow on large tables.
                        $stmt = $conn->prepare("SELECT * FROM products ORDER BY RAND(:seed) LIMIT :limit OFFSET :offset");

                        // Bind parameters
                        $stmt->bindParam(':seed', $seed, PDO::PARAM_INT);
                        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            foreach ($stmt as $row) {
                                // Image path logic (same as before)
                                $image_url = 'images/' . htmlspecialchars($row['photo']);
                                if (empty($row['photo']) || !file_exists($image_url)) {
                                     $image_url = 'images/noimage.jpg';
                                }

                                // --- HTML Output (same structure as before) ---
                                echo "
                                <div class='col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch'>
                                    <div class='product-card w-100'>
                                        <a href='product.php?product=".htmlspecialchars($row['slug'])."' class='product-image-link'>
                                            <img src='".$image_url."' class='product-image' alt='".htmlspecialchars($row['name'])."'>
                                        </a>
                                        <div class='product-card-body'>
                                            <a href='product.php?product=".htmlspecialchars($row['slug'])."' class='product-title' title='".htmlspecialchars($row['name'])."'>".htmlspecialchars($row['name'])."</a>
                                        </div>
                                        <div class='product-footer'>
                                            <span class='product-price'>&#36; ".number_format($row['price'], 2)."</span>
                                            <a href='product.php?product=".htmlspecialchars($row['slug'])."' class='btn btn-sm btn-outline-primary d-block mt-2'>View Details</a>
                                        </div>
                                    </div>
                                </div>
                                ";
                            }
                        } else {
                             // Message adjusted for pagination context
                             if ($page == 1) {
                                 echo '<div class="col-12"><p class="text-center">No featured products found.</p></div>';
                             } else {
                                 echo '<div class="col-12"><p class="text-center">No more products found.</p></div>';
                             }
                        }

                    } catch (PDOException $e) {
                        echo "<div class='col-12'><p class='text-center text-danger'>Error loading products.</p></div>";
                        error_log("Homepage Product Fetch Error (Paginated Random): " . $e->getMessage());
                    } finally {
                        if ($conn) { $pdo->close(); }
                    }
                } else {
                     echo '<div class="col-12"><p class="text-center text-warning">Database connection object not available.</p></div>';
                }
            ?>
        </div> <?php // End .row .product-grid ?>

        <?php if ($totalPages > 1): // Only show pagination if there's more than one page ?>
        <nav aria-label="Product navigation">
            <ul class="pagination">
                <?php // Previous Button ?>
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php // Page Number Links (simplified version) ?>
                <?php // You might want to add logic here to limit the number of visible page links (e.g., show first, last, current, and a few around current) for very large numbers of pages ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php // Next Button ?>
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        </div> <?php // End .container ?>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>

    <?php // --- Carousel JavaScript (Keep As Before) --- ?>
    <script>
        const carouselList = document.querySelector('.carousel .list');
        const prevButton = document.querySelector('.carousel .arrows .prev');
        const nextButton = document.querySelector('.carousel .arrows .next');
        if (carouselList && prevButton && nextButton) {
            let currentIndex = 0;
            const items = carouselList.querySelectorAll('.item');
            if (items.length > 0) {
                const totalItems = items.length;
                let autoSlideInterval;
                const slideDuration = 5000;
                function updateCarousel() { const offset = -currentIndex * 100; carouselList.style.transform = `translateX(${offset}%)`; }
                function startAutoSlide() { stopAutoSlide(); if (totalItems > 1) { autoSlideInterval = setInterval(() => { currentIndex = (currentIndex + 1) % totalItems; updateCarousel(); }, slideDuration); } }
                function stopAutoSlide() { clearInterval(autoSlideInterval); }
                nextButton.addEventListener('click', () => { currentIndex = (currentIndex + 1) % totalItems; updateCarousel(); startAutoSlide(); });
                prevButton.addEventListener('click', () => { currentIndex = (currentIndex - 1 + totalItems) % totalItems; updateCarousel(); startAutoSlide(); });
                const carouselElement = document.querySelector('.carousel');
                if(carouselElement) { carouselElement.addEventListener('mouseenter', stopAutoSlide); carouselElement.addEventListener('mouseleave', startAutoSlide); }
                updateCarousel(); startAutoSlide();
             } else { console.warn("No items found in the carousel list."); if(prevButton) prevButton.style.display = 'none'; if(nextButton) nextButton.style.display = 'none'; }
        } else { console.warn("Carousel elements not found."); }
    </script>

</body>
</html>