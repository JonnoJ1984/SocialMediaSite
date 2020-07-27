<?php
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");

    if(isset($_SESSION['username']))
    {
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    }
    else
    {
        header("Location: register.php");
    }
?>


<html>
    <head>
        <title>Neighborhood</title>

        
        <!--Javascript-->

        <script src="https://kit.fontawesome.com/bc2c777f54.js" crossorigin="anonymous"></script> 
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src = "assets/js/bootstrap.js"></script>
        <script src = "assets/js/bootbox.min.js"></script>
        <script src = "assets/js/neighborhood.js"></script>
        <script src="assets/js/jquery.jcrop.js"></script>
	    <script src="assets/js/jcrop_bits.js"></script>

        
        
        <!--CSS-->
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> 
        <link rel = "stylesheet" href = "assets/css/style.css">
        <link rel = "stylesheet" href = "assets/css/boostrap.css">
        <link rel="stylesheet" href="assets/css/jquery.Jcrop.css"/>
        
        <link rel = "shortcut icon" href = "assets/images/icons/logo1.png">

    </head>

    <body>

        <div class = "top_bar">
            <div class = "logo">
                <a href = "index.php">Neighborhood</a>
            </div>

            <nav>
                <a href = "<?php echo "$userLoggedIn"; ?>" id = "display_name">
                    <?php echo $user['username']; ?>
                </a>
                    <!--Home-->
                <a href = "index.php">
                    <i class="fas fa-home fa-2x"></i>
                </a>
                    <!--Messages-->
                <a href = "#">
                    <i class="far fa-envelope fa-2x"></i>
                </a>
                    <!--Notifications-->
                <a href = "requests.php">
                    <i class="fas fa-bell fa-2x"></i>
                </a>
                <!--Users-->
                <a href = "#">
                    <i class="fas fa-users fa-2x"></i>
                </a>
                <!--Settings-->
                <a href = "#">
                    <i class="fas fa-cog fa-2x"></i>
                </a>
                <!--Logout-->
                <a href = "includes/handlers/logout.php">
                    <i class="fas fa-sign-out-alt fa-2x"></i>
                </a>
            </nav>
        </div>


        <div class = "wrapper">
