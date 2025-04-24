<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: Login/login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get order details
$sql = "SELECT o.*, f.fname as farmer_name, b.bname as buyer_name, p.product, p.price 
        FROM orders o 
        JOIN farmer f ON o.farmer_id = f.fid 
        JOIN buyer b ON o.buyer_id = b.bid 
        JOIN fproduct p ON o.product_id = p.pid 
        WHERE o.order_id = $order_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

// Check if user has access to this chat
$user_id = $_SESSION['id'];
$user_type = $_SESSION['Category'] == 1 ? 'farmer' : 'buyer';
$opposite_type = $user_type == 'farmer' ? 'buyer' : 'farmer';

if (($user_type == 'farmer' && $order['farmer_id'] != $user_id) || 
    ($user_type == 'buyer' && $order['buyer_id'] != $user_id)) {
    header("Location: error.php");
    exit();
}

// Mark all unread messages from the opposite user type as read
$sql = "UPDATE chat_messages 
        SET is_read = 1 
        WHERE order_id = $order_id 
        AND sender_type = '$opposite_type' 
        AND is_read = 0";
mysqli_query($conn, $sql);

// Handle order completion
if (isset($_POST['complete_order']) && $order['status'] == 'pending') {
    $sql = "UPDATE orders SET status = 'completed' WHERE order_id = $order_id";
    if (mysqli_query($conn, $sql)) {
        $order['status'] = 'completed';
        // Add a system message about order completion
        $system_message = "Order has been marked as completed.";
        $sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
                VALUES ($order_id, $user_id, '$user_type', '$system_message')";
        mysqli_query($conn, $sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat - Order #<?php echo $order_id; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/chat.css">
</head>
<body></body>
    <div class="container">
        <div class="chat-container">
            <div class="chat-header">
                <div class="header-content">
                    <h3>Order #<?php echo $order_id; ?></h3>
                    <p>Product: <?php echo $order['product']; ?></p>
                    <p>Status: <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                    <?php if ($order['status'] == 'pending'): ?>
                        <form method="post" class="complete-order-form">
                            <button type="submit" name="complete_order" class="btn btn-success">Mark as Complete</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="header-buttons">
                    <?php if ($user_type == 'buyer'): ?>
                        <a href="market.php" class="btn btn-primary">Continue Shopping</a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here via AJAX -->
            </div>
            
            <div class="chat-input">
                <form id="message-form">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <input type="hidden" name="sender_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="sender_type" value="<?php echo $user_type; ?>">
                    <textarea name="message" placeholder="Type your message..." required></textarea>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/chat.js"></script>
</body>
</html> 