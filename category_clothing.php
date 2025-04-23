<?php
include 'includes/session.php'; // Assumed to start session if needed

$slug = $_GET['category'] ?? null; // Use null coalescing for safety if slug might be missing
$cat = null;    // Initialize variable to store category details
$catid = null;  // Initialize variable for category ID
$conn = null;   // Initialize connection variable

// --- Open Connection and Fetch Category Details ---
if ($slug && isset($pdo)) { // Proceed only if slug exists and PDO object is available
    try {
        $conn = $pdo->open(); // Open the connection ONCE
        $stmt = $conn->prepare("SELECT * FROM category1 WHERE cat_slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $cat = $stmt->fetch(); // Fetch category details

        if ($cat) {
            $catid = $cat['id']; // Get the category ID if found
        } else {
            // Handle case where category slug doesn't match any category
            $cat = ['name' => 'Category Not Found']; // Set a default name for display
            $catid = null; // Ensure catid is null
            // You might want to log this error or redirect the user
            error_log("Category not found for slug: " . $slug);
        }

    } catch (PDOException $e) {
        echo "There is some problem fetching category details: " . htmlspecialchars($e->getMessage());
        // Log the actual error for debugging
        error_log("Category Fetch Error (slug: $slug): " . $e->getMessage());
        // Close connection if it was opened before the error
        if ($conn instanceof PDO) {
            $pdo->close();
        }
        // Stop script execution if category fetch fails critically
        // You might want to include footer/scripts gracefully here instead
        exit;
    }
    // Keep the connection ($conn) open to fetch products
} else {
    // Handle cases where slug is missing or $pdo is not set
    echo "Category not specified or database connection not ready.";
    // You might want to redirect or show a more user-friendly error page
    exit;
}

// --- Include Header (likely starts HTML, HEAD tags) ---
include 'includes/header.php';
?>

<?php // --- CSS Styles (Ideally move to a separate CSS file included via header.php) --- ?>
<head> <?php // Remove this opening <head> tag if header.php already includes it ?>
    <style>
        /* --- Product Card Styling --- */
        .product-grid {
            /* Add some padding if needed, or let the container handle it */
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            overflow: hidden; /* Ensures image corners are rounded if image fills card */
            margin-bottom: 2rem; /* Spacing between rows of cards */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            display: flex; /* Use flexbox for vertical layout */
            flex-direction: column; /* Stack items vertically */
            height: 100%; /* Make card fill the column height (requires d-flex on column) */
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .product-image-link {
            display: block; /* Make link a block to contain image */
            position: relative; /* For potential overlays later */
            overflow: hidden; /* Hide parts of image that might overflow on hover zoom */
            height: 230px; /* Fixed height for image container */
            background-color: #f8f9fa; /* Light background for image area */
        }

        .product-image {
            display: block;
            width: 100%;
            height: 100%; /* Fill the container height */
            object-fit: contain; /* Use 'contain' to show whole image, or 'cover' to fill */
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05); /* Slight zoom on hover */
        }

        .product-card-body {
            padding: 1rem 1.2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Allow body to grow and push footer down */
            text-align: center; /* Center align title */
        }

        .product-title {
            font-size: 1.05rem; /* Slightly smaller title */
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
            text-decoration: none; /* Remove default underline */
            flex-grow: 1; /* Allow title to take available space */
             /* Ellipsis for long titles */
             display: -webkit-box;
             -webkit-line-clamp: 2;
             -webkit-box-orient: vertical;
             overflow: hidden;
             text-overflow: ellipsis;
             line-height: 1.4em;
             max-height: 2.8em; /* Approx 2 lines */
        }
         .product-title:hover {
             color: #007bff; /* Change color on hover - adjust as needed */
             text-decoration: none; /* Keep no underline on hover */
         }


        .product-footer {
            padding: 0.8rem 1.2rem;
            border-top: 1px solid #eee;
            background-color: #f9f9f9;
            margin-top: auto; /* Push footer to the bottom */
            text-align: center; /* Center price and button */
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745; /* Green color for price */
            display: block; /* Price on its own line */
            margin-bottom: 0.75rem; /* Space between price and button */
        }

        .product-footer .btn { /* Style view details button */
            /* Make button full width if desired */
            /* width: 100%; */
        }

        /* Add margin-bottom to columns for spacing */
        .product-grid > [class*='col-'] {
             margin-bottom: 1.5rem; /* Consistent bottom margin for columns */
        }

        /* Optional: Adjust container padding */
        .container {
             padding-top: 1rem;
             padding-bottom: 1rem;
        }

    </style>
</head> <?php // Remove this closing </head> tag if header.php doesn't include the opening one above ?>

<body> <?php // Add the opening body tag ?>
    <?php include 'includes/navbar.php'; ?>

    
        <div class="container">

            <section class="mt-4 mb-4">
                <div class="row">
                    <div class="col-12">
                        <?php // Display category name, handle if category wasn't found ?>
                        <h1 class="page-header "><?php echo htmlspecialchars($cat['name'] ?? 'Products'); ?></h1>
                        <hr>
                    </div>
                </div>
                <div class="row product-grid">
                    <?php
                    // Check if the connection is valid and category ID was found
                    if ($conn instanceof PDO && $catid !== null) {
                        try {
                            // Prepare statement to fetch products for this category
                            // Added ORDER BY name ASC for consistent product order
                            $stmt = $conn->prepare("SELECT * FROM products1 WHERE category_id = :catid ORDER BY name ASC");
                            $stmt->execute(['catid' => $catid]);

                            if ($stmt->rowCount() > 0) {
                                // Loop through products using fetch()
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    // Determine image URL, use placeholder if needed
                                    $image_url = 'images/' . htmlspecialchars($row['photo']);
                                    if (empty($row['photo']) || !file_exists($image_url)) {
                                        $image_url = 'images/noimage.jpg'; // Ensure this placeholder exists
                                    }

                                    // --- Output Product Card HTML ---
                                    // Uses Bootstrap columns and the custom CSS classes defined above
                                    echo "
                                    <div class='col-12 col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch'>
                                        <div class='product-card'>
                                            <a href='product.php?product=".htmlspecialchars($row['slug'])."' class='product-image-link'>
                                                <img src='".$image_url."' class='product-image' alt='".htmlspecialchars($row['name'])."'>
                                            </a>
                                            <div class='product-card-body'>
                                                <a href='product.php?product=".htmlspecialchars($row['slug'])."' class='product-title' title='".htmlspecialchars($row['name'])."'>".htmlspecialchars($row['name'])."</a>
                                            </div>
                                            <div class='product-footer'>
                                                <span class='product-price'>&#36; ".number_format($row['price'], 2)."</span>
                                                <a href='product.php?product=".htmlspecialchars($row['slug'])."' class='btn btn-sm btn-outline-primary d-block'>View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                    ";
                                } // End while loop
                            } else {
                                // Message if no products were found in this specific category
                                echo "<div class='col-12'><p>No products found in this category.</p></div>";
                            }
                        } catch (PDOException $e) {
                            // Error during product fetching
                            echo "<div class='col-12'><p class='text-danger'>Error loading products: " . htmlspecialchars($e->getMessage()) . "</p></div>";
                            error_log("Product Fetch Error (Category ID: $catid): " . $e->getMessage());
                        }
                    } elseif ($catid === null) {
                        // Message if the category itself wasn't found (based on $catid being null)
                        echo "<div class='col-12'><p>The category specified could not be found.</p></div>";
                    } else {
                        // Message if $conn isn't a valid PDO object (should have been caught earlier)
                         echo "<div class='col-12'><p class='text-danger'>Database connection error.</p></div>";
                    }

                    // --- Close the single database connection ---
                    if ($conn instanceof PDO) {
                        $pdo->close();
                    }
                    ?>
                </div> <?php // End .row .product-grid ?>
            </section>
        <?php // End .container ?>
    </div> <?php // End .content-wrapper ?>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>
</html>