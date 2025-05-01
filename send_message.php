<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if (isset($_POST['message']) && isset($_GET['farmer_id'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $buyer_id = $_SESSION['id'];
    $farmer_id = (int)$_GET['farmer_id'];
    
    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
        exit();
    }
    
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, created_at) 
            VALUES ($buyer_id, $farmer_id, '$message', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?> 