<?php
    include("includes/header.php");
    $message_obj = new Message($con, $userLoggedIn);

    if( isset($_GET['u']) ){
        $user_to = $_GET['u'];
    }else{
        $user_to = $message_obj->getMostRecentUser();
        if($user_to == false){
            $user_to = 'new';
        }
    }//End if..else

    if($user_to !== "new"){
        $user_to_obj = new User($con, $user_to);
    }

    if( isset( $_POST['post_message'] ) ){
        if( isset( $_POST['message_body'] ) ){

            $body = strip_tags($_POST['message_body']);
            $date = date("Y-m-d H:i:s");
            $message_obj->sendMessage($user_to, $body, $date);
            header("Location: messages.php?u=$user_to"); //Redirect to prevent form re-submit
            exit;
        }
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
                </div><!-- /.user_details column -->
                <div class="user_details column card shadow-sm" id="conversations">
                    <div class="card-body row no-gutters">
                        <div class="col">
                            <h4>Conversations</h4>
                            <div class="loaded_conversations">
                                <?php echo $message_obj->getConvos(); ?>
                            </div><!-- /.loaded_conversations -->
                            <br>
                            <a href="messages.php?u=new" class="btn btn-info">New Message</a>
                        </div>
                    </div><!-- /.card-body -->
                </div><!-- /.user_details column -->
            </div><!-- /.col-4 -->
            <div class="col-8">
                <div class="messages-feed card shadow-sm">
                    <div class="card-body">
                        <?php
                            if($user_to !== 'new'){
                                echo "<h4> You and <a href='$user_to'>" . $user_to_obj->getFullName() . "</a></h4><hr><br>";
                                echo "<div class='loaded_messages' id='scroll_messages'>";
                                    echo $message_obj->getMessages($user_to);
                                echo "</div><!-- /.loaded_messages -->";
                            }else {
                                echo "<h4>New Message</h4>";
                            }
                        ?>

                        <div class="message_post">
                            <form action="" method="POST">
                                <?php
                                    if($user_to == "new"){
                                        echo "Select the friend you would like to message <br><br>";
                                        ?>
                                        To: <input type='text'
                                                   onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")'
                                                   name='q'
                                                   placeholder='Name'
                                                   autocomplete='off'
                                                   id='search_text_input'>
                                        <?php
                                        echo "<div class='results'></div>";
                                    }else {
                                        echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
                                        echo "<input type='submit' name='post_message' class='btn btn-info' id='message_submit' value='Send'>";
                                    }
                                ?>
                            </form>
                        </div>
                        <script>
                            var div = document.getElementById("scroll_messages");
                            if  (div !== null){
                                div.scrollTop = div.scrollHeight; //scroll to the bottom of the messages div
                            }
                        </script>
                    </div><!-- /.card-body -->
                </div><!-- /.messages-feed -->
            </div><!-- /.col-8 -->
        </div><!-- /.row -->
    </div><!-- /.container wrapper -->
</body>
</html>