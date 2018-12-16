<?php
    require_once 'config/config.php'; //Require our config
    include_once("includes/classes/User.php");
    include_once("includes/classes/Post.php");
    include_once("includes/classes/Notification.php");

    use FriendsCube\User;
    use FriendsCube\Post;
    use FriendsCube\Notification;

    if( isset($_SESSION['username']) ){
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = "SELECT * FROM users WHERE username = :username";
        $user_details_stmt = $con->prepare($user_details_query);
        $user_details_stmt->execute(['username' => $userLoggedIn]);
        $user = $user_details_stmt->fetch();
    }else {
        // user is not logged in, so redirect to register page
        header("Location: register.php");
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Like | FriendsCube</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- css -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="like_wrapper">
    <?php
        //Get id of post
        if (isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
        }

        $get_like_query = "SELECT likes, added_by FROM posts WHERE id=?";
        $get_like_smtp = $con->prepare($get_like_query);
        $get_like_smtp->execute([$post_id]);

        $row = $get_like_smtp->fetch();
        $total_likes = $row['likes'];
        $user_liked = $row['added_by'];

        $user_details_obj = new User($con, $user_liked);
        $user_total_likes = $user_details_obj->getNumLikes();

        //Like Button
        if(isset($_POST['like_button'])){
            //Increase and update total post likes
            $total_likes++;
            $update_post_likes_query = "UPDATE posts SET likes =? WHERE id = ?";
            $update_post_likes_smtp = $con->prepare($update_post_likes_query);
            $update_post_likes_smtp->execute([$total_likes, $post_id]);

            //Increase and update total user's likes
            $user_total_likes++;
            $update_user_likes_query = "UPDATE users SET num_likes =? WHERE username = ?";
            $update_user_likes_smtp = $con->prepare($update_user_likes_query);
            $update_user_likes_smtp->execute([$user_total_likes, $user_liked]);

            //Add like info (user and post) to the DB
            $insert_like_query = "INSERT INTO likes VALUES ('', ?, ?)";
            $con->prepare($insert_like_query)->execute([$userLoggedIn, $post_id]);

            //Insert Notification
            if($user_liked !== $userLoggedIn){
                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($post_id, $user_liked, "like");
            }
        }
        //Unlike Button
        if(isset($_POST['unlike_button'])){
            //Decrease and update total post likes
            $total_likes--;
            $update_post_likes_query = "UPDATE posts SET likes =? WHERE id = ?";
            $update_post_likes_smtp = $con->prepare($update_post_likes_query);
            $update_post_likes_smtp->execute([$total_likes, $post_id]);

            //Decrease and update total user's likes
            $user_total_likes--;
            $update_user_likes_query = "UPDATE users SET num_likes =? WHERE username = ?";
            $update_user_likes_smtp = $con->prepare($update_user_likes_query);
            $update_user_likes_smtp->execute([$user_total_likes, $user_liked]);

            //Delete like info (user and post) from the DB
            $delete_like_query = "DELETE FROM likes WHERE username = ? AND post_id = ?";
            $con->prepare($delete_like_query)->execute([$userLoggedIn, $post_id]);

            //Insert Notification
        }

        //Check for previous likes
        $check_query = "SELECT * FROM likes WHERE username = ? AND post_id = ? ";
        $check_smtp = $con->prepare($check_query);
        $check_smtp->execute([$userLoggedIn, $post_id]);

        if ($check_smtp->rowCount() > 0){
            echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                        <button class="comment_like" name="unlike_button" >Dislike</button>
                        <div class="like_value">
                            <span class="badge badge-info">' . $total_likes . ' Likes</span>
                        </div>
                    </form>
            ';
        }else {
            echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                        <button class="comment_like" name="like_button" >Like</button>
                        <div class="like_value">
                            <span class="badge badge-info">' . $total_likes . ' Likes</span>
                        </div>
                    </form>
            ';
        }
    ?>

</body>
</html>