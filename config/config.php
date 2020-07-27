<?php
    ob_start(); //This turns on output buffering
    session_start(); //allows us to store entries inside a session variable; prevents over re-entering data
    // see SESSION_ variables in register_handler 

    $timezone = date_default_timezone_set("America/Chicago");

    $con = mysqli_connect("localhost", "root", "", "social"); //connection variable

    if(mysqli_connect_errno())
    {
        echo "Failed to connect to database: " . mysqli_connect_errno(); 
    }
?>