<?php
    $host='localhost';
    $username='user223';
    $password='user123';
    $database='tcap_db';

    $conn = mysqli_connect($host,$username,$password,$database);
    
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL:". mysqli_connect_error();
    }
?>