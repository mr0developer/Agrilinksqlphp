<?php
    session_start();
?>

<!DOCTYPE HTML>

<html lang="en">
    <head>
        <title>Profile: <?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?></title>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="assets/css/main.css" />
        <link rel="stylesheet" href="assets/css/profile.css" />
        <link rel="stylesheet" href="login.css"/>
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<link rel="stylesheet" href="css/skel.css" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/style-xlarge.css" />
    </head>

    <body class="subpage">

        <?php
            require 'menu.php';
        ?>

        <div class="profile-container">
            <div class="profile-header">
                <div class="text-center">
                    <img src="<?php echo 'images/profileImages/'.(isset($_SESSION['picName']) ? $_SESSION['picName'] : 'profile0.png').'?'.mt_rand(); ?>" 
                         class="profile-image" alt="Profile Picture">
                    <h2><?php echo isset($_SESSION['Name']) ? $_SESSION['Name'] : ''; ?></h2>
                    <h4><?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?></h4>
                </div>
                <div class="file-upload text-center">
                    <form method="post" action="Profile/updatePic.php" enctype="multipart/form-data">
                        <label for="profilePic" class="file-upload-label">Choose New Profile Picture</label>
                        <input type="file" name="profilePic" id="profilePic" accept="image/*">
                        <div class="button-group">
                            <button type="submit" class="btn-primary" name="upload">Upload</button>
                            <button type="submit" class="btn-secondary" name="remove">Remove</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="profile-form">
                <form method="post" action="Profile/updateProfile.php">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" name="name" id="name" 
                               value="<?php echo isset($_SESSION['Name']) ? $_SESSION['Name'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="mobile">Mobile Number</label>
                        <input type="text" class="form-control" name="mobile" id="mobile" 
                               value="<?php echo isset($_SESSION['Mobile']) ? $_SESSION['Mobile'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="uname">Username</label>
                        <input type="text" class="form-control" name="uname" id="uname" 
                               value="<?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" 
                               value="<?php echo isset($_SESSION['Email']) ? $_SESSION['Email'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="section">Category</label>
                        <div class="select-wrapper">
                            <select name="section" id="section" class="form-control">
                                <option value="">Select Category</option>
                                <option value="Farmer" <?php echo (isset($_SESSION['Category']) && $_SESSION['Category'] == 1) ? 'selected' : ''; ?>>Farmer</option>
                                <option value="Buyer" <?php echo (isset($_SESSION['Category']) && $_SESSION['Category'] == 0) ? 'selected' : ''; ?>>Buyer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="addr">Address</label>
                        <input type="text" class="form-control" name="addr" id="addr" 
                               value="<?php echo isset($_SESSION['Addr']) ? $_SESSION['Addr'] : ''; ?>" required>
                    </div>


                    <div class="text-center">
                        <button type="submit" class="btn-primary">Update Profile</button>
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
