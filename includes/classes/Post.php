<?php

namespace FriendsCube;

use FriendsCube\User;
use FriendsCube\Notification;

class Post {
    private $user_obj;
    private $con;

    /**
     * Post constructor method
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
    public static function dateDiffToString(\DateTime $start_date, \DateTime $end_date){
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

    public function submitPost($body, $user_to, $imageName) {
        $body = strip_tags($body); //removes html tags
        $check_empty = preg_replace('/\s+/', '', $body); //Delete all spaces

        if ($check_empty != ""){

            // ----------------------------------
            //feature START: embed YouTube videos

            // split body at a space
            $body_array = preg_split("/\s+/", $body);

            foreach($body_array as $key => $value){
                // check if we have a YouTube video
                if( strpos($value, "www.youtube.com/watch?v=") !== false ){

                    //https://www.youtube.com/watch?v=kCC8UIPrASA&list=RDkCC8UIPrASA&start_radio=1
                    // Clean YouTube urls from lists by splitting the url at &
                    $link = preg_split("!&!", $value);

                    //modify the url to be embed ready
                    $value = preg_replace("!watch\?v=!", "embed/", $link[0]);

                    //add the embed url to an iframe
                    $value = "<br><iframe width='420' height='315' src='" . $value . "'></iframe><br>";

                    //save the modified value back to our body_array
                    $body_array[$key] = $value;
                }
            }
            // Join our body_array element back, by placing a space between each element
            $body = implode(" ", $body_array);

            //feature END: embed YouTube videos
            // --------------------------------

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
                'likes'      => 0,
                'image'      => $imageName,
            ];
            $insert_post_query = "INSERT INTO posts VALUES ('', :body, :added_by, :user_to, :date_added, :user_closed, :deleted, :likes, :image)";
            $this->con->prepare($insert_post_query)->execute($insert_post_data);
            $returned_id = $this->con->lastInsertId();

            //Insert notification
            if($user_to !== 'none'){
                $notification = new Notification($this->con, $added_by);
                $notification->insertNotification($returned_id, $user_to, "profile_post");
            }

            //Update post count
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_num_posts_query = "UPDATE users SET num_posts=:num_posts WHERE username=:username";
            $this->con->prepare($update_num_posts_query)
                ->execute([
                    'num_posts' => $num_posts,
                    'username' => $added_by
                ]);

            // --------------------------------
            //feature START: show popular words

            //stopwords list. This is a list of words that we will skip from counting as popular.
            $stopWords = "a about above across after again against all almost alone along already
			 also although always among am an and another any anybody anyone anything anywhere are
			 area areas around as ask asked asking asks at away b back backed backing backs be became
			 because become becomes been before began behind being beings best better between big
			 both but by c came can cannot case cases certain certainly clear clearly come could
			 d did differ different differently do does done down down downed downing downs during
			 e each early either end ended ending ends enough even evenly ever every everybody
			 everyone everything everywhere f face faces fact facts far felt few find finds first
			 for four from full fully further furthered furthering furthers g gave general generally
			 get gets give given gives go going good goods got great greater greatest group grouped
			 grouping groups h had has have having he her here herself high high high higher
		     highest him himself his how however i im if important in interest interested interesting
			 interests into is it its itself j just k keep keeps kind knew know known knows
			 large largely last later latest least less let lets like likely long longer
			 longest m made make making man many may me member members men might more most
			 mostly mr mrs much must my myself n necessary need needed needing needs never
			 new new newer newest next no nobody non noone not nothing now nowhere number
			 numbers o of off often old older oldest on once one only open opened opening
			 opens or order ordered ordering orders other others our out over p part parted
			 parting parts per perhaps place places point pointed pointing points possible
			 present presented presenting presents problem problems put puts q quite r
			 rather really right right room rooms s said same saw say says second seconds
			 see seem seemed seeming seems sees several shall she should show showed
			 showing shows side sides since small smaller smallest so some somebody
			 someone something somewhere state states still still such sure t take
			 taken than that the their them then there therefore these they thing
			 things think thinks this those though thought thoughts three through
	         thus to today together too took toward turn turned turning turns two
			 u under until up upon us use used uses v very w want wanted wanting
			 wants was way ways we well wells went were what when where whether
			 which while who whole whose why will with within without work
			 worked working works would x y year years yet you young younger
			 youngest your yours z lol haha omg hey ill iframe wonder else like
             hate sleepy reason for some little yes bye choose";

            //convert above stopwords into an array by splitting in whitespace
            $stopWords = preg_split("/[\s,]+/", $stopWords);

            //Replace/clear anything that is not a letter or number
            $no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

            //we will not count anything like a link
            if( strpos($no_punctuation, "height") === false  &&
                strpos($no_punctuation, "width") === false  &&
                strpos($no_punctuation, "http") === false )
            {
                $no_punctuation = preg_split("/[\s,]+/", $no_punctuation);

                foreach( $stopWords as $value ){
                    foreach( $no_punctuation as $key => $value2 ){
                        if( strtolower($value) === strtolower($value2) ){
                            $no_punctuation[$key] = "";
                        }
                    }//end inner foreach
                }//end outer foreach

                foreach ( $no_punctuation as $value ){
                    $this->calculateTrend( ucfirst($value) );
                }

            }

            //feature END: show popular words
            // ------------------------------
        }
    }

    public function calculateTrend($term){
        if($term !== ''){
            $get_trends_query = "SELECT * FROM trends WHERE title=:title";
            $get_trends_stmt = $this->con->prepare($get_trends_query);
            $get_trends_stmt->execute(["title" => $term]);

            if( $get_trends_stmt->rowCount() == 0 ){
                $insert_trends_data = [
                    'title' => $term,
                    'hits'  => 1,
                ];
                $insert_trends_query = "INSERT INTO trends(title, hits) VALUES(:title, :hits)";
                $this->con->prepare($insert_trends_query)->execute($insert_trends_data);
            }else {
                $update_trends_query = "UPDATE trends SET hits=hits+1 WHERE title=:title";
                $update_trends_stmt = $this->con->prepare($update_trends_query);
                $update_trends_stmt->execute(["title" => $term]);
            }

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
                $imagePath = $row['image'];

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
                    $start_date = new \DateTime($date_time); //Time of post
                    $end_date = new \DateTime($date_time_now); //Current time
                    $time_message = self::dateDiffToString($start_date, $end_date);

                    if($imagePath !== ""){
                        $imageDiv = "<div class='postedImage'>
                                        <img src='$imagePath'>
                                    </div>";
                    }else {
                        $imageDiv = "";
                    }

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
                                    $imageDiv
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
                $start_date = new \DateTime($date_time); //Time of post
                $end_date = new \DateTime($date_time_now); //Current time
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

    public function getSinglePost($post_id){

        $userLoggedIn = $this->user_obj->getUsername();

        $opened_query = "UPDATE notifications SET opened=:opened WHERE user_to=:user_to AND link LIKE :link";
        $this->con->prepare($opened_query)
            ->execute([
                'opened' => 'yes',
                'user_to' => $userLoggedIn,
                'link' => '%=' . $post_id . ''
            ]);

        $str = ""; // string to return

        $get_posts_query = "SELECT * FROM posts WHERE deleted=:deleted AND id=:id ORDER BY id DESC";
        $get_posts_stmt = $this->con->prepare($get_posts_query);
        $get_posts_stmt->execute([
            'deleted' => 'no',
            'id' => $post_id
        ]);

        if( $get_posts_stmt->rowCount() > 0 ){

            $row = $get_posts_stmt->fetchall()[0]; //returns array with 1 entry, so we access it with [0]

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
                return;
            }

            $user_logged_obj = new User($this->con, $userLoggedIn);
            // show our posts and or friends posts
            if($user_logged_obj->isFriend($added_by)){

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
                    $start_date = new \DateTime($date_time); //Time of post
                    $end_date = new \DateTime($date_time_now); //Current time
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
            } //End if isFriend
            else{
                echo "<p>You cannot see this post because you are not friends rith this user.</p>";
                return;
            }

        } //End if
        else {
            echo "<p>No post found. If you clicked a link, it may be broken.</p>";
            return;
        }

        echo $str;
    }

}

?>

