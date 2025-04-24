<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    $_SESSION['message'] = "Please login as a buyer to access the cart!";
    header("Location: Login/error.php");
    exit();
}

if (isset($_GET['pid'])) {
    $pid = (int)$_GET['pid'];
    $buyer_id = $_SESSION['id'];
    
    // Remove item from cart
    $sql = "DELETE FROM mycart WHERE bid = ? AND pid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $pid);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Item removed from cart successfully!";
    } else {
        $_SESSION['message'] = "Error removing item from cart: " . $conn->error;
    }
    
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid request!";
}

header("Location: cart.php");
exit();
?> 