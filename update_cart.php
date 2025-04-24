<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    die('Unauthorized access');
}

if (isset($_POST['pid']) && isset($_POST['quantity'])) {
    $pid = (int)$_POST['pid'];
    $quantity = (int)$_POST['quantity'];
    $buyer_id = $_SESSION['id'];
    
    // Update quantity in cart
    $sql = "UPDATE mycart SET quantity = ? WHERE bid = ? AND pid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $buyer_id, $pid);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}
?> 