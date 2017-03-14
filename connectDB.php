<?php

class connectDB {
    
    public function connect(){ 
        $host = "localhost";
        $username = "root";
        $password = "";
        $db = "ordereas_db";

        $link = mysqli_connect($host, $username, $password, $db);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        /*
        else {
            echo "MySQL Connected";
        }
         * 
         */
        return $link;
    }
    
    public function close($link) {
        mysqli_close($link);
    }
    
}
?>

