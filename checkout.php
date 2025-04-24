<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    header("Location: Login/error.php");
    $_SESSION['message'] = "Please login as a buyer to checkout!";
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

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Create order
        $order_sql = "INSERT INTO orders (buyer_id, farmer_id, product_id, quantity, price, status) 
                     VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("iiiid", 
            $buyer_id, 
            $row['fid'], 
            $row['pid'], 
            $row['quantity'], 
            $row['price']
        );
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Send message to farmer
        $message = "New order #$order_id received for {$row['quantity']} units of {$row['product']} from {$_SESSION['Name']}.";
        $message_sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
                       VALUES (?, ?, 'buyer', ?)";
        $stmt = $conn->prepare($message_sql);
        $stmt->bind_param("iis", $order_id, $buyer_id, $message);
        $stmt->execute();
    }
    
    // Clear cart
    $clear_cart = "DELETE FROM mycart WHERE bid = ?";
    $stmt = $conn->prepare($clear_cart);
    $stmt->bind_param("i", $buyer_id);
    $stmt->execute();
    
    $_SESSION['message'] = "Order placed successfully! The farmer will confirm your order shortly.";
} else {
    $_SESSION['message'] = "Your cart is empty!";
}

header("Location: productMenu.php");
exit();
?> 