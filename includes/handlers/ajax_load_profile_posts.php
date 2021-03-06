<?php
    include_once("../../config/config.php");
    include_once("../classes/User.php");
    include_once("../classes/Post.php");

    use FriendsCube\Post;

    $limit = 10; //Number of posts to be loaded per call

    $posts = new Post($con, $_REQUEST['userLoggedIn']);
    $posts->loadProfilePosts($_REQUEST, $limit);
?>