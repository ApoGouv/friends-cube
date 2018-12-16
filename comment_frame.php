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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Comments | FriendsCube</title>

    <!-- css -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        *{
            font-family: Arial, Helvetica, Sans-serif;
            font-size: 12px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php echo '<!-- ' . basename($_SERVER['PHP_SELF']) . ' -->'; ?>
    <script>
        function toggle(){
            var element = document.getElementById("comment_section");

            if (element.style.display == "block"){
                element.style.display = "none";
            }else {
                element.style.display = "block";
            }
        }
    </script>

    <?php
        //Get id of post
        if (isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
        }

        $user_query = "SELECT added_by, user_to FROM posts WHERE id=?";
        $stmt = $con->prepare($user_query);
        $stmt->execute([$post_id]);
        $row = $stmt->fetch();

        $posted_to = $row['added_by'];
        $user_to = $row['user_to'];

        if (isset($_POST['postComment' . $post_id])){
            $post_body = strip_tags($_POST['post_body']);
            $date_time_now = date("Y-m-d H:i:s");

            $post_comment_data = [
                'post_body' => $post_body,
                'posted_by' => $userLoggedIn,
                'posted_to' => $posted_to,
                'date_added' => $date_time_now,
                'removed' => 'no',
                'post_id' => $post_id,
            ];
            // add comment to DB
            $insert_post_query = "INSERT INTO comments VALUES ('', :post_body, :posted_by, :posted_to, :date_added, :removed, :post_id)";
            $con->prepare($insert_post_query)->execute($post_comment_data);

            //Insert Notification
            if($posted_to !== $userLoggedIn){
                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($post_id, $posted_to, "comment");
            }

            if($user_to !== 'none' && $user_to !== $userLoggedIn) {
                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($post_id, $user_to, "profile_comment");
            }

            $get_commenters_query = "SELECT * FROM comments WHERE post_id=:post_id";
            $get_commenters_stmt =  $con->prepare($get_commenters_query);
            $get_commenters_stmt->execute([
                'post_id' => $post_id
            ]);
            $notified_users = [];
            while($row = $get_commenters_stmt->fetch() ){
                if( $row['posted_by'] !== $posted_to && $row['posted_by'] !== $user_to
                    && $row['posted_by'] !== $userLoggedIn && !in_array($row['posted_by'], $notified_users)
                ){
                    $notification = new Notification($con, $userLoggedIn);
                    $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

                    $notified_users[] = $row['posted_by'];
                }
            }

            echo "<p>Comment Posted!</p>";
        } //End if

    ?>
    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>"
          id="comment_form"
          name="postComment<?php echo $post_id; ?>"
          method="POST">
        <textarea name="post_body"></textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post" />
    </form>

    <!-- Load Comments-->
    <?php
        $get_comments_query = "SELECT * FROM comments WHERE post_id=? ORDER BY id ASC";
        $get_comments_stmt = $con->prepare($get_comments_query);
        $get_comments_stmt->execute([$post_id]);

        if( $get_comments_stmt->rowCount() > 0 ){
            while ($comment = $get_comments_stmt->fetch() ){
                //Get comment data
                $comment_body = $comment['post_body'];
                $posted_to    = $comment['posted_to'];
                $posted_by    = $comment['posted_by'];
                $date_added   = $comment['date_added'];
                $removed      = $comment['removed'];

                //Timeframe
                $start_date = new DateTime($date_added); //Time of post
                $end_date = new DateTime( date("Y-m-d H:i:s") ); //Current time
                $time_message = Post::dateDiffToString($start_date, $end_date);

                $user_obj = new User($con, $posted_by);

                ?>
                <div class="comment_section">
                    <?php
                    // The target="_parent" part is important, cause otherwise
                    // our link would be opened inside the iframe
                    ?>
                    <a href="<?php echo $posted_by; ?>" target="_parent">
                        <img src="<?php echo $user_obj->getProfilePic(); ?>"
                            title = "<?php echo $user_obj->getFullName(); ?>"
                            alt="<?php echo $user_obj->getFullName(); ?>"
                            style="float:left;" height="30">
                    </a>
                    <a href="<?php echo $posted_by; ?>" target="_parent">
                        <b><?php echo $user_obj->getFullName(); ?></b>
                    </a>
                    &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?>
                    <hr>
                </div><!-- /.comment_section -->
                <?php

            } //While we have posts
        } // if( $get_comments_stmt->rowCount() > 0 )
        else {
            echo "<br><br><center>No Comments to Show!</center>";
        }
    ?>

</body>
</html>
