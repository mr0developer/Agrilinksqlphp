<?php
    session_start();
    require 'db.php';

    if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
        header("Location: Login/error.php");
        $_SESSION['message'] = "Please login as a buyer to access the cart!";
        exit();
    }

    $buyer_id = $_SESSION['id'];
    
    // Get cart items
    $sql = "SELECT c.*, f.*, fp.* 
            FROM mycart c 
            JOIN fproduct fp ON c.pid = fp.pid 
            JOIN farmer f ON fp.fid = f.fid 
            WHERE c.bid = $buyer_id";
    
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart - Agrilink</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-default" style="margin-bottom: 0;">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Agrilink</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="productMenu.php">Continue Shopping</a></li>
                <li><a href="Login/profile.php">My Profile</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>My Shopping Cart</h2>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Farmer</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                while($row = mysqli_fetch_assoc($result)): 
                                    $total += $row['price'];
                                ?>
                                <tr>
                                    <td><?php echo $row['product']; ?></td>
                                    <td><?php echo $row['fname']; ?></td>
                                    <td>KES <?php echo number_format($row['price'], 2); ?></td>
                                    <td>
                                        <input type="number" class="form-control quantity-input" 
                                               data-pid="<?php echo $row['pid']; ?>" 
                                               value="1" min="1" style="width: 70px;">
                                    </td>
                                    <td class="item-total">KES <?php echo number_format($row['price'], 2); ?></td>
                                    <td>
                                        <a href="remove_from_cart.php?pid=<?php echo $row['pid']; ?>" 
                                           class="btn btn-danger btn-sm">Remove</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td colspan="2"><strong>KES <?php echo number_format($total, 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Your cart is empty. <a href="market.php">Continue shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.quantity-input').change(function() {
                var quantity = $(this).val();
                var pid = $(this).data('pid');
                var price = parseFloat($(this).closest('tr').find('td:eq(2)').text().replace('KES ', ''));
                var total = quantity * price;
                
                $(this).closest('tr').find('.item-total').text('KES ' + total.toFixed(2));
                
                // Update cart quantity
                $.post('update_cart.php', {
                    pid: pid,
                    quantity: quantity
                });
                
                updateTotal();
            });
            
            function updateTotal() {
                var total = 0;
                $('.item-total').each(function() {
                    total += parseFloat($(this).text().replace('KES ', ''));
                });
                $('tfoot strong').text('KES ' + total.toFixed(2));
            }
        });
    </script>
</body>
</html> 