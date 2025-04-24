<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id']) || $_SESSION['Category'] != 0) { // Only buyers can rate
    echo 'error';
    exit();
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$farmer_id = isset($_POST['farmer_id']) ? (int)$_POST['farmer_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review = isset($_POST['review']) ? mysqli_real_escape_string($conn, $_POST['review']) : '';

// Validate order belongs to buyer
$sql = "SELECT * FROM orders WHERE order_id = $order_id AND buyer_id = " . $_SESSION['id'];
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo 'error';
    exit();
}

// Check if already rated
$sql = "SELECT * FROM product_ratings WHERE order_id = $order_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    echo 'error';
    exit();
}

// Insert rating
$sql = "INSERT INTO product_ratings (order_id, buyer_id, product_id, farmer_id, rating, review) 
        VALUES ($order_id, " . $_SESSION['id'] . ", $product_id, $farmer_id, $rating, '$review')";

if (mysqli_query($conn, $sql)) {
    // Update farmer's average rating
    $sql = "UPDATE farmer f 
            SET frating = (
                SELECT AVG(rating) 
                FROM product_ratings 
                WHERE farmer_id = f.fid
            )
            WHERE fid = $farmer_id";
    mysqli_query($conn, $sql);
    
    echo 'success';
} else {
    echo 'error';
}
?> 