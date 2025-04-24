<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    $_SESSION['message'] = "Please login as a buyer to add items to cart!";
    header("Location: Login/error.php");
    exit();
}

if (isset($_POST['pid'])) {
    $pid = (int)$_POST['pid'];
    $buyer_id = $_SESSION['id'];
    
    // Check if product exists
    $check_product = "SELECT * FROM fproduct WHERE pid = ?";
    $stmt = $conn->prepare($check_product);
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['message'] = "Product not found!";
        header("Location: productMenu.php");
        exit();
    }
    
    // Check if item is already in cart
    $check_cart = "SELECT * FROM mycart WHERE bid = ? AND pid = ?";
    $stmt = $conn->prepare($check_cart);
    $stmt->bind_param("ii", $buyer_id, $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity if already in cart
        $sql = "UPDATE mycart SET quantity = quantity + 1 WHERE bid = ? AND pid = ?";
        $message = "Item quantity updated in cart!";
    } else {
        // Add new item to cart
        $sql = "INSERT INTO mycart (bid, pid, quantity) VALUES (?, ?, 1)";
        $message = "Item added to cart successfully!";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $pid);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = $message;
        header("Location: cart.php");
    } else {
        $_SESSION['message'] = "Error updating cart: " . $conn->error;
        header("Location: productMenu.php");
    }
    
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid request!";
    header("Location: productMenu.php");
}
exit();
?> 