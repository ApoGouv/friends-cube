<?php

class Post {
    private $user_obj;
    private $con;

    /**
     * User constructor method
     *
     * @param [PDO connection] $con | the PDO mySQL connection var
     * @param [string] $user | the username
     * @return void
     */
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    /**
     * dateDiffToString
     *  use Post::dateDiffToString($sdate, $edate)
     *
     * @param DateTime $start_date
     * @param DateTime $end_date
     * @return string of time difference
     */
    public static function dateDiffToString(DateTime $start_date, DateTime $end_date){
        $time_message = '';
        $interval = $start_date->diff($end_date); //Difference between dates
        /*
         * $interval->y |Number of years.
         * $interval->m |Number of months.
         * $interval->d |Number of days.
         * $interval->h |Number of hours.
         * $interval->i |Number of minutes.
         * $interval->s |Number of seconds.
         * */
        if( $interval->y >= 1 ){
            if($interval == 1){
                $time_message = $interval->y . " year ago"; //1 year ago
            }else {
                $time_message = $interval->y ." years ago"; //1+ years ago
            }
        }else if ( $interval->m >= 1 ){
            if( $interval->d == 0 ){
                $days = " ago";
            }else if( $interval->d == 1 ){
                $days = $interval->d . " day ago";
            }else {
                $days = $interval->d ." days ago";
            }

            if( $interval->m == 1 ){
                $time_message = $interval->m . " month". $days;
            }else{
                $time_message = $interval->m . " months". $days;
            }
        }else if ( $interval->d >= 1 ){
            if( $interval->d == 1 ){
                $time_message = "Yesterday";
            }else {
                $time_message = $interval->d ." days ago";
            }
        }else if ( $interval->h >= 1 ){
            if( $interval->h == 1 ){
                $time_message = $interval->h . " hour ago";
            }else {
                $time_message = $interval->h ." hours ago";
            }
        }else if ( $interval->i >= 1 ){
            if( $interval->i == 1 ){
                $time_message = $interval->i . " minute ago";
            }else {
                $time_message = $interval->i ." minutes ago";
            }
        }else if ( $interval->s <= 30 ){
            if( $interval->h == 1 ){
                $time_message = "Just now";
            }else {
                $time_message = $interval->s ." seconds ago";
            }
        }
        return $time_message;
    }

    public function getUserObj(){
        return $this->user_obj;
    }

    public function submitPost($body, $user_to) {
        $body = strip_tags($body); //removes html tags
        $check_empty = preg_replace('/\s+/', '', $body); //Delete all spaces

        if ($check_empty != ""){

            //Current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get username
            $added_by = $this->user_obj->getUsername();

            //If user is on own profile, user_to is 'none'
            if( $user_to == $added_by ){
                $user_to = "none";
            }

            //Insert post
            $insert_post_data = [
                'body'       => $body,
                'added_by'   => $added_by,
                'user_to'    => $user_to,
                'date_added' => $date_added,
                'user_closed' => 'no',
                'deleted'    => 'no',
                'likes'      => 0
            ];
            $insert_post_query = "INSERT INTO posts VALUES ('', :body, :added_by, :user_to, :date_added, :user_closed, :deleted, :likes)";
            $this->con->prepare($insert_post_query)->execute($insert_post_data);
            $returned_id = $this->con->lastInsertId();

            //Insert notification

            //Update post count
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_num_posts_query = "UPDATE users SET num_posts=:num_posts WHERE username=:username";
            $this->con->prepare($update_num_posts_query)
                ->execute([
                    'num_posts' => $num_posts,
                    'username' => $added_by
                ]);
        }
    }

