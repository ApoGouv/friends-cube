<?php
    include_once("includes/header.php");

    use FriendsCube\Post;

    if(isset($_POST['post'])){
        $post = new Post($con, $userLoggedIn);
        $post->submitPost($_POST['post_text'], 'none');
        header("Location: index.php"); //Redirect to prevent form re-submit
        exit;
    }

?>
        <div class="row">
            <div class="col-4">
                <div class="user_details column card shadow-sm">
                    <div class="card-body row no-gutters">
                        <div class="col-auto">
                            <a href="<?php echo $userLoggedIn; ?>">
                                <img classs="img-thumbnail img-profile-pic"
                                    src="<?php echo $user['profile_pic']; ?>"
                                    alt="<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>"
                                    title="<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>">
                            </a>
                        </div>
                        <div class="col px-2">
                            <a href="<?php echo $userLoggedIn; ?>">
                                <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                            </a>
                            <p>Posts: <?php echo $user['num_posts']; ?></p>
                            <p>Likes: <?php echo $user['num_likes']; ?></p>
                        </div>
                    </div><!-- /.card-body -->
                    <?php if( $userLoggedIn === "goofy_duck"): ?>
                    <div class="card-body row no-gutters">
                        <div class="col">
                            <p>The <strong>Goofy</strong> profile pic was taken from: <a href="http://www.pngall.com/goofy-png/download/16889">here</a></p>
                            <p>under the <a href="https://creativecommons.org/licenses/by-nc/4.0/" rel="license" target="_blank">Creative Commons 4.0 BY-NC</a>.</p>
                            <p>Here we cropped the original image, but no other changes made.</p>
                        </div>
                    </div><!-- /.card-body -->
                    <?php endif; ?>
                </div><!-- /.user_details column -->
            </div><!-- /.col-4 -->
            <div class="col-8">
                <div class="news-feed card shadow-sm">
                    <div class="card-body">
                        <form action="index.php" method="POST" class="post_form">
                            <textarea name="post_text"
                                      id="post_text"
                                      placeholder="Got something to say?"></textarea><!-- /#post_text -->
                            <input type="submit" name="post" id="post_button" value="Post">
                            <hr>

                        </form><!-- /.post_form -->

                        <div class="posts_area"></div><!-- /.posts_area -->

                        <img id="loading" src="assets/images/icons/pacman-1s-200px.gif" alt="Loading...">
                    </div><!-- /.card-body -->
                </div><!-- /.news-feed -->
                <script>
                    $(document).ready(function(){

                        var userLoggedIn = '<?php echo $userLoggedIn; ?>';
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
                                url: "includes/handlers/ajax_load_posts.php",
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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
            </div><!-- /.col-8 -->
        </div><!-- /.row -->
    </div><!-- /.container wrapper -->
</body>
</html>