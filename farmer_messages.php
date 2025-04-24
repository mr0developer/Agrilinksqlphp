<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id']) || $_SESSION['Category'] != 1) {
    header("Location: Login/error.php");
    exit();
}

// Get all orders with messages for this farmer
$farmer_id = $_SESSION['id'];
$sql = "SELECT DISTINCT o.order_id, o.status, p.product, b.bname as buyer_name, 
        (SELECT COUNT(*) FROM chat_messages cm 
         WHERE cm.order_id = o.order_id 
         AND cm.sender_type = 'buyer' 
         AND cm.is_read = 0) as unread_count
        FROM orders o 
        JOIN fproduct p ON o.product_id = p.pid 
        JOIN buyer b ON o.buyer_id = b.bid 
        WHERE o.farmer_id = $farmer_id 
        AND EXISTS (SELECT 1 FROM chat_messages cm WHERE cm.order_id = o.order_id)
        ORDER BY o.order_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Messages - <?php echo $_SESSION['Name']; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/chat.css">
</head>
<body>
    <div class="container">
        <div class="messages-container">
            <div class="messages-header">
                <h2>My Messages</h2>
                <a href="profileView.php" class="btn btn-secondary">Back to Profile</a>
            </div>
            
            <div class="messages-list">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($order = mysqli_fetch_assoc($result)): ?>
                        <div class="message-item">
                            <div class="message-info">
                                <h4>Order #<?php echo $order['order_id']; ?></h4>
                                <p>Product: <?php echo $order['product']; ?></p>
                                <p>Buyer: <?php echo $order['buyer_name']; ?></p>
                                <p>Status: <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                            </div>
                            <div class="message-actions">
                                <?php if ($order['unread_count'] > 0): ?>
                                    <span class="badge"><?php echo $order['unread_count']; ?> new</span>
                                <?php endif; ?>
                                <a href="chat.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary">View Chat</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-messages">
                        <p>You have no messages yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html> 