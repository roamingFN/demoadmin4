<?php
    session_start();
    if(!isset($_SESSION['ID'])){
        
    }
    else if ($_SESSION['LOGGEDIN'] == 1){
        header("Location: index.php");
    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="./css/login.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>	
        <title>Login</title>      
    </head>
    
    <body>
            <div class="login-box">   
                <div class="inlogin-box">
                        <div class="header">
                            <h1><i class="material-icons" style="font-size:32px;">vpn_key</i> Log In</h1>
                        </div>
                            
                        <form method="POST" action="check_login.php">
                        <div class="detail">
                                <div style="padding-top:5%;">
                                        <div style="float:left;padding-left:15%;"><i class="material-icons" style="font-size:40px">&#xE7FD;</i></div>
                                        <div style="padding-right:10%;"><input name="uid" class="loginText" size="30" maxlength="12" required="required"></div>
                                </div>
                                
                                <div style="padding-top:5%;">
                                        <div style="float:left;padding-left:15%;"><i class="material-icons" style="font-size:40px">&#xE897;</i></div>
                                        <div style="padding-right:10%;"><input name="pwd" type="password" class="loginText" value="" size="30" maxlength="26" required="required"></div>
                                </div>
                        </div>

                        <div class="footer">             
                                <button class="loginButton">Login</button>
                        </div>
                        </form>
                </div>
            </div>
    </body>

</html>
