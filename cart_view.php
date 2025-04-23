<?php 
    include 'includes/session.php'; 
?>
<?php include 'includes/header.php'; // Should contain <head> tag start, meta tags, title etc. ?>
<style>
    /* General Body Styling */
    body.layout-top-nav {
        background-color: #f9fafb; /* Lighter background for a modern feel */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .content-wrapper .container {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    /* Page Header */
    .page-header {
        font-size: 2em;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
        border-bottom: 2px solid #ecf0f1;
        padding-bottom: 15px;
    }

    /* Box Styling */
    .box.box-solid {
        border-radius: 12px; /* More rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        border: none; /* Remove borders */
        background-color: #ffffff; /* White background for contrast */
        margin-bottom: 20px;
    }
    .box.box-solid > .box-body {
        padding: 20px; /* Add padding for spacing */
    }

    /* Table Styling */
    .table-bordered {
        border: none; /* Remove outer border */
        width: 100%;
    }
    .table-bordered thead th {
        background-color: #f8f9fa; /* Light header background */
        border-bottom: 2px solid #dee2e6; /* Stronger bottom border */
        padding: 15px; /* Increased padding */
        font-size: 0.9em;
        text-transform: uppercase;
        color: #495057;
        text-align: left;
    }
    .table-bordered thead th[width="20%"] { /* Center quantity header */
        text-align: center;
    }
    .table-bordered thead th:last-child { /* Align subtotal header right */
        text-align: right;
    }
    .table-bordered tbody tr:hover {
        background-color: #f1f3f5; /* Hover effect for rows */
    }
    .table-bordered tbody td {
        padding: 15px; /* Consistent padding */
        vertical-align: middle; /* Align content vertically */
        border-top: 1px solid #dee2e6; /* Lighter row separator */
        font-size: 0.95em;
    }
    .table-bordered tbody td:last-child { /* Align subtotal cell right */
        text-align: right;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Photo Styling */
    .table-bordered tbody td img {
        max-width: 80px; /* Larger image size */
        height: auto;
        border-radius: 8px; /* Rounded image corners */
        object-fit: cover; /* Ensure images look clean */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }

    /* Delete Button Styling */
    .cart_delete {
        color: #e74c3c; /* Bright red color */
        background: none;
        border: none;
        padding: 5px;
        cursor: pointer;
        font-size: 1.2em;
        transition: color 0.2s ease;
    }
    .cart_delete:hover {
        color: #c0392b; /* Darker red on hover */
    }

    /* Quantity Input & Buttons Styling */
    .quantity-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ced4da;
        border-radius: 6px;
        overflow: hidden;
    }
    .quantity-controls button {
        background-color: #f8f9fa;
        border: none;
        color: #495057;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.2s ease;
    }
    .quantity-controls button:hover {
        background-color: #e9ecef;
    }
    .quantity-controls input {
        width: 50px;
        text-align: center;
        border: none;
        padding: 8px 0;
        font-size: 0.95em;
        outline: none;
    }

    /* PayPal / Login Section */
    .checkout-section {
        margin-top: 20px;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: right;
    }
    .checkout-section h4 {
        margin: 0;
        font-size: 1.1em;
        color: #2c3e50;
    }
    .checkout-section h4 a {
        font-weight: 600;
        color: #3498db; /* Blue link color */
        text-decoration: none;
    }
    .checkout-section h4 a:hover {
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table-bordered thead th,
        .table-bordered tbody td {
            font-size: 0.85em;
            padding: 10px;
        }
        .quantity-controls input {
            width: 40px;
        }
        .table-bordered tbody td img {
            max-width: 60px;
        }
    }
</style>
<body class="hold-transition skin-white layout-top-nav">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Your Cart</h1>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <div class="row">
                    <div class="col-sm-12"> 
                        <div class="box box-solid">
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <th></th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th width="20%">Quantity</th>
                                        <th>Subtotal</th>
                                    </thead>
                                    <tbody id="tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="checkout-section"> 
                            <?php
                                if(isset($_SESSION['user'])){
                                    echo "<div id='paypal-button'></div>";
                                }
                                else{
                                    echo "<h4>You need to <a href='login.php'>Login</a> to checkout.</h4>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php 
        // Close PDO connection if applicable
        if (isset($pdo) && method_exists($pdo, 'close')) {
            $pdo->close(); 
        }
    ?>
    <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; // Should include jQuery ?>
<script>
var total = 0;
$(function(){
    $(document).on('click', '.cart_delete', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'cart_delete.php',
            data: {id:id},
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    getDetails();
                    getCart(); // Make sure this function exists if called
                    getTotal();
                }
            }
        });
    });
    $(document).on('click', '.minus', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var qty = $('#qty_'+id).val();
        if(qty>1){
            qty--;
        }
        $('#qty_'+id).val(qty);
        $.ajax({
            type: 'POST',
            url: 'cart_update.php',
            data: {
                id: id,
                qty: qty,
            },
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    getDetails();
                    getCart(); // Make sure this function exists if called
                    getTotal();
                }
            }
        });
    });
    $(document).on('click', '.add', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var qty = $('#qty_'+id).val();
        qty++;
        $('#qty_'+id).val(qty);
        $.ajax({
            type: 'POST',
            url: 'cart_update.php',
            data: {
                id: id,
                qty: qty,
            },
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    getDetails();
                    getCart(); // Make sure this function exists if called
                    getTotal();
                }
            }
        });
    });
    getDetails();
    getTotal();
});
function getDetails(){
    $.ajax({
        type: 'POST',
        url: 'cart_details.php',
        dataType: 'json',
        success: function(response){
            $('#tbody').html(response);
            getCart(); // Make sure this function exists if called
        }
    });
}
function getTotal(){
    $.ajax({
        type: 'POST',
        url: 'cart_total.php',
        dataType: 'json',
        success:function(response){
            total = response;
        }
    });
}
</script>
<script>
// Ensure PayPal SDK script is loaded (usually in scripts.php) before rendering
if (typeof paypal !== 'undefined') {
    paypal.Button.render({
        env: 'sandbox', // change for production if app is live,
        client: {
            sandbox:    'AQ85zPiQILS2j6agFyqio_h9IHlRrJjszu7ws-cmVwnOxD_Yy8tTs47mA-u-SKOuO_hRwEXsJUNrxeNw',
            //production: 'PRODUCTION_CLIENT_ID'AVVV
        },
        commit: true, // Show a 'Pay Now' button
        style: {
            color: 'gold',
            size: 'small'
        },
        payment: function(data, actions) {
            return actions.payment.create({
                payment: {
                    transactions: [
                        {
                            amount: { 
                                total: total, 
                                currency: 'USD' 
                            }
                        }
                    ]
                }
            });
        },
        onAuthorize: function(data, actions) {
            return actions.payment.execute().then(function(payment) {
                window.location = 'sales.php?pay='+payment.id;
            });
        },
    }, '#paypal-button');
} else {
    console.error("PayPal SDK not loaded.");
}
</script>
</body>
</html>