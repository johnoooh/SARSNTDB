<?php
$host="127.0.0.1:3306";
$port=80;
$socket="";
$user="web_app";
$password="RhVETxdWIwpZ5DoR";
$dbname="web_app";


$con = new mysqli($host, $user, $password, "web_app")

    or die ('Could not connect to the database server' . mysqli_connect_error());

    //Return error code if the connection fails - OA
    if ($con->connect_errno) {
        echo "Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
    }
?>
