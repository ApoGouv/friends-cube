<?php
    include_once("includes/header.php");
    use FriendsCube\User;
    use FriendsCube\Post;

    if(isset($_GET['id'])){
        $id = $_GET['id'];
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
                        <div class="posts_area">
                            <?php
                                $post = new Post($con, $userLoggedIn);
                                $post->getSinglePost($id);
                            ?>
                        </div>
                    </div><!-- /.card-body -->
                </div><!-- /.news-feed -->
            </div><!-- /.col-8 -->
        </div><!-- /.row -->
    </div><!-- /.container wrapper -->
</body>
</html>