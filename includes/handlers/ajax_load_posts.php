<?php
include_once("../../config/config.php");
include_once("../classes/User.php");
include_once("../classes/Post.php");

use FriendsCube\Post;
/**
 * We can use only $_POST here, or we can use $_REQUEST
 * */

 /*
if ( isset($_POST['userLoggedIn']) ){
    $limit = 10; //Number of posts to be loaded per call

    $posts = new Post($con, $_POST['userLoggedIn']);
    $posts->loadPostsFriends();
}else{
    return header('Location: ../../../register.php');
}
*/

$limit = 10; //Number of posts to be loaded per call

$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->loadPostsFriends($_REQUEST, $limit);



?>