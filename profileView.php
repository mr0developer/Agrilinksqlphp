<?php
    session_start();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
        $_SESSION['message'] = "You have to Login to view this page!";
        header("Location: Login/error.php");
        exit();
    }
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Profile: <?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?></title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap & CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/profile.css" />
    <link rel="stylesheet" href="login.css"/>
    <link rel="stylesheet" href="css/skel.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/style-xlarge.css" />

    <!-- JS Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
</head>

<body class="subpage" style="background-color:rgb(73, 231, 152);">

    <?php require 'menu.php'; ?>

    <div class="profile-container">
        <div class="profile-header text-center">
            <img src="<?php echo 'images/profileImages/' . (isset($_SESSION['picName']) ? $_SESSION['picName'] : 'profile0.png') . '?' . mt_rand(); ?>" 
                 class="profile-image" alt="Profile Picture">
            <h2 style="color: #2c3e50;"><?php echo isset($_SESSION['Name']) ? $_SESSION['Name'] : ''; ?></h2>
            <h4 style="color: #7f8c8d;"><?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?></h4>
        </div>

        <div class="profile-info">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <div class="info-item">
                        <span class="info-label">RATINGS:</span>
                        <span class="info-value"><?php echo isset($_SESSION['Rating']) ? $_SESSION['Rating'] : '0'; ?></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-item">
                        <span class="info-label">Email ID:</span>
                        <span class="info-value"><?php echo isset($_SESSION['Email']) ? $_SESSION['Email'] : 'Not set'; ?></span>
                    </div>
                </div>
                <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <div class="info-item">
                        <span class="info-label">Mobile No:</span>
                        <span class="info-value"><?php echo $_SESSION['Mobile']; ?></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="info-item">
                        <span class="info-label">ADDRESS:</span>
                        <span class="info-value"><?php echo $_SESSION['Addr']; ?></span>
                    </div>
                </div>
                <div class="col-sm-3"></div>
            </div>
        </div>

        <br />
        <div class="text-center">
            <div class="row justify-content-center">
                <div class="col-md-2 col-sm-4">
                    <a href="changePassPage.php" class="btn btn-primary btn-block">Change Password</a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="profileEdit.php" class="btn btn-primary btn-block">Edit Profile</a>
                </div>
                <?php if ($_SESSION['Category'] == 1): ?>
                <div class="col-md-2 col-sm-4">
                    <a href="uploadProduct.php" class="btn btn-primary btn-block">Upload Product</a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="farmer_messages.php" class="btn btn-primary btn-block">View Messages</a>
                </div>
                <?php endif; ?>
                <div class="col-md-2 col-sm-4">
                    <a href="Login/logout.php" class="btn btn-danger btn-block">LOG OUT</a>
                </div>
            </div>
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
