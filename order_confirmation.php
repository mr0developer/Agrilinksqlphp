<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    header("Location: Login/error.php");
    $_SESSION['message'] = "Please login as a buyer to view order confirmation!";
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    header("Location: error.php");
    $_SESSION['message'] = "Invalid order ID!";
    exit();
}

// Get order details
$sql = "SELECT o.*, p.product, f.fname as farmer_name, b.bname as buyer_name
        FROM orders o 
        JOIN fproduct p ON o.product_id = p.pid 
        JOIN farmer f ON o.farmer_id = f.fid 
        JOIN buyer b ON o.buyer_id = b.bid 
        WHERE o.order_id = $order_id AND o.buyer_id = " . $_SESSION['id'];
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: error.php");
    $_SESSION['message'] = "Order not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Confirmation - Agrilink</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<link rel="stylesheet" href="Blog/commentBox.css" />
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
</head>
<body>
    <?php require 'menu.php'; ?>
    
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3>Order Confirmed Successfully!</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h4>Thank you for your order!</h4>
                            <p>Your order has been successfully placed and confirmed.</p>
                        </div>
                        
                        <div class="order-details">
                            <h4>Order Details</h4>
                            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                            <p><strong>Product:</strong> <?php echo htmlspecialchars($order['product']); ?></p>
                            <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?> units</p>
                            <p><strong>Total Price:</strong> KES <?php echo number_format($order['price'] * $order['quantity'], 2); ?></p>
                            <p><strong>Farmer:</strong> <?php echo htmlspecialchars($order['farmer_name']); ?></p>
                        </div>
                        
                        <div class="pickup-info mt-4">
                            <h4>Pickup Information</h4>
                            <div class="alert alert-info">
                                <p><strong>Please pick up your order at:</strong></p>
                                <p>Local Warehouse<br>
                                Agrilink Distribution Center<br>
                                123 Market Street<br>
                                Business Hours: 9:00 AM - 5:00 PM</p>
                                <p><strong>Please bring:</strong></p>
                                <ul>
                                    <li>Your order confirmation number (#<?php echo $order_id; ?>)</li>
                                    <li>Valid ID</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="chat.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">View Order Chat</a>
                            <a href="productMenu.php" class="btn btn-secondary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html> 