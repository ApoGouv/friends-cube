<?php
    include("includes/header.php");
?>


        <div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="news-feed card shadow-sm">
                        <div class="card-body">
                            <h4 class="text-center">Friend Requests</h4>
                            <?php
                                $query = "SELECT * FROM friend_requests WHERE user_to = ?";
                                $stmt = $con->prepare($query);
                                $stmt->execute(array($userLoggedIn));

                                if ( $stmt->rowCount() == 0 ){
                                    echo "You have no friend requests at the time!";
                                }else{
                                    while( $row = $stmt->fetch() ){
                                        $user_from = $row['user_from'];
                                        $user_from_obj = new User($con, $user_from);

                                        $user_from_name_and_link_to_account = "
                                            <a href='" . $user_from_obj->getUsername() . "'>" . $user_from_obj->getFullName() . "</a>
                                        ";

                                        echo $user_from_name_and_link_to_account . " sent you a friend request!";

                                        $user_from_friend_array = $user_from_obj->getFriendArray();

                                        if( isset( $_POST['accept_request_' . $user_from ] ) ){
                                            $add_friend_query = "UPDATE users SET friend_array = CONCAT(friend_array, ?) WHERE username = ?";

                                            $add_friend_stmt = $con->prepare($add_friend_query);
                                            $add_friend_stmt->execute(array( $user_from.',', $userLoggedIn ));// Add to current user friends list
                                            $add_friend_stmt->execute(array( $userLoggedIn.',', $user_from ));// Add to "friend's" user friends list

                                            $remove_friend_request_query = "DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?";
                                            $remove_friend_request_stmt = $con->prepare($remove_friend_request_query);
                                            $remove_friend_request_stmt->execute(array( $userLoggedIn, $user_from ));

                                            echo "You are now friends!";
                                            header("Location: requests.php");
                                        }
                                        if( isset( $_POST['ignore_request_' . $user_from ] ) ){
                                            $remove_friend_request_query = "DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?";
                                            $remove_friend_request_stmt = $con->prepare($remove_friend_request_query);
                                            $remove_friend_request_stmt->execute(array( $userLoggedIn, $user_from ));

                                            echo "Request Ignores!";
                                            header("Location: requests.php");
                                        }

                                        ?>
                                        <form action="requests.php" method="POST">
                                            <input type="submit"
                                                name="accept_request_<?php echo $user_from; ?>"
                                                id="accept_button"
                                                class="btn btn-success"
                                                value="Accept">
                                            <input type="submit"
                                                name="ignore_request_<?php echo $user_from; ?>"
                                                id="ignore_button"
                                                class="btn btn-danger"
                                                value="Ignore">
                                        </form>
                                        <?php
                                    }//End while
                                }
                            ?>

                        </div><!-- /.card-body -->
                    </div><!-- /.news-feed -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->
        </div><!-- /#content -->
    </div><!-- /.container wrapper -->
</body>
</html>