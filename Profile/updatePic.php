<?php
    session_start();
    require '../db.php';
    
    // Check and add picName column to both tables if it doesn't exist
    $tables = ['farmer', 'buyer'];
    foreach ($tables as $table) {
        $check_column = "SHOW COLUMNS FROM $table LIKE 'picName'";
        $result = mysqli_query($conn, $check_column);
        if (mysqli_num_rows($result) == 0) {
            $add_column = "ALTER TABLE $table ADD COLUMN picName VARCHAR(255) DEFAULT 'profile0.png'";
            mysqli_query($conn, $add_column);
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if(isset($_POST['upload']))
        {
            $pic = $_FILES['profilePic'];
            $picName = $pic['name'];
            $picTmpName = $pic['tmp_name'];
            $picSize = $pic['size'];
            $picError = $pic['error'];
            $picType = $pic['type'];

            $picExt = explode('.', $picName);
            $picActualExt = strtolower(end($picExt));
            $allowed = array('jpg','jpeg','png');

            if(in_array($picActualExt, $allowed))
            {
                if($picError === 0)
                {
                    $_SESSION['picId'] = $_SESSION['id'];
                    $picNameNew = "profile".$_SESSION['picId'].".".$picActualExt;
                    $_SESSION['picName'] = $picNameNew;
                    $picDestination = "../images/profileImages/".$picNameNew;
                    move_uploaded_file($picTmpName, $picDestination);
                    $id = $_SESSION['id'];

                    // Determine which table to update based on user category
                    $table = ($_SESSION['Category'] == 1) ? 'farmer' : 'buyer';
                    $idField = ($_SESSION['Category'] == 1) ? 'fid' : 'bid';
                    
                    // Update only the profile picture name in the database
                    $sql = "UPDATE $table SET picName = ? WHERE $idField = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $picNameNew, $id);

                    if($stmt->execute())
                    {
                        $_SESSION['message'] = "Profile picture Updated successfully !!!";
                        header("Location: ../profileView.php");
                    }
                    else
                    {
                        $_SESSION['message'] = "There was an error in updating your profile picture! Please Try again!";
                        header("Location: ../Login/error.php");
                    }
                }
                else
                {
                    $_SESSION['message'] = "There was an error in uploading your image! Please Try again!";
                    header("Location: ../Login/error.php");
                }
            }
            else
            {
                $_SESSION['message'] = "You cannot upload files of this type! Please upload only jpg, jpeg or png files!";
                header("Location: ../Login/error.php");
            }
        }
        else if(isset($_POST['remove']))
        {
            $id = $_SESSION['id'];
            // Determine which table to update based on user category
            $table = ($_SESSION['Category'] == 1) ? 'farmer' : 'buyer';
            $idField = ($_SESSION['Category'] == 1) ? 'fid' : 'bid';
            
            // Set default profile picture
            $defaultPic = "profile0.png";
            $_SESSION['picName'] = $defaultPic;
            
            $sql = "UPDATE $table SET picName = ? WHERE $idField = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $defaultPic, $id);
            
            if($stmt->execute())
            {
                $_SESSION['message'] = "Profile picture removed successfully !!!";
                header("Location: ../profileView.php");
            }
            else
            {
                $_SESSION['message'] = "There was an error in removing your profile picture! Please Try again!";
                header("Location: ../Login/error.php");
            }
        }
    }

    function dataFilter($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
