<?php
// Include session management
include 'includes/session.php';

// Open database connection
$conn = $pdo->open();

// Initialize output array
$output = array('error' => false);

// Get product ID and quantity from POST request
$id = $_POST['id'];
$quantity = $_POST['quantity'];

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    // User is logged in, use database-based cart

    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id");
    $stmt->execute(['user_id' => $user['id'], 'product_id' => $id]);
    $row = $stmt->fetch();

    if ($row['numrows'] < 1) {
        // Product is not in the cart, add it
        try {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $stmt->execute(['user_id' => $user['id'], 'product_id' => $id, 'quantity' => $quantity]);
            $output['message'] = 'Product added to cart';
        } catch (PDOException $e) {
            $output['error'] = true;
            $output['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        // Product is already in the cart
        $output['error'] = true;
        $output['message'] = 'Product already in cart';
    }
} else {
    // User is not logged in, use session-based cart

    // Initialize the cart session if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Check if the product is already in the cart
    $exist = array();
    foreach ($_SESSION['cart'] as $row) {
        array_push($exist, $row['productid']);
    }

    if (in_array($id, $exist)) {
        // Product is already in the cart
        $output['error'] = true;
        $output['message'] = 'Product already in cart';
    } else {
        // Product is not in the cart, add it
        $data = array(
            'productid' => $id,
            'quantity' => $quantity
        );

        if (array_push($_SESSION['cart'], $data)) {
            $output['message'] = 'Product added to cart';
        } else {
            $output['error'] = true;
            $output['message'] = 'Cannot add item to cart';
        }
    }
}

// Close the database connection
$pdo->close();

// Output the result as JSON
echo json_encode($output);
?>