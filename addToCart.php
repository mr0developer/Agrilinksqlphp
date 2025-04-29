<?php
session_start();
require 'db.php';

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == 0) {
    $_SESSION['message'] = "You need to first login to access this page !!!";
    header("Location: Login/error.php");
    exit();
}

if(isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $bid = $_SESSION['id'];
    
    // Check if item is already in cart
    $check_sql = "SELECT * FROM mycart WHERE bid = '$bid' AND pid = '$pid'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) == 0) {
        // Add item to cart if not already present
        $sql = "INSERT INTO mycart (bid, pid) VALUES ('$bid', '$pid')";
        $result = mysqli_query($conn, $sql);
        
        if($result) {
            $_SESSION['message'] = "Item added to cart successfully!";
        } else {
            $_SESSION['message'] = "Error adding item to cart. Please try again.";
        }
    } else {
        $_SESSION['message'] = "Item is already in your cart!";
    }
    
    // Redirect to cart.php
    header("Location: cart.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?> 