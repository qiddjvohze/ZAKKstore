<?php include 'includes/session.php'; ?>
<?php
    /* --- START: ORIGINAL UNCHANGED BACKEND PHP LOGIC --- */
    $conn = $pdo->open();
    $slug = isset($_GET['product']) ? $_GET['product'] : '';
    if (empty($slug)) { /* Basic check */ } // Error handling needed
    try{
        $stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname, products.id AS prodid FROM products LEFT JOIN category ON category.id=products.category_id WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch();
        if(!$product){ throw new Exception("Product not found."); }
        $prodid = $product['prodid'];
    } catch(PDOException $e){ /* Original Error Handling */ echo $e->getMessage(); exit(); }
      catch(Exception $e){ echo $e->getMessage(); exit(); }
    try { // Counter Logic - Original
        $now = date('Y-m-d');
        if (isset($prodid) && isset($product['date_view'])) {
            if($product['date_view'] == $now){ $stmt = $conn->prepare("UPDATE products SET counter=counter+1 WHERE id=:id"); $stmt->execute(['id'=>$prodid]); }
            else{ $stmt = $conn->prepare("UPDATE products SET counter=1, date_view=:now WHERE id=:id"); $stmt->execute(['id'=>$prodid, 'now'=>$now]); }
        }
    } catch (PDOException $e) { error_log("Counter Error: " . $e->getMessage()); }
    /* --- END: ORIGINAL UNCHANGED BACKEND PHP LOGIC --- */
?>
<?php include 'includes/header.php'; ?>
<?php $pageTitle = isset($product['prodname']) ? htmlspecialchars($product['prodname']) : "Product"; ?>
<head>
    <style>
        /* --- "Innovated" Product Page UI --- */

        :root { /* Define theme colors - adjust as needed */
            --primary-color: #007bff; /* Example blue */
            --primary-hover-color: #0056b3;
            --text-dark: #212529;
            --text-medium: #555;
            --text-light: #777;
            --border-color: #e9ecef;
            --background-light: #f8f9fa;
            --background-white: #ffffff;
            --price-color: var(--primary-color);
            --button-text: #ffffff;
        }

        /* General Layout */
        .product-page-section {
            padding: 40px 0; /* More vertical padding */
            background-color: var(--background-light); /* Light page background */
        }
        .product-main-content { /* Target the col-sm-9 */
             background-color: var(--background-white);
             padding: 30px;
             border-radius: 8px;
             box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
             margin-bottom: 30px; /* Space below main content on small screens */
        }
       

        /* Image Area */
        .product-gallery { /* New wrapper for image */
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 25px; /* Space below image */
            cursor: zoom-in;
            border: 1px solid var(--border-color);
        }
        .product-gallery img.product-main-image {
            display: block;
            width: 100%;
            height: auto;
            max-height: 500px; /* Limit max image height */
            object-fit: contain; /* Show whole image */
            background-color: var(--background-white);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-gallery:hover img.product-main-image {
            transform: scale(1.05);
        }

        /* Info Area */
        .product-details { /* New wrapper for info */
            padding-left: 15px; /* Space next to image column */
        }
         @media (max-width: 767px) {
             .product-details { padding-left: 0; margin-top: 20px; }
         }

        .product-header h1.product-name { /* New class */
            font-size: 2.2rem; /* Even larger title */
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 0;
            margin-bottom: 10px;
            line-height: 1.2;
        }
        .product-meta { /* Wrapper for price/category */
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column; /* Stack price/category */
            gap: 5px; /* Space between price/category */
        }
        .product-price-display { /* New class */
            font-size: 2rem;
            font-weight: 700;
            color: var(--price-color);
            line-height: 1;
        }
        .product-category-link { /* New class */
            font-size: 0.9rem;
            color: var(--text-light);
        }
        .product-category-link strong { color: var(--text-medium); font-weight: 600; margin-right: 4px; }
        .product-category-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .product-category-link a:hover {
            text-decoration: underline;
            color: var(--primary-hover-color);
        }

        /* Form & Actions */
        .product-actions { /* New wrapper for form */
            margin: 25px 0;
        }
        .product-actions label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-medium);
        }
        .quantity-control { /* New wrapper */
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .quantity-control button {
            background-color: var(--background-light);
            border: 1px solid var(--border-color);
            color: var(--text-medium);
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .quantity-control button:hover {
            background-color: #e2e6ea;
            color: var(--text-dark);
        }
        .quantity-control button#minus { border-radius: 50% 0 0 50%; } /* Rounded outer edges */
        .quantity-control button#add { border-radius: 0 50% 50% 0; }
        .quantity-control input#quantity {
            width: 60px;
            height: 40px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-left: none;
            border-right: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            padding: 0 5px;
            box-shadow: none;
        }
        .add-to-cart-button { /* New class */
            display: inline-block; /* Or block for full width */
            width: 100%; /* Full width button */
            background-color: var(--primary-color);
            color: var(--button-text);
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 25px; /* Pill shape */
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .add-to-cart-button:hover {
            background-color: var(--primary-hover-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
        }
        .add-to-cart-button:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .add-to-cart-button i { margin-right: 8px; }


        /* Description */
        .product-description-section { /* New wrapper */
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        .product-description-section h4 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 15px;
        }
        .product-description-content { /* New wrapper */
            font-size: 0.95rem;
            color: var(--text-medium);
            line-height: 1.7;
        }
         /* Styles for potential HTML tags inside description (if rendered) */
         .product-description-content p { margin-bottom: 1em; }
         .product-description-content strong,
         .product-description-content b { font-weight: 600; color: var(--text-dark); }
         .product-description-content ul,
         .product-description-content ol { margin-left: 20px; margin-bottom: 1em; }

        /* Facebook Comments */
        .product-comments-section { /* New wrapper */
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }
        .product-comments-section h4 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
            text-align: center;
        }

        /* Callout/Alert */
        #callout {
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            /* Success/Error styles applied via JS needed */
        }
         #callout button.close { /* Style the close button */
             opacity: 0.7;
             font-size: 1.5rem;
             font-weight: bold;
             line-height: 1;
             text-shadow: none;
             color: inherit; /* Inherit color from parent */
         }
         #callout button.close:hover { opacity: 1; }

    </style>
