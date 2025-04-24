<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
    header("Location: Login/error.php");
    exit();
}

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $user_id = $_SESSION['id'];
    $category = $_SESSION['Category'];
    
    // Verify password
    if ($category == 1) {
        $sql = "SELECT fpassword FROM farmer WHERE fid = ?";
        $table = "farmer";
        $id_field = "fid";
    } else {
        $sql = "SELECT bpassword FROM buyer WHERE bid = ?";
        $table = "buyer";
        $id_field = "bid";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user[$category == 1 ? 'fpassword' : 'bpassword'])) {
        // Delete user's profile picture if exists
        if (isset($_SESSION['picName']) && $_SESSION['picName'] != 'profile0.png') {
            $pic_path = "images/profileImages/" . $_SESSION['picName'];
            if (file_exists($pic_path)) {
                unlink($pic_path);
            }
        }
        
        // Delete user's products if farmer
        if ($category == 1) {
            $sql = "DELETE FROM fproduct WHERE fid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }
        
        // Delete user's orders and messages
        $sql = "DELETE FROM orders WHERE " . ($category == 1 ? "fid" : "bid") . " = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete user's chat messages
        $sql = "DELETE FROM chat_messages WHERE " . ($category == 1 ? "farmer_id" : "buyer_id") . " = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Finally, delete the user account
        $sql = "DELETE FROM " . $table . " WHERE " . $id_field . " = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            // Destroy session and redirect to login
            session_destroy();
            header("Location: Login/login.php?message=Account deleted successfully");
            exit();
        } else {
            $_SESSION['message'] = "Error deleting account. Please try again.";
            header("Location: profileEdit.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Incorrect password. Please try again.";
        header("Location: profileEdit.php");
        exit();
    }
} else {
    header("Location: profileEdit.php");
    exit();
}
?> 