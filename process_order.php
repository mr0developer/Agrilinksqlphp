<?php
session_start();
require 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Debug information
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . print_r($_POST, true));

if (!isset($_SESSION['logged_in'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to process orders!'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $buyer_id = $_SESSION['Category'] == 0 ? $_SESSION['id'] : (isset($_POST['buyer_id']) ? (int)$_POST['buyer_id'] : 0);
    
    error_log("Processed Data - Product ID: $product_id, Quantity: $quantity, Buyer ID: $buyer_id");
    
    if ($product_id <= 0 || $quantity <= 0 || $buyer_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid order details! Product ID: ' . $product_id . ', Quantity: ' . $quantity . ', Buyer ID: ' . $buyer_id
        ]);
        exit();
    }
    
    // Get product and farmer details
    $sql = "SELECT p.*, f.fid as farmer_id, f.fname as farmer_name, f.fmobile as farmer_mobile, f.femail as farmer_email,
            b.bname as buyer_name, b.bmobile as buyer_mobile, b.bemail as buyer_email
            FROM fproduct p 
            JOIN farmer f ON p.fid = f.fid 
            JOIN buyer b ON b.bid = $buyer_id
            WHERE p.pid = $product_id";
    
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
    
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found!'
        ]);
        exit();
    }
    
    // Check if there's enough quantity
    if ($product['quantity'] < $quantity) {
        echo json_encode([
            'success' => false,
            'message' => 'Not enough quantity available!'
        ]);
        exit();
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Create new order with all required information
        $order_sql = "INSERT INTO orders (
                        buyer_id, 
                        farmer_id, 
                        product_id, 
                        quantity, 
                        price, 
                        status,
                        created_at
                    ) VALUES (
                        $buyer_id, 
                        {$product['farmer_id']}, 
                        $product_id, 
                        $quantity, 
                        {$product['price']}, 
                        'confirmed',
                        NOW()
                    )";
        mysqli_query($conn, $order_sql);
        $order_id = mysqli_insert_id($conn);
        
        // Update product quantity
        $new_quantity = $product['quantity'] - $quantity;
        $update_product = "UPDATE fproduct SET quantity = $new_quantity WHERE pid = $product_id";
        mysqli_query($conn, $update_product);
        

        
        // Send message to farmer
        $farmer_message = "Order #$order_id: We will pick up $quantity units of {$product['product']} from your farm.";
        $sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
                VALUES ($order_id, {$product['farmer_id']}, 'farmer', '$farmer_message')";
        mysqli_query($conn, $sql);
        
        // Send message to buyer
        $buyer_message = "Order #$order_id has been successfully confirmed. Please pick up your order at the specified location.";
        $sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
                VALUES ($order_id, $buyer_id, 'buyer', '$buyer_message')";
        mysqli_query($conn, $sql);
        
        // Commit transaction
        mysqli_commit($conn);
        
        echo json_encode([
            'success' => true,
            'message' => 'Order created and processed successfully!',
            'order_id' => $order_id
        ]);
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo json_encode([
            'success' => false,
            'message' => 'Error processing order: ' . $e->getMessage()
        ]);
        exit();
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
    exit();
}
?> 