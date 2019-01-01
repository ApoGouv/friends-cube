<?php
    include_once("../../config/config.php");
    include_once("../classes/User.php");
    include_once("../classes/Post.php");
    include_once("../classes/Notification.php");

    use FriendsCube\Post;

    if( isset($_POST['post_body']) ){
        // for now, we set the image name to blank
        $image_name = "";
        $post = new Post($con, $_POST['user_from']);
        $post->submitPost($_POST['post_body'], $_POST['user_to'], $image_name);
    }

?>