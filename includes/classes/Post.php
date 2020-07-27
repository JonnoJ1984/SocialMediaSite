<?php
    class Post 
    {
        private $user_obj;
        private $con; 

        public function __construct($con, $user)
        {
            $this->con = $con;

            $this->user_obj = new User($con, $user);
        }

        public function submitPost($body, $user_to)
        {
            $body = strip_tags($body); //removes HTML tags for security purposes
            $body = mysqli_real_escape_string($this->con, $body); //removes special characters like ' or " to prevent misread in database
            
            $body = str_replace('\r\n', "\n", $body); //replaces a new line with an actual new line
            $body = nl2br($body); // built in php function that replaces a new line with a line break
            
            $check_empty = preg_replace('/\s+/', '', $body); //deletes all spaces

            if($check_empty != "")
            {
                //Current and time
                $date_added = date("Y-m-d H:i:s");
                //get username
                $added_by = $this->user_obj->getUsername();

                //if user is OWN profile, user_to is 'none'
                if($user_to == $added_by)
                {
                    $user_to = "none";
                }

                //insert post into database
                $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");
                $returned_id = mysqli_insert_id($this->con);

                //Insert notification - we still need to do this

                //update post count for user
                $num_posts = $this->user_obj->getNumPosts();
                $num_posts++;
                $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
            }

        } // end of submitPost() function

        public function loadPostsFriends($data, $limit)
        {  
            $page = $data['page'];
            $userLoggedIn = $this->user_obj->getUsername();
            
            if($page == 1)
            {
                $start = 0;
            }
            else
            {
                $start = ($page -1) * $limit;
            }
            
            
            $str = ""; //String to return
            $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");

            if(mysqli_num_rows($data_query) > 0) //Conditional statement ***
            {
                $num_iterations = 0; //number of results checked; not necessarily posted
                $count = 1;

                while($row = mysqli_fetch_array($data_query))
                {
                    $id = $row['id'];
                    $body = $row['body'];
                    $added_by = $row['added_by'];
                    $date_time = $row['date_added'];

                    //Prepare user_to string so it can be included even if not posted to a user
                    if($row['user_to'] == "none")
                    {
                        $user_to = "";
                    }
                    else
                    {
                        $user_to_obj = new User($this->con, $row['user_to']);
                        $user_to_name = $user_to_obj->getFirstAndLastName();
                        $user_to = "to <a href = '" . $row['user_to'] . "'>" . $user_to_name . "</a>";
                    }

                    //Check if user who posted, has a their account closed... Account closed = don't show posts
                    $added_by_obj = new User($this->con, $added_by);
                    if($added_by_obj->isClosed())
                    {
                        continue;
                    }

                    //below only allows posts to appear on news feed that was posted by friends
                    $user_logged_obj = new User($this->con, $userLoggedIn);
                    if($user_logged_obj->isFriend($added_by))
                    {
                        if($num_iterations++ < $start)
                        {
                            continue;
                        }

                        //Once 10 posts have been loaded, break
                        if($count > $limit)
                        {
                            break;
                        }
                        else
                        {
                            $count++;
                        }

                        if($userLoggedIn == $added_by)
                        {
                            $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
                        }
                        else
                        {
                            $delete_button = "";
                        }

                        $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM
                            users WHERE username = '$added_by'");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];

?>
                        
                        <script>
                            function toggle<?php echo $id; ?>()
                            {
                                
                                var target = $(event.target);
                                if(!target.is("a"))
                                {
                                    var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                    if(element.style.display == "block")
                                    {
                                        element.style.display = "none";
                                    }
                                    else
                                    {
                                        element.style.display = "block";
                                    }
                                }
                            }

                        </script>

<?php

                        $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                        $comments_check_num = mysqli_num_rows($comments_check);
 
                        //TimeFrame
                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time); //Time of post
                        $end_date = new DateTime($date_time_now); //Current time
                        $interval = $start_date->diff($end_date); //Difference between the two dates

                        if($interval->y >= 1)
                        {
                            if($interval->y == 1)
                            {
                                $time_message = $interval->y . " year ago"; //1 year ago
                            }
                            else
                            {
                                $time_message = $interval->y . " years ago"; // > 1 year ago
                            }
                        }
                        else if($interval->m >= 1)
                        {
                            if($interval->d == 0)
                            {
                                $days = " ago";
                            }
                            else if($interval->d == 1)
                            {
                                $days = $interval->d . " day ago";
                            }
                            else
                            {
                                $days = $interval->d . " days ago";
                            }

                            if($interval->m == 1)
                            {
                                $time_message = $interval->m . " month". $days;
                            }
                            else
                            {
                                $time_message = $interval->m . " months". $days;
                            }
                        }
                        else if($interval->m < 1)
                        {
                            if($interval->d == 1)
                            {
                                $time_message = "Yesterday";
                            }
                            else if($interval->d > 1)
                            {
                                $time_message = $interval->d . " days ago";
                            }
                            else
                            {
                                if($interval->h == 0)
                                {
                                    if($interval->i < 1)
                                    {
                                        $time_message = "Less than a minute ago";
                                    }
                                    else if($interval->i == 1)
                                    {
                                        $time_message = $interval->i . " minute ago";
                                    }
                                    else 
                                    {
                                        $time_message = $interval->i . " minutes ago";
                                    }
                                    
                                }
                                else if($interval->h == 1)
                                {
                                    $time_message = $interval->h . " hour ago";
                                }
                                else if($interval->h > 1)
                                {
                                    $time_message = $interval->h . " hours ago";
                                }
                            }
                        }

                        $str .= "<div class = 'status_post' onClick = 'javascript:toggle$id()'>
                                    <div class = 'post_profile_pic'>
                                        <img src = '$profile_pic' width = '50' >
                                    </div>

                                    <div class = 'posted_by' style = 'color: #ACACAC;'>
                                        <a href = '$added_by'> $first_name $last_name </a> $user_to 
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        $time_message
                                        $delete_button
                                    </div>

                                    <div id = 'post_body'>
                                        <div class = 'body_text1'>
                                            $body
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                    </div>

                                    <div class = 'newsfeedPostOptions'>
                                        Comments ($comments_check_num)&nbsp;&nbsp;&nbsp;
                                        <iframe src = 'like.php?post_id=$id' scrolling='no'></iframe>
                                    </div>

                                </div>

                                <div class = 'post_comment' id = 'toggleComment$id' style = 'display:none;'>
                                    <iframe src = 'comment_frame.php?post_id=$id' id = 'comment_iframe' frameborder = '0'></iframe>
                                </div>

                                <hr>";
                    }

                    ?>

                    <script>
                    
                        $(document).ready(function() {

                            $('#post<?php echo $id; ?>').on('click', function(){
                                bootbox.confirm("Are you sure you want to delete this post?", function(result){
                                    $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result})

                                    if(result)
                                    {
                                        location.reload();
                                    }
                                });
                            });


                        });
                    
                    </script>

                    <?php

                } // end of while loop

                if($count > $limit)
                {
                    $str .= "<input type = 'hidden' class = 'nextPage' value ='" . ($page + 1) . "'>
                    <input type = 'hidden' class = 'noMorePosts' value = 'false'>";
                }
                else
                {
                    $str .= "<input type = 'hidden' class = 'noMorePosts' value = 'true'><p style = 'text-align: center;'> 
                    No more posts to show :) </p>";
                }



            } //end of conditional statement ***

            echo $str;
        }






        //profile page infinite load

        public function loadProfilePosts($data, $limit)
        {  
            $page = $data['page'];
            $profileUser = $data['profileUsername'];// issue
            $userLoggedIn = $this->user_obj->getUsername();
            
            if($page == 1)
            {
                $start = 0;
            }
            else
            {
                $start = ($page -1) * $limit;
            }
            
            
            $str = ""; //String to return
            $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted ='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");

            if(mysqli_num_rows($data_query) > 0) //Conditional statement ***
            {
                $num_iterations = 0; //number of results checked; not necessarily posted
                $count = 1;

                while($row = mysqli_fetch_array($data_query))
                {
                    $id = $row['id'];
                    $body = $row['body'];
                    $added_by = $row['added_by'];
                    $date_time = $row['date_added'];

                        if($num_iterations++ < $start)
                        {
                            continue;
                        }

                        //Once 10 posts have been loaded, break
                        if($count > $limit)
                        {
                            break;
                        }
                        else
                        {
                            $count++;
                        }

                        if($userLoggedIn == $added_by)
                        {
                            $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
                        }
                        else
                        {
                            $delete_button = "";
                        }

                        $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM
                            users WHERE username = '$added_by'");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];

?>
                        
                        <script>
                            function toggle<?php echo $id; ?>()
                            {
                                
                                var target = $(event.target);
                                if(!target.is("a"))
                                {
                                    var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                    if(element.style.display == "block")
                                    {
                                        element.style.display = "none";
                                    }
                                    else
                                    {
                                        element.style.display = "block";
                                    }
                                }
                            }

                        </script>

<?php

                        $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                        $comments_check_num = mysqli_num_rows($comments_check);
 
                        //TimeFrame
                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time); //Time of post
                        $end_date = new DateTime($date_time_now); //Current time
                        $interval = $start_date->diff($end_date); //Difference between the two dates

                        if($interval->y >= 1)
                        {
                            if($interval->y == 1)
                            {
                                $time_message = $interval->y . " year ago"; //1 year ago
                            }
                            else
                            {
                                $time_message = $interval->y . " years ago"; // > 1 year ago
                            }
                        }
                        else if($interval->m >= 1)
                        {
                            if($interval->d == 0)
                            {
                                $days = " ago";
                            }
                            else if($interval->d == 1)
                            {
                                $days = $interval->d . " day ago";
                            }
                            else
                            {
                                $days = $interval->d . " days ago";
                            }

                            if($interval->m == 1)
                            {
                                $time_message = $interval->m . " month". $days;
                            }
                            else
                            {
                                $time_message = $interval->m . " months". $days;
                            }
                        }
                        else if($interval->m < 1)
                        {
                            if($interval->d == 1)
                            {
                                $time_message = "Yesterday";
                            }
                            else if($interval->d > 1)
                            {
                                $time_message = $interval->d . " days ago";
                            }
                            else
                            {
                                if($interval->h == 0)
                                {
                                    if($interval->i < 1)
                                    {
                                        $time_message = "Less than a minute ago";
                                    }
                                    else if($interval->i == 1)
                                    {
                                        $time_message = $interval->i . " minute ago";
                                    }
                                    else 
                                    {
                                        $time_message = $interval->i . " minutes ago";
                                    }
                                    
                                }
                                else if($interval->h == 1)
                                {
                                    $time_message = $interval->h . " hour ago";
                                }
                                else if($interval->h > 1)
                                {
                                    $time_message = $interval->h . " hours ago";
                                }
                            }
                        }

                        $str .= "<div class = 'status_post' onClick = 'javascript:toggle$id()'>
                                    <div class = 'post_profile_pic'>
                                        <img src = '$profile_pic' width = '50' >
                                    </div>

                                    <div class = 'posted_by' style = 'color: #ACACAC;'>
                                        <a href = '$added_by'> $first_name $last_name </a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        $time_message
                                        $delete_button
                                    </div>

                                    <div id = 'post_body'>
                                        <div class = 'body_text1'>
                                            $body
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                    </div>

                                    <div class = 'newsfeedPostOptions'>
                                        Comments ($comments_check_num)&nbsp;&nbsp;&nbsp;
                                        <iframe src = 'like.php?post_id=$id' scrolling='no'></iframe>
                                    </div>

                                </div>

                                <div class = 'post_comment' id = 'toggleComment$id' style = 'display:none;'>
                                    <iframe src = 'comment_frame.php?post_id=$id' id = 'comment_iframe' frameborder = '0'></iframe>
                                </div>

                                <hr>";

                    ?>

                    <script>
                    
                        $(document).ready(function() {

                            $('#post<?php echo $id; ?>').on('click', function(){
                                bootbox.confirm("Are you sure you want to delete this post?", function(result){
                                    $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result})

                                    if(result)
                                    {
                                        location.reload();
                                    }
                                });
                            });


                        });
                    
                    </script>

                    <?php

                } // end of while loop

                if($count > $limit)
                {
                    $str .= "<input type = 'hidden' class = 'nextPage' value ='" . ($page + 1) . "'>
                    <input type = 'hidden' class = 'noMorePosts' value = 'false'>";
                }
                else
                {
                    $str .= "<input type = 'hidden' class = 'noMorePosts' value = 'true'><p style = 'text-align: center;'> 
                    No more posts to show :) </p>";
                }

            } //end of conditional statement ***

            echo $str;
        }
        //END profile page infinte load


        public function getEmail()
        {
            $username = $this->user['username'];
            $query = mysqli_query($this->con, "SELECT email FROM users WHERE username = '$username'");
            $row = mysqli_fetch_array($query);
            return $row['email'];
        }
    } //end of class parenthesis!

?>