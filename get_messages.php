<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 0) {
    exit();
}

$buyer_id = $_SESSION['id'];
$farmer_id = $_GET['farmer_id'] ?? 0;

$sql = "SELECT m.*, 
        CASE 
            WHEN m.sender_id = $buyer_id THEN 'buyer-message'
            ELSE 'farmer-message'
        END as message_class,
        CASE 
            WHEN m.sender_id = $buyer_id THEN b.bname
            ELSE f.fname
        END as sender_name
        FROM messages m
        LEFT JOIN buyer b ON m.sender_id = b.bid
        LEFT JOIN farmer f ON m.sender_id = f.fid
        WHERE (m.sender_id = $buyer_id AND m.receiver_id = $farmer_id)
        OR (m.sender_id = $farmer_id AND m.receiver_id = $buyer_id)
        ORDER BY m.created_at ASC";

$result = mysqli_query($conn, $sql);

while ($message = mysqli_fetch_assoc($result)) {
    echo '<div class="message ' . $message['message_class'] . '">';
    echo '<div class="message-sender">' . htmlspecialchars($message['sender_name']) . '</div>';
    echo '<div class="message-content">' . htmlspecialchars($message['message']) . '</div>';
    echo '<div class="message-time">' . date('h:i A', strtotime($message['created_at'])) . '</div>';
    echo '</div>';
}
?> 