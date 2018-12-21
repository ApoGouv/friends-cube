<?php
    include_once("includes/header.php");
    use FriendsCube\User;

    if( isset($_GET['q']) ){
        $query = $_GET['q'];
    }else {
        $query = "";
    }

    if( isset($_GET['type']) ){
        $type = $_GET['type'];
    }else {
        $type = "name";
    }
?>

        <div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="news-feed card shadow-sm">
                        <div class="card-body search-page">
                            <?php if( $query === "" ): ?>
                                <p>You must enter something in the search box.</p>
                            <?php else: ?>
                                <?php
                                    //If query contains an underscore, assume user is searching for username
                                    if( $type === "username" ){
                                        $users_query = "SELECT * FROM users WHERE username LIKE :query AND user_closed=:user_closed";
                                        $users_stmt = $con->prepare($users_query);
                                        $users_stmt->execute([
                                            'query' => '%'.$query.'%',
                                            'user_closed' => 'no',
                                        ]);
                                    }else {
                                        $names = explode(" ", $query); //Split search query in every space
                                        // If there are 3 words, assume user is searching by FirstName Middle LastName respectively
                                        if ( count($names) == 3 ) {
                                            $users_query = "SELECT * FROM users WHERE (first_name LIKE :first_name AND last_name LIKE :last_name ) AND user_closed=:user_closed";
                                            $users_stmt = $con->prepare($users_query);
                                            $users_stmt->execute([
                                                'first_name' => '%'.$names[0].'%',
                                                'last_name' => '%'.$names[2].'%',
                                                'user_closed' => 'no',
                                            ]);
                                        // If there are two words, assume user is searching by FirstName LastName respectively
                                        }else if (count($names) == 2 ) {
                                            $users_query = "SELECT * FROM users WHERE (first_name LIKE :first_name AND last_name LIKE :last_name ) AND user_closed=:user_closed";
                                            $users_stmt = $con->prepare($users_query);
                                            $users_stmt->execute([
                                                'first_name' => '%'.$names[0].'%',
                                                'last_name' => '%'.$names[1].'%',
                                                'user_closed' => 'no',
                                            ]);
                                        // Search first OR last name
                                        }else {
                                            $users_query = "SELECT * FROM users WHERE (first_name LIKE :first_name OR last_name LIKE :last_name ) AND user_closed=:user_closed";
                                            $users_stmt = $con->prepare($users_query);
                                            $users_stmt->execute([
                                                'first_name' => '%'.$names[0].'%',
                                                'last_name' => '%'.$names[0].'%',
                                                'user_closed' => 'no',
                                            ]);
                                        }
                                    }//End outer if..else

                                    //Check if results were found
                                    if($users_stmt->rowCount() == 0 ){
                                        echo "We can't find anyone with a " . $type . " like: " . $query;
                                    }else {
                                        echo $users_stmt->rowCount() . " results found: <br><br>";
                                    }

                                    echo "<p class='lead'>Try searching for:</p>";
                                    echo "<a href='search.php?q=" . $query . "&type=name'>Names</a>, <a href='search.php?q=" . $query . "&type=username'>Usernames</a><br><br><hr>";

                                    while($row = $users_stmt->fetch()){
                                        $user_obj = new User($con, $user['username']);

                                        $button = "";
                                        $mutual_friends = "";

                                        if($user['username'] !== $row['username']){

                                            //Generate button depending on friendship status
                                            if( $user_obj->isFriend($row['username']) ){
                                                $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-danger' value='Remove Friend' >";
                                            }else if( $user_obj->didReceiveRequest($row['username']) ){
                                                $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-warning' value='Respond to request' >";
                                            }else if( $user_obj->didSendRequest($row['username']) ){
                                                $button = "<input type='submit' class='btn btn-secondary' value='Request Sent' >";
                                            }else{
                                                $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-success' value='Add Friend' >";
                                            }

                                            $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";

                                            //Button forms
                                            if(isset($_POST[$row['username']])){

                                                if($user_obj->isFriend($row['username'])){
                                                    $user_obj->removeFriend($row['username']);
                                                    header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                                                }else if($user_obj->didReceiveRequest($row['username'])){
                                                    header("Location: requests.php");
                                                }else if($user_obj->didSendRequest($row['username'])) {
                                                    //ToDo: maybe cancel request?
                                                }else{
                                                    $user_obj->sendRequest($row['username']);
                                                    header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                                                }

                                            }

                                        }

                                        echo "<div class='search_result'>
                                                <div class='searchPageFriendButtons'>
                                                    <form action='' method='POST'>
                                                        " . $button . "
                                                    </form>
                                                </div>

                                                <div class='result_profile_pic'>
                                                    <a href='" . $row['username'] . "'><img src='" . $row['profile_pic'] . "' style='height: 100px;' ></a>
                                                </div>

                                                <a href='" . $row['username'] . "'> " . $row['first_name'] . " " . $row['last_name'] . "
                                                    <p class='lead'> " . $row['username'] . "</p>
                                                </a>
                                                " . $mutual_friends . "
                                                <br>

                                              </div><hr>";
                                    }//End while
                                ?>
                            <?php endif; ?>
                        </div><!-- /.card-body -->
                    </div><!-- /.news-feed -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->
        </div><!-- /#content -->
    </div><!-- /.container wrapper -->
</body>
</html>