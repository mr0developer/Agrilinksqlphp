<?php
    session_start();
    require '../db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $name = dataFilter($_POST['name']);
        $mobile = dataFilter($_POST['mobile']);
        $user = dataFilter($_POST['uname']);
        $email = dataFilter($_POST['email']);
        $section = dataFilter($_POST['section']);
        $addr = dataFilter($_POST['addr']);
        
        // Set default values for optional fields
        $post = isset($_POST['post']) ? dataFilter($_POST['post']) : '';
        $year = isset($_POST['year']) ? dataFilter($_POST['year']) : '';
        $edu = isset($_POST['edu']) ? dataFilter($_POST['edu']) : '';

        $_SESSION['Email'] = $email;
        $_SESSION['Name'] = $name;
        $_SESSION['Username'] = $user;
        $_SESSION['Mobile'] = $mobile;
        $_SESSION['Category'] = $section;
        $_SESSION['Addr'] = $addr;
        $_SESSION['Edu'] = $edu;
        $_SESSION['Year'] = $year;
    }
    
    $id = $_SESSION['id'];
    
    // Determine which table to update based on user category
    $table = ($_SESSION['Category'] == 1) ? 'farmer' : 'buyer';
    $idField = ($_SESSION['Category'] == 1) ? 'fid' : 'bid';
    
    // Build the SQL query based on the table
    if ($table == 'farmer') {
        $sql = "UPDATE farmer SET fname='$name', fusername='$user', fmobile='$mobile', 
                femail='$email', faddress='$addr' WHERE $idField='$id';";
    } else {
        $sql = "UPDATE buyer SET bname='$name', busername='$user', bmobile='$mobile', 
                bemail='$email', baddress='$addr' WHERE $idField='$id';";
    }

    $result = mysqli_query($conn, $sql);
    if($result)
    {
        $_SESSION['message'] = "Profile Updated successfully !!!";
        header("Location: ../profileView.php");
    }
    else
    {
        $_SESSION['message'] = "There was an error in updating your profile! Please Try again!";
        header("Location: ../Login/error.php");
    }

    function dataFilter($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