    public function loadPostsFriends($data, $limit){
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();

        if($page == 1){ // No posts have been loaded yet
            $Start = 0;
        }else {
            $start = ($page - 1) * $limit;
        }

        $str = ""; // string to return

        $get_posts_query = "SELECT * FROM posts WHERE deleted = :deleted ORDER BY id DESC";
        $get_posts_stmt = $this->con->prepare($get_posts_query);
        $get_posts_stmt->execute(['deleted' => 'no']);

        if( $get_posts_stmt->rowCount() > 0 ){

            $num_iterations = 0; //Number of results checked (not necessarily posted)
            $count = 1;

            while ($row = $get_posts_stmt->fetch() ){ //OR
            //foreach ($get_posts_stmt->fetchall() as $row ){
                /*highlight_string("<?php\n\$row =\n" . var_export($row, true) . ";\n?>");*/

                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                //Prepare user_to string so it can be included even if not posted to a user
                if($row['user_to'] == 'none'){
                    $user_to = "";
                }else {
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFullName();
                    $user_to = " to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
                }

                //Check if user who posted, has their account closed
                $added_by_obj = new User($this->con, $added_by);
                if($added_by_obj->isClosed()){
                    continue;
                }

                $user_logged_obj = new User($this->con, $userLoggedIn);
                // show our posts and or friends posts
                if($user_logged_obj->isFriend($added_by)){

                    if($num_iterations++ < $start ){
                        continue;
                    }

                    //Once we have loaded $limit (e.g: 10) posts, break
                    if( $count > $limit ){
                        break;
                    }else {
                        $count++;
                    }

                    if($userLoggedIn == $added_by){
                        $delete_button = "<button class='delete_button btn btn-danger' id='post$id'>X</button>";
                    }else {
                        $delete_button = "";
                    }


                    //Added by User data
                    $user_details_query = "SELECT first_name, last_name, profile_pic FROM users WHERE username=:username";
                    $user_details_stmt = $this->con->prepare($user_details_query);
                    $user_details_stmt->execute(['username'=> $added_by]);
                    $user_row = $user_details_stmt->fetch();

                    /*highlight_string("<?php\n\$user_row =\n" . var_export($user_row, true) . ";\n?>");*/

                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_pic = $user_row['profile_pic'];

                    ?>
                    <script>
                        function toggle<?php echo $id; ?>(){

                            var target = $(event.target);
                            if(!target.is("a")){
                                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                if (element.style.display == "block"){
                                    element.style.display = "none";
                                }else {
                                    element.style.display = "block";
                                }
                            }

                        }
                    </script>

                    <?php

                    $check_comments_query = "SELECT * FROM comments WHERE post_id=? ORDER BY id ASC";
                    $check_comments_stmt = $this->con->prepare($check_comments_query);
                    $check_comments_stmt->execute([$id]);
                    $comments_check_num = $check_comments_stmt->rowCount();

                    //Timeframe
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time); //Time of post
                    $end_date = new DateTime($date_time_now); //Current time
                    $time_message = self::dateDiffToString($start_date, $end_date);

                    $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                                <div class='post_profile_pic'>
                                    <img src='$profile_pic' width='50'>
                                </div>

                                <div class='posted_by' style='color:#ACACAC;'>
                                    <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                    $delete_button
                                </div>

                                <div id='post_body'>
                                    $body
                                    <br>
                                    <br>
                                    <br>
                                </div>

                                <div class='friendscubePostOptions'>
                                    Comments <span class='badge badge-dark'>$comments_check_num</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <iframe src='like.php?post_id=$id' id='like_iframe' frameborder='0' scrolling='no'></iframe>
                                </div>

                            </div>
                            <div class='post_comment' id='toggleComment$id' style='display:none;'>
                                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                            </div>
                            <hr>";
                } //End if

                ?>
                <script>
                    $(document).ready(function(){

                        $('#post<?php echo $id; ?>').on('click', function(){
                            bootbox.confirm("Are you sure you want to delete this post?", function(result){

                                $.post( "includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", { result:result } );

                                if(result){
                                    location.reload();
                                }

                            })
                        });

                    });
                </script>

                <?php
            }//End while - foreach
            if ( $count > $limit ){
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                         <input type='hidden' class='noMorePosts' value='false'>";
            }else {
                $str .= "<input type='hidden' class='noMorePosts' value='true'>
                         <p class='noMorePostsText' style='text-align: center;'>No more posts to show!</p>";
            }

        } //End if

