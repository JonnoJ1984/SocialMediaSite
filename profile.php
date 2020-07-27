        <?php 
            include("includes/header.php");
            //No need to include Post and User classes; included in header

            if(isset($_GET['profile_username'])) {
                $username = $_GET['profile_username'];
                $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
                $user_array = mysqli_fetch_array($user_details_query);

                $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
            }

            if(isset($_POST['remove_friend'])) {
                $user = new User($con, $userLoggedIn);
                $user->removeFriend($username);
            }

            if(isset($_POST['add_friend'])) {
                $user = new User($con, $userLoggedIn);
                $user->sendRequest($username);
            }
            if(isset($_POST['respond_request'])) {
                header("Location: requests.php");
            }

        ?>

        <style type="text/css">
            .wrapper 
            {
                margin-left: 0px;
                padding-left: 0px;
            }

        </style>
        
        <div class="profile_left">
            <img src="<?php echo $user_array['profile_pic']; ?>">

                <div class="profile_info">
                    <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
                    <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
                    <p><?php echo "Friends: " . $num_friends ?></p>
                </div>

                <form action="<?php echo $username; ?>" method="POST">
                    <?php 
                        $profile_user_obj = new User($con, $username); 
                        if($profile_user_obj->isClosed()) {
                            header("Location: user_closed.php");
                        }

                        $logged_in_user_obj = new User($con, $userLoggedIn); 

                        if($userLoggedIn != $username) {

                            if($logged_in_user_obj->isFriend($username)) {
                                echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
                            }
                            else if ($logged_in_user_obj->didReceiveRequest($username)) {
                                echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
                                
                            }
                            else if ($logged_in_user_obj->didSendRequest($username)) {
                                echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
                            }
                            else 
                                echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';

                        }
                    ?>  
                </form>

                <!--Post on another's timeline -->
                <input type = "submit" class = "deep_blue" data-toggle = "modal" data-target = "#post_form" value = "Post Something">

                <?php
                    if($userLoggedIn != $username)
                    {
                        $numberOfFriends = $logged_in_user_obj->getMutualFriends($username);
                        if($numberOfFriends == 1)
                        {
                            echo '<div class="profile_info_bottom">';
                                echo $numberOfFriends . " Mutual friend";
                            echo '</div>';
                        }
                        else
                        {
                            echo '<div class="profile_info_bottom">';
                                echo $numberOfFriends . " Mutual friends";
                            echo '</div>';
                        }
                        
                    }

                ?>

            </div>

            <!--                                   MAIN DIV IS RIGHT HERE                                   -->
            <div class="profile_main_column column">
                <div class = "posts_area"></div>
                <img id = "loading" src = "assets/images/icons/load2.gif" height = 5%>
                

            </div>

            <!-- Modal -->
            <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                <div class="modal-header">
                
                    <h5 class="modal-title" id="postModalLabel">Post something</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <p>This will appear on the both the user's profile page <em>and</em> their Newsfeed for all your friends to see.</p>
                    
                    <form class = "profile_post" action = "" method = "POST">
                        <div class = "form-group">
                            <textarea class = "form-control" name = "post_body"></textarea>
                            <input type = "hidden" name = "user_from" value = "<?php echo $userLoggedIn; ?>">
                            <input type = "hidden" name = "user_to" value = "<?php echo $username; ?>">
                        </div>
                    </form>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name = "post_button" id = "submit_profile_post">Post</button>
                </div>
                </div>
            </div>
            </div>         

            <!-- Infinite Loading for the profile page-->
            <script>
                var userLoggedIn = '<?php echo $userLoggedIn; ?>';
                var profileUsername = '<?php echo $username; ?>';

                $(document).ready(function() {

                    $('#loading').show();

                    //Original ajax request for loading first posts 
                    $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache:false,

                    success: function(data) {
                        $('#loading').hide();
                        $('.posts_area').html(data);
                    }
                    });

                    $(window).scroll(function() {
                        var height = $('.posts_area').height(); //Div containing posts
                        var scroll_top = $(this).scrollTop();
                        var page = $('.posts_area').find('.nextPage').val();
                        var noMorePosts = $('.posts_area').find('.noMorePosts').val();
                        

                        if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                            $('#loading').show();
                            
                            /*stylesheet.insertRule(".profile_left { height: 100%;}", 3);*/

                            var ajaxReq = $.ajax({
                            url: "includes/handlers/ajax_load_profile_posts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                            cache:false,

                            success: function(response) 
                            {
                                $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                                $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                                $('#loading').hide();
                                $('.posts_area').append(response);
                            }
                            });

                        } //End if 

                        return false;

                    }); //End (window).scroll(function())


                });

            </script>


        </div>
    </body>
</html>