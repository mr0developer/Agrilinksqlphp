<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: Login/error.php");
    $_SESSION['message'] = "Please login to create orders!";
    exit();
}

// Get product details if product_id is provided
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product = null;

if ($product_id > 0) {
    $sql = "SELECT p.*, f.fname as farmer_name 
            FROM fproduct p 
            JOIN farmer f ON p.fid = f.fid 
            WHERE p.pid = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Order - Agrilink</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require 'menu.php'; ?>
    
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>Create New Order</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($product): ?>
                            <form action="process_order.php" method="POST" id="orderForm">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                
                                <div class="form-group">
                                    <label>Product</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($product['product']); ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>Farmer</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($product['farmer_name']); ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>Price per Unit</label>
                                    <input type="text" class="form-control" value="KES <?php echo number_format($product['price'], 2); ?>" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>Available Quantity</label>
                                    <input type="text" class="form-control" value="<?php echo $product['quantity']; ?> units" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="quantity">Order Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           min="1" max="<?php echo $product['quantity']; ?>" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Confirm Order</button>
                                <a href="productMenu.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                No product selected. Please select a product from the <a href="productMenu.php">product menu</a>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#orderForm').on('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            var formData = {
                product_id: $('input[name="product_id"]').val(),
                quantity: $('#quantity').val()
            };
            
            // Send AJAX request
            $.ajax({
                type: 'POST',
                url: 'process_order.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'chat.php?order_id=' + response.order_id;
                    } else {
                        alert(response.message || 'Error processing order');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    alert('Error connecting to server. Please try again.');
                }
            });
        });
    });
    </script>
</body>
</html> 