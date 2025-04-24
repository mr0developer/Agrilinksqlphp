<?php
session_start();
require '../db.php';

// Set content type header
header('Content-Type: text/plain; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo 'error:not_logged_in';
    exit();
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$sender_id = isset($_POST['sender_id']) ? (int)$_POST['sender_id'] : 0;
$sender_type = isset($_POST['sender_type']) ? $_POST['sender_type'] : '';
$message = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : '';

// Validate inputs
if ($order_id <= 0) {
    echo 'error:invalid_order';
    exit();
}

if ($sender_id <= 0) {
    echo 'error:invalid_sender';
    exit();
}

if ($sender_id != $_SESSION['id']) {
    echo 'error:unauthorized';
    exit();
}

if (!in_array($sender_type, ['farmer', 'buyer'])) {
    echo 'error:invalid_sender_type';
    exit();
}

if (empty($message)) {
    echo 'error:empty_message';
    exit();
}

// Check if order exists and user has access
$sql = "SELECT * FROM orders WHERE order_id = $order_id AND 
        (buyer_id = $sender_id OR farmer_id = $sender_id)";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo 'error:order_not_found';
    exit();
}

// Insert message
$sql = "INSERT INTO chat_messages (order_id, sender_id, sender_type, message) 
        VALUES ($order_id, $sender_id, '$sender_type', '$message')";

if (mysqli_query($conn, $sql)) {
    echo 'success';
} else {
    echo 'error:database_error';
    error_log("Chat message error: " . mysqli_error($conn));
}
?> 