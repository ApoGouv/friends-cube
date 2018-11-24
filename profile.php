<?php
    include("includes/header.php");

    if(isset($_GET['profile_username'])){
        $username = $_GET['profile_username'];

        $user_details_query = "SELECT * FROM users WHERE username = ?";
        $user_details_stmt = $con->prepare($user_details_query);
        $user_details_stmt->execute(array($username));
        $user_array = $user_details_stmt->fetch();

        //Count how many friends user has | -1 is cause we always have a , in the friends array
        $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
    }

    // Handle "Remove Friend" button submit
    if(isset($_POST['remove_friend'])){
        $user = new User($con,$userLoggedIn);
        $user->removeFriend($username);
    }

    // Handle "Add Friend" button submit
    if(isset($_POST['add_friend'])){
        $user = new User($con,$userLoggedIn);
        $user->sendRequest($username);
    }

    // Handle "Respond to Request" button submit
    if(isset($_POST['respond_request'])){
        header("Location: requests.php");
    }
?>

         <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo $user_array['profile_pic']; ?>" alt="<?php echo $username . ' pic'; ?>">
            </div>
            <div class="profile_info">
                <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
                <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
                <p><?php echo "Friends: " . $num_friends; ?></p>
            </div>

            <form action="<?php echo $username; ?>" method="POST">
                <?php
                    $profile_user_obj = new User($con, $username);
                    if( $profile_user_obj->isClosed() ){
                        header("Location: user_closed.php");
                    }

                    $logged_in_user_obj = new User($con, $userLoggedIn);

                    if( $userLoggedIn !== $username ){

                        if($logged_in_user_obj->isFriend($username)){
                            echo '<input type="submit" name="remove_friend" class="btn btn-danger" value="Remove Friend"><br>';
                        } else if( $logged_in_user_obj->didReceiveRequest($username) ){
                            echo '<input type="submit" name="respond_request" class="btn btn-warning" value="Respond to Request"><br>';
                        } else if( $logged_in_user_obj->didSendRequest($username) ){
                            echo '<input type="submit" name="" class="btn btn-default" value="Request Sent"><br>';
                        } else{
                            echo '<input type="submit" name="add_friend" class="btn btn-success" value="Add Friend"><br>';
                        }
                    }
                ?>
            </form>
            <!-- Button trigger post modal -->
            <input type="submit" class="btn btn-info" data-toggle="modal" data-target="#post_form" value="Post Something">

            <?php
                if ($userLoggedIn !== $username){
                    echo '<div class="profile_info_bottom">';
                        echo '<p>' . $logged_in_user_obj->getMutualFriends($username) . ' Mutual friends</p>';
                    echo '</div>';
                }
            ?>

        </nav>

        <div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="news-feed card shadow-sm">
                        <div class="card-body">

                            <div class="posts_area"></div><!-- /.posts_area -->

                            <img id="loading" src="assets/images/icons/pacman-1s-200px.gif" alt="Loading...">
                            <?php
                                /* highlight_string("<?php\n\$user_array =\n" . var_export($user_array, true) . ";\n?>"); */
                            ?>
                        </div><!-- /.card-body -->

                        <!-- Modal -->
                        <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="postModalLabel">Post Something!</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>

                                        <form class="profile_post" action="" method="POST">
                                            <div class="form-group">
                                                <textarea name="post_body" class="form-control"></textarea>
                                                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                                                <input type="hidden" name="user_to" value="<?php echo $username; ?>">
                                            </div><!-- /.form-group -->
                                        </form>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal.fade -->

                        <script>
                            $(document).ready(function(){

                                var userLoggedIn = '<?php echo $userLoggedIn; ?>';
                                var profileUsername = '<?php echo $username; ?>';

                                var inProgress = false;
                                loadPosts(); //Load first posts

                                $(window).scroll(function() {
                                    var bottomElement = $(".status_post").last();
                                    var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                                    // isElementInViewport uses getBoundingClientRect(), which requires
                                    // the HTML DOM object, not the jQuery object. The jQuery equivalent
                                    // is using [0] as shown below.
                                    if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                                        loadPosts();
                                    }
                                }); //End $(window).scroll(function())

                                function loadPosts() {
                                    //If it is already in the process of loading some posts, just return
                                    if(inProgress) {
                                        return;
                                    }

                                    inProgress = true;
                                    $('#loading').show();
                                    // If .nextPage couldn't be found, it must not be on the page yet
                                    // (it must be the first time loading posts), so use the value '1'
                                    var page = $('.posts_area').find('.nextPage').val() || 1;

                                    $.ajax({
                                        url: "includes/handlers/ajax_load_profile_posts.php",
                                        type: "POST",
                                        data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUser=" + profileUsername,
                                        cache:false,
                                        success: function(response) {
                                            $('.posts_area').find('.nextPage').remove(); //Removes current .nextPage
                                            $('.posts_area').find('.noMorePosts').remove(); //Removes current .noMorePosts
                                            $('.posts_area').find('.noMorePostsText').remove(); //Removes current .noMorePostsText
                                            $('#loading').hide();
                                            $(".posts_area").append(response);
                                            inProgress = false;

                                        }
                                    });//End $.ajax()
                                } //End function loadPosts()

                                //Check if the element is in view
                                function isElementInView (el) {
                                        if(el == null) {
                                            return;
                                        }
                                    var rect = el.getBoundingClientRect();
                                    return (
                                        rect.top >= 0 &&
                                        rect.left >= 0 &&
                                        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                                        rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
                                    );
                                } //End function isElementInView (el)

                            }); //End $(document).ready(function())

                        </script>

                    </div><!-- /.news-feed -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->
        </div><!-- /#content -->

    </div><!-- /.container wrapper -->
</body>
</html>