        echo $str;

    }//End of loadPostsFriends()

    public function loadProfilePosts($data, $limit){

        $page = $data['page'];
        $profileUser = $data['profileUser'];

        $userLoggedIn = $this->user_obj->getUsername();

        if($page == 1){ // No posts have been loaded yet
            $Start = 0;
        }else {
            $start = ($page - 1) * $limit;
        }

        $str = ""; // string to return

        $get_posts_query = "SELECT * FROM posts WHERE deleted = :deleted AND ((added_by= :added_by AND user_to='none') OR user_to= :user_to ) ORDER BY id DESC";
        $get_posts_stmt = $this->con->prepare($get_posts_query);
        $get_posts_stmt->execute([
            'deleted' => 'no',
            'added_by' => $profileUser,
            'user_to' => $profileUser
        ]);

        if( $get_posts_stmt->rowCount() > 0 ){

            $num_iterations = 0; //Number of results checked (not necessarily posted)
            $count = 1;

            while ($row = $get_posts_stmt->fetch() ){ //OR
            //foreach ($get_posts_stmt->fetchall() as $row ){
                /*highlight_string("<?php\n\$row =\n" . var_export($row, true) . ";\n?>");*/

                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                if($num_iterations++ < $start ){
                    continue;
                }

                //Once we have loaded $limit (e.g: 10) posts, break
                if( $count > $limit ){
                    break;
                }else {
                    $count++;
                }

                if($userLoggedIn == $added_by){
                    $delete_button = "<button class='delete_button btn btn-danger' id='post$id'>X</button>";
                }else {
                    $delete_button = "";
                }


                //Added by User data
                $user_details_query = "SELECT first_name, last_name, profile_pic FROM users WHERE username=:username";
                $user_details_stmt = $this->con->prepare($user_details_query);
                $user_details_stmt->execute(['username'=> $added_by]);
                $user_row = $user_details_stmt->fetch();

                /*highlight_string("<?php\n\$user_row =\n" . var_export($user_row, true) . ";\n?>");*/

                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];

                ?>
                <script>
                    function toggle<?php echo $id; ?>(){

                        var target = $(event.target);
                        if(!target.is("a")){
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if (element.style.display == "block"){
                                element.style.display = "none";
                            }else {
                                element.style.display = "block";
                            }
                        }

                    }
                </script>

                <?php

                $check_comments_query = "SELECT * FROM comments WHERE post_id=? ORDER BY id ASC";
                $check_comments_stmt = $this->con->prepare($check_comments_query);
                $check_comments_stmt->execute([$id]);
                $comments_check_num = $check_comments_stmt->rowCount();

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $time_message = self::dateDiffToString($start_date, $end_date);

                $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                            <div class='post_profile_pic'>
                                <img src='$profile_pic' width='50'>
                            </div>

                            <div class='posted_by' style='color:#ACACAC;'>
                                <a href='$added_by'>$first_name $last_name</a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                $delete_button
                            </div>

                            <div id='post_body'>
                                $body
                                <br>
                                <br>
                                <br>
                            </div>

                            <div class='friendscubePostOptions'>
                                Comments <span class='badge badge-dark'>$comments_check_num</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                <iframe src='like.php?post_id=$id' id='like_iframe' frameborder='0' scrolling='no'></iframe>
                            </div>

                        </div>
                        <div class='post_comment' id='toggleComment$id' style='display:none;'>
                            <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                        </div>
                        <hr>";

                ?>
                <script>
                    $(document).ready(function(){

                        $('#post<?php echo $id; ?>').on('click', function(){
                            bootbox.confirm("Are you sure you want to delete this post?", function(result){

                                $.post( "includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", { result:result } );

                                if(result){
                                    location.reload();
                                }

                            })
                        });

                    });
                </script>

                <?php
            }//End while - foreach
            if ( $count > $limit ){
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                         <input type='hidden' class='noMorePosts' value='false'>";
            }else {
                $str .= "<input type='hidden' class='noMorePosts' value='true'>
                         <p class='noMorePostsText' style='text-align: center;'>No more posts to show!</p>";
            }

        } //End if

        echo $str;

    }//End of loadProfilePosts()

}

?>

