<?php
session_start();
require '../db.php';

// Set headers
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$last_message_id = isset($_POST['last_message_id']) ? (int)$_POST['last_message_id'] : 0;

if ($order_id <= 0) {
    echo json_encode(['error' => 'invalid_order']);
    exit();
}

// Get messages
$sql = "SELECT * FROM chat_messages 
        WHERE order_id = $order_id 
        AND message_id > $last_message_id 
        ORDER BY created_at ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['error' => 'database_error']);
    exit();
}

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convert any numeric strings to integers
    $row['message_id'] = (int)$row['message_id'];
    $row['order_id'] = (int)$row['order_id'];
    $row['sender_id'] = (int)$row['sender_id'];
    $messages[] = $row;
}

// Send response
$response = ['messages' => $messages];
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();
?> 