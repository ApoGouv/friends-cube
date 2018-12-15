<?php
    include_once("../../config/config.php");
    include_once("../classes/User.php");

    use FriendsCube\User;

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];

    $names = explode(" ", $query);

    // assume user is searching for username IF their search contains _
    if( strpos($query, "_") !== false ){
        $users_query = "SELECT * FROM users WHERE username LIKE :query AND user_closed=:user_closed LIMIT 8";
        $users_stmt = $con->prepare($users_query);
        $users_stmt->execute([
            'query' => '%'.$query.'%',
            'user_closed' => 'no',
        ]);
    }else if ( count($names) == 2 ) { // assume user is searching by FirstName LastName
        $users_query = "SELECT * FROM users WHERE (first_name LIKE :firstname AND last_name LIKE :lastname ) AND user_closed=:user_closed LIMIT 8";
        $users_stmt = $con->prepare($users_query);
        $users_stmt->execute([
            'firstname' => '%'.$names[0].'%',
            'lasttname' => '%'.$names[1].'%',
            'user_closed' => 'no',
        ]);
    }else {
        $users_query = "SELECT * FROM users WHERE (first_name LIKE :firstname OR last_name LIKE :lastname ) AND user_closed=:user_closed LIMIT 8";
        $users_stmt = $con->prepare($users_query);
        $users_stmt->execute([
            'firstname' => '%'.$names[0].'%',
            'lastname' => '%'.$names[0].'%',
            'user_closed' => 'no',
        ]);
    }

    if ( $query != "" ) {
        while( $row = $users_stmt->fetch() ) {
            $user = new User($con, $userLoggedIn);

            if( $row['username'] !== $userLoggedIn ){
                $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
            }else {
                $mutual_friends = "";
            }

            if( $user->isFriend($row['username']) ){
                echo "<div class='resultDisplay'>
                        <a href='messages.php?u=" . $row['username'] . "' style='color: #000'>
                            <div class='liveSearchProfilePic'>
                                <img src='" . $row['profile_pic'] . "' >
                            </div>
                            <div class='liveSearchText'>
                                " . $row['first_name'] . " " . $row['last_name'] . "
                                <p>" . $row['username'] . "</p>
                                <p class='lead'>" . $mutual_friends . "</p>
                            </div>
                        </a>
                     </div>";
            }

        }//End while
    }//End if not empty $query

?>