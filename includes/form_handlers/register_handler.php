<?php
    //require 'config/config.php'; Removed this because register.php laready includes config.php; 
    //therefore shouldn't need to repeat this statement
    //session_start();

    //Declaring variables to prevent errors
    $fname = ""; //First name
    $lname = ""; //Last name
    $em = ""; //Email
    $em2 = ""; //Email 2
    $password = ""; //Password
    $password2 = ""; //Password2
    $date = ""; //Date user signed up
    $error_array = array(); //hold any errors we get like email used


    //if the register button is pressed, start handling the form
    if(isset($_POST['register_button']))
    {
        
        //Now we start taking the values

        //First name
        $fname = strip_tags($_POST['reg_fname']); //remove any html tgs for security
        $fname = str_replace(' ', '', $fname); //remove accidental spaces
        $fname = ucfirst(strtolower($fname)); //Uppercase first letter, lowercase the rest
        $_SESSION['reg_fname'] = $fname; //stores first name into session variable
                
        //Last name
        $lname = strip_tags($_POST['reg_lname']); //remove any html tgs for security
        $lname = str_replace(' ', '', $lname); //remove accidental spaces
        $lname = ucfirst(strtolower($lname)); //Uppercase first letter, lowercase the rest
        $_SESSION['reg_lname'] = $lname; //stores last name into session variable

        //Email
        $em = strip_tags($_POST['reg_email']); //remove any html tgs for security
        $em = str_replace(' ', '', $em); //remove accidental spaces
        $em = strtolower($em); //Lowercase everything
        $_SESSION['reg_email'] = $em; //stores email into session variable

        //Email 2
        $em2 = strip_tags($_POST['reg_email2']); //remove any html tgs for security
        $em2 = str_replace(' ', '', $em2); //remove accidental spaces
        $em2 = strtolower($em2); //Lowercase everything
        $_SESSION['reg_email2'] = $em2; //stores email2 into session variable

        //Password
        $password = strip_tags($_POST['reg_password']); //remove any html tgs for security
        //$password = str_replace(' ', '', $password); //DON'T Want to remove things here!
        //$em2 = strtolower($em2); //Lowercase everything - with passwords I think we should leave this out...
        $_SESSION['reg_password'] = $password; //stores password name into session variable

        //Password2
        $password2 = strip_tags($_POST['reg_password2']); //remove any html tgs for security
        $_SESSION['reg_password2'] = $password2; //stores password into session variable
        
        //date of register
        $date = date("Y-m-d"); //gets the current date


        //Condition: if emails match
        if($em == $em2)
        {
            //Check if email is in a valid format
            if(filter_var($em, FILTER_VALIDATE_EMAIL))
            {
                $em = filter_var($em, FILTER_VALIDATE_EMAIL);

                //check if email exists
                $e_check = mysqli_query($con, "SELECT email FROM users WHERE email = '$em' ");

                //count the number of rows returned
                $num_rows = mysqli_num_rows($e_check);

                if($num_rows > 0)
                {
                    array_push($error_array, "Email is already in use!<br> ");
                }
            }
            else
            {
                array_push($error_array, "Invalid format of email!<br>  ");
            }
        }
        else
        {
            array_push($error_array,  "Emails do not match.<br>  ");
        }
        
        //Validate first name
        if(strlen($fname) > 25 || strlen($fname) < 2)
        {
            array_push($error_array,  "Your first name must be between 2 and 25 characters!<br>  ");
        }

        //Validate last name
        if(strlen($lname) > 25 || strlen($lname) < 2)
        {
            array_push($error_array, "Your last name must be between 2 and 25 characters!<br>  ");
        }

        //Validate password match
        if($password != $password2)
        {
            array_push($error_array, "Your passwords must match!<br>  ");
        }
        else
        {
            //Check to see if password is in correct format/language
            if(preg_match('/[^A-Za-z0-9]/', $password))
            {
                array_push($error_array,  "You password can only contain English characters and numbers.<br> ");
            }
        }
        
        //check length of password; no need to do this for password2 because you check to see if they match
        if(strlen($password) > 30 || strlen($password) < 5)
        {
            array_push($error_array,  "Your password must be between 5 and 30 characters!<br> ");
        }

        if(empty($error_array))
        {
            $password = md5($password); //Encrypt password before sending to database

            //Generate username by concatenating last name with first name
            $username = strtolower($fname . "_" . $lname);
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");

            $i = 0;
            //if username exists add number to username
            while(mysqli_num_rows($check_username_query) != 0)
            {
                $i++; //increment
                $username = $username . "_" . $i;
                $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");
            }
           

            //profile picture assignment - random
            $rand = rand(1, 2);
            if($rand == 1)
            {
                $profile_pic = "assets/images/profile_pics/defaults/default1.png";
            }
            else if($rand == 2)
            {
                $profile_pic = "assets/images/profile_pics/defaults/default2.png";
            }

            //if all goes well, here is where the values are added to the database!
            $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

            //Prints the confirmation text once the person has registered correctly; see php in form below too**
            array_push($error_array, "<span style='color: red;'>Awesome - you're all set; go ahead and log in!</span></br>");

            //clear session variables
            $_SESSION['reg_fname'] = "";
            $_SESSION['reg_lname'] = "";
            $_SESSION['reg_email'] = "";
            $_SESSION['reg_email2'] = "";
        }
    } //end of the register button!
?>