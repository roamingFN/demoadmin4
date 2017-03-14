<?php
    session_start();
    if (!isset($_POST['uid']) && !isset($_POST['pwd'])) {
        header("Location: login.php");
    }
    else if (!isset($_SESSION['LOGGEDIN'])){
        
    }
    else if ($_SESSION['LOGGEDIN'] == 1){
        header("Location: index.php");
    }
    
    //connect DB
    include 'connectDB.php';
    $objConn = new connectDB;
    $link = $objConn->connect();

    //query
    $query = "SELECT * from user WHERE uid='" . mysqli_real_escape_string($link,$_POST['uid']) .
            "' AND password=MD5('" . mysqli_real_escape_string($link,$_POST['pwd']) . "');";
    $objQuery = mysqli_query($link, $query);
    $objResult = mysqli_fetch_array($objQuery);

    //check login
    if (!$objResult) {
        echo "<script type='text/javascript'>";
        echo "alert('Incorrect ID or Password');";
        echo "location='login.php';";
        echo "</script>";
    } 
    else {
        $_SESSION['ID'] = $objResult['uid'];
        $_SESSION['USERID'] = $objResult['userid'];
        $_SESSION['LOGGEDIN'] = 1;
        session_write_close();
        //close connection
        $objConn->close($link);
        header("Location: index.php");
    }
    
    $objConn->close($link);
?>

