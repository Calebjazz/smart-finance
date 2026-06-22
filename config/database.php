<?php

$Host = "localhost";
$username = "root";
$password = "";
$database = "financedb";

//connection to database
$conn = mysqli_connect( "localhost", "root", "", "financedb" );

// Checking connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>