</head>
<body class="hold-transition skin-white layout-top-nav"> <script>/* Facebook SDK */ /* ... */</script>
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>

     
        <div class="container">

            
                <div class="row">
                <div class="row" style="display: flex; justify-content: center; align-items: center;">
    <div class="col-sm-9 product-main-content">
        <div class="callout" id="callout" style="display:none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> 
            <span class="message"></span>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="product-gallery">
                    <img src="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>"
                         class="zoom product-main-image"
                         data-magnify-src="images/large-<?php echo isset($product['photo']) ? $product['photo'] : ''; ?>"
                         alt="<?php echo isset($product['prodname']) ? htmlspecialchars($product['prodname']) : ''; ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <div class="product-header">
                        <h1 class="product-name"><?php echo isset($product['prodname']) ? $product['prodname'] : ''; ?></h1>
                    </div>
                    <div class="product-meta">
                        <div class="product-price-display">&#36; <?php echo isset($product['price']) ? number_format($product['price'], 2) : '0.00'; ?></div>
                        <div class="product-category-link">
                            <strong>Category:</strong> <a href="category.php?category=<?php echo isset($product['cat_slug']) ? $product['cat_slug'] : ''; ?>"><?php echo isset($product['catname']) ? $product['catname'] : ''; ?></a>
                        </div>
                    </div>

                    <div class="product-actions">
                        <form id="productForm"> 
                            <div class="form-group"> 
                                <label for="quantity">Quantity:</label>
                                <div class="quantity-control">
                                    <button type="button" id="minus"><i class="fa fa-minus"></i></button>
                                    <input type="text" name="quantity" id="quantity" value="1" readonly> 
                                    <button type="button" id="add"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <input type="hidden" value="<?php echo isset($product['prodid']) ? $product['prodid'] : ''; ?>" name="id">
                            <button type="submit" class="add-to-cart-button">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </form>
                    </div>

                    <div class="product-description-section">
                        <h4>Description</h4>
                        <div class="product-description-content">
                            <?php echo isset($product['description']) ? $product['description'] : '<p>No description available.</p>'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product-comments-section">
            <h4>Product Comments</h4>
            <div class="fb-comments" data-href="http://localhost/ecommerce/product.php?product=<?php echo $slug; ?>" data-numposts="10" width="100%"></div>
        </div>
    
</div>

                    
                </div> </section> </div> </div> <?php include 'includes/footer.php'; ?>

</div> <?php include 'includes/scripts.php'; ?>
<script>
$(function(){
    // Updated Quantity Spinner Logic for new structure
    $('#add').click(function(e){
        e.preventDefault();
        var quantityInput = $('#quantity');
        var currentVal = parseInt(quantityInput.val());
        if (!isNaN(currentVal)) {
            quantityInput.val(currentVal + 1);
        } else {
            quantityInput.val(1);
        }
    });

    $('#minus').click(function(e){
        e.preventDefault();
        var quantityInput = $('#quantity');
        var currentVal = parseInt(quantityInput.val());
        if (!isNaN(currentVal) && currentVal > 1) {
            quantityInput.val(currentVal - 1);
        } else {
            quantityInput.val(1);
        }
    });

     // Prevent manual input into quantity field if readonly
     $('#quantity').on('keypress', function(e){
        // Allow only numbers - basic check
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
         }
         return true; // Or just keep it readonly in HTML
     });


    // AJAX form submission (adapt URL and response handling)
     $('#productForm').submit(function(e){
         e.preventDefault();
         var productData = $(this).serialize(); // Includes hidden id and quantity
         console.log("Submitting:", productData); // Debugging

         $.ajax({
             type: 'POST',
             url: 'cart_add.php', // <<< YOUR CART ADDING SCRIPT
             data: productData,
             dataType: 'json',
             success: function(response){
                 console.log("Response:", response); // Debugging
                 if(!response){
                      // Handle empty response
                      $('#callout').show().removeClass('alert-success').addClass('alert-danger'); // Use alert classes
                      $('.message').html('Error: Invalid response from server.');
                      return;
                 }
                 // Show message
                 $('#callout').removeClass('alert-danger alert-success').addClass(response.error ? 'alert-danger' : 'alert-success').show();
                 $('.message').html(response.message);

                 if(!response.error){
                      // Update cart count in navbar if applicable
                      // Example: $('.cart_count').text(response.count);
                 }
                 // Hide after timeout
                 setTimeout(function() { $('#callout').fadeOut('slow'); }, 3500);
             },
             error: function(xhr, status, error) {
                 console.error("AJAX Error:", status, error, xhr.responseText);
                 $('#callout').show().removeClass('alert-success').addClass('alert-danger');
                 $('.message').html('Could not add item to cart. Please try again later.');
                  setTimeout(function() { $('#callout').fadeOut('slow'); }, 3500);
             }
         });
     });

     // Close callout (using Bootstrap 3 JS via data-dismiss)
     // $(document).on('click', '#callout .close', function(){ $(this).closest('#callout').hide(); }); // Manual close if needed

     // Magnify Plugin Initialization (make sure library is loaded)
     // $('.zoom').magnify();

});
</script>
<?php $pdo->close(); ?>
</body>
</html>