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

                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-3">
                            <b><font size="+1" color="black">Mobile No : </font></b>
                            <input type="text" name="mobile" value="<?php echo $_SESSION['Mobile'];?>" class="form-control" required>
                        </div>
                        <div class="col-sm-3">
                            <b><font size="+1" color="black">Category : </font></b>
                            <input type="text" value="<?php echo $_SESSION['Category'] == 1 ? 'Farmer' : 'Buyer'; ?>" class="form-control" disabled>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <b><font size="+1" color="black">ADDRESS : </font></b>
                            <textarea name="addr" class="form-control" required><?php echo $_SESSION['Addr'];?></textarea>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <b><font size="+1" color="black">Profile Picture : </font></b>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary" name="update">Update Profile</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">Delete Account</button>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Account Modal -->
        <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                        <form action="delete_account.php" method="POST">
                            <div class="form-group">
                                <label for="password">Enter your password to confirm:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-danger">Delete Account</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
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
