<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
    header("Location: Login/error.php");
    exit();
}

require 'db.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } else {
        $user_id = $_SESSION['id'];
        $category = $_SESSION['Category'];
        
        // Get current password hash
        if ($category == 1) {
            $sql = "SELECT fpassword FROM farmer WHERE fid = ?";
        } else {
            $sql = "SELECT bpassword FROM buyer WHERE bid = ?";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Verify current password
        if (password_verify($current_password, $user[$category == 1 ? 'fpassword' : 'bpassword'])) {
            // Update password
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            
            if ($category == 1) {
                $update_sql = "UPDATE farmer SET fpassword = ? WHERE fid = ?";
            } else {
                $update_sql = "UPDATE buyer SET bpassword = ? WHERE bid = ?";
            }
            
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_password_hash, $user_id);
            
            if ($update_stmt->execute()) {
                $message = "Password changed successfully";
            } else {
                $error = "Error updating password. Please try again.";
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>Change Password</title>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="assets/css/main.css" />
        <link rel="stylesheet" href="assets/css/profile.css" />
        <script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
    </head>

    <body class="subpage">
        <?php require 'menu.php'; ?>

        <div class="profile-container">
            <div class="profile-header">
                <h2>Change Password</h2>
            </div>

            <div class="profile-form">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" name="current_password" id="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required>
                        <small class="form-text text-muted">Password must be at least 8 characters long</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <a href="profileView.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Scripts -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery.scrolly.min.js"></script>
        <script src="assets/js/jquery.scrollex.min.js"></script>
        <script src="assets/js/skel.min.js"></script>
        <script src="assets/js/util.js"></script>
        <script src="assets/js/main.js"></script>
    </body>
</html> 