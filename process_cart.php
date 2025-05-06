<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    header("Location: Login/error.php");
    $_SESSION['message'] = "Please login as a buyer to process orders!";
    exit();
}

$buyer_id = $_SESSION['id'];

// Get cart items along with product stock
$sql = "SELECT c.quantity AS cart_quantity, c.*, 
               p.pid, p.product, p.price, p.quantity AS available_quantity, 
               f.fid AS farmer_id 
        FROM mycart c 
        JOIN fproduct p ON c.pid = p.pid 
        JOIN farmer f ON p.fid = f.fid 
        WHERE c.bid = $buyer_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = "Your cart is empty!";
    header("Location: cart.php");
    exit();
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    $order_ids = array();

    while ($item = mysqli_fetch_assoc($result)) {
        $ordered_quantity = $item['cart_quantity'];           // Quantity from cart
        $available_quantity = $item['available_quantity'];    // Stock from fproduct

        // Check for stock availability
        if ($ordered_quantity > $available_quantity) {
            throw new Exception("Not enough quantity available for {$item['product']}. Available: $available_quantity, Requested: $ordered_quantity.");
        }

        // Insert into orders table
        $order_sql = "INSERT INTO orders (buyer_id, farmer_id, product_id, quantity, price, status, created_at) 
                      VALUES ($buyer_id, {$item['farmer_id']}, {$item['pid']}, $ordered_quantity, {$item['price']}, 'confirmed', NOW())";
        mysqli_query($conn, $order_sql);
        $order_id = mysqli_insert_id($conn);
        $order_ids[] = $order_id;

        // Update product stock
        $new_quantity = $available_quantity - $ordered_quantity;
        $update_product = "UPDATE fproduct SET quantity = $new_quantity WHERE pid = {$item['pid']}";
        mysqli_query($conn, $update_product);

        // Notify farmer
        $farmer_message = "Order #$order_id: We will pick up $ordered_quantity units of {$item['product']} from your farm.";
        $sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
                VALUES ($order_id, {$item['farmer_id']}, 'farmer', '$farmer_message')";
        mysqli_query($conn, $sql);

        // Notify buyer
        $buyer_message = "Order #$order_id has been successfully confirmed. Please pick up your order at the specified location.";
        $sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
                VALUES ($order_id, $buyer_id, 'buyer', '$buyer_message')";
        mysqli_query($conn, $sql);
    }

    // Clear cart
    $clear_cart = "DELETE FROM mycart WHERE bid = $buyer_id";
    mysqli_query($conn, $clear_cart);

    // Commit transaction
    mysqli_commit($conn);

    // Redirect to confirmation page for the first order
    header("Location: order_confirmation.php?order_id=" . $order_ids[0]);
    exit();

} catch (Exception $e) {
    // Rollback transaction on failure
    mysqli_rollback($conn);
    $_SESSION['message'] = $e->getMessage();
    header("Location: cart.php");
    exit();
}
?>
