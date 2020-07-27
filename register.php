<?php
    require 'config/config.php';
    require 'includes/form_handlers/register_handler.php';
    require 'includes/form_handlers/login_handler.php';
?>

<html>
<head>
    <title>Welcome to the Neighborhood!</title>
    <link rel = "stylesheet" type = "text/css" href = "assets/css/register_style.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src = "assets/js/register.js"></script>
    <link rel = "shortcut icon" href = "assets/images/icons/logo1.png">
</head>

    <body>

        <?php
            if(isset($_POST['register_button']))
            {
                echo '
                <script>
                    $(document).ready(function()
                    {
                        $("#first").hide();
                        $("#second").show();
                    });
                </script>
                ';
            }
        ?>

        <div class = "wrapper">

                    <div class = "login_box">

                        <div class = "login_header">
                            <h1>Neighborhood</h1>
                            Log in or sign up to get started!
                        </div>

                        <!--  Login form  -->
                        <div id = "first">

                            <form action = "register.php" method = "POST">
                                <input type="email" name = "log_email" placeholder = "Email Address" value = "<?php 
                                    if(isset($_SESSION['log_email'])) 
                                    {
                                        echo $_SESSION['log_email'];
                                    }
                                    ?>" required>
                                <br>
                                <input type="password" name = "log_password" placeholder = "Password">
                                <br>
                                
                                <!--Error message - if there is one -->
                                <?php 
                                    if(in_array("Email or password was incorrect.<br>", $error_array))
                                        echo "Email or password was incorrect.<br>";
                                ?>

                                <input type="submit" name= "login_button" value = "Login">
                                <br>

                                <a href = "#" id = "signup" class = "signup">Need an account?  Register here!</a>
                            </form>
                        </div>
                        

                        <!--  Register form  -->

                        <div id = "second">
                            <form action = "register.php" method = "POST">
                                <input type = "text" name="reg_fname" placeholder = "First Name" value = "<?php 
                                    if(isset($_SESSION['reg_fname'])) 
                                    {
                                        echo $_SESSION['reg_fname'];
                                    }
                                    ?>" required>
                                <br>
                                <?php 
                                    if(in_array("Your first name must be between 2 and 25 characters!<br>  ", $error_array))
                                        echo "Your first name must be between 2 and 25 characters!<br>  ";
                                ?>

                                <input type = "text" name="reg_lname" placeholder = "Last Name" value = "<?php 
                                    if(isset($_SESSION['reg_lname'])) 
                                    {
                                        echo $_SESSION['reg_lname'];
                                    }
                                    ?>" required>
                                <br>
                                <?php 
                                    if(in_array("Your last name must be between 2 and 25 characters!<br>  ", $error_array))
                                        echo "Your last name must be between 2 and 25 characters!<br>  ";
                                ?>

                                <input type = "email" name="reg_email" placeholder = "Email" value = "<?php 
                                    if(isset($_SESSION['reg_email'])) 
                                    {
                                        echo $_SESSION['reg_email'];
                                    }
                                    ?>" required>
                                <br>
                                <?php 
                                    if(in_array("Email is already in use!<br> ", $error_array))
                                        echo "Email is already in use!<br> ";
                                    else if(in_array("Invalid format of email!<br>  ", $error_array))
                                        echo "Invalid format of email!<br>  ";
                                ?>

                                <input type = "email" name="reg_email2" placeholder = "Confirm Email" value = "<?php 
                                    if(isset($_SESSION['reg_email2'])) 
                                    {
                                        echo $_SESSION['reg_email2'];
                                    }
                                    ?>" required>
                                <br>
                                <?php 
                                    if(in_array("Emails do not match.<br>  ", $error_array))
                                        echo "Emails must match!<br>  ";
                                ?>

                                <input type = "password" name="reg_password" placeholder = "Password" required>
                                <br>
                                <?php 
                                    if(in_array("You password can only contain English characters and numbers.<br> ", $error_array))
                                        echo "You password can only contain English characters and numbers.<br> ";
                                    else if(in_array("Your password must be between 5 and 30 characters!<br> ", $error_array))
                                        echo "Your password must be between 5 and 30 characters!<br> ";
                                ?>

                                <input type = "password" name="reg_password2" placeholder = "Confirm Password" required>
                                <br>
                                <?php 
                                    if(in_array("Your passwords must match!<br>  ", $error_array))
                                        echo "Your passwords must match!<br>  ";
                                ?>

                                <input type = "submit" name = "register_button" value = "Register">
                                <br>
                                <?php 
                                    //confirms correct registration from error_array above **
                                    if(in_array("<span style='color: red;'>Awesome - you're all set; go ahead and log in!</span></br>", $error_array))
                                        echo "<span style='color: red;'>Awesome - you're all set; go ahead and log in!</span></br>";
                                ?>

                                <a href = "#" id = "signin" class = "signin">Already have an account?  Sign in here!</a>

                            </form>
                        </div>

                        
                    </div>
        </div>

    </body>
</html>