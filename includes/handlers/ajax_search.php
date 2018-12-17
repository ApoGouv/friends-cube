<?php
    include_once("../../config/config.php");
    include_once("../classes/User.php");
    include_once("../classes/Message.php");

    use FriendsCube\User;

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];

    $names = explode(" ", $query); //Split search query in every space

    //If query contains an underscore, assume user is searching for username
    if( strpos($query, "_") !== false ){
        $users_query = "SELECT * FROM users WHERE username LIKE :query AND user_closed=:user_closed LIMIT 8";
        $users_stmt = $con->prepare($users_query);
        $users_stmt->execute([
            'query' => '%'.$query.'%',
            'user_closed' => 'no',
        ]);
    // If there are two words, assume user is searching by FirstName LastName respectively
    }else if ( count($names) == 2 ) {
        $users_query = "SELECT * FROM users WHERE (first_name LIKE :first_name AND last_name LIKE :last_name ) AND user_closed=:user_closed LIMIT 8";
        $users_stmt = $con->prepare($users_query);
        $users_stmt->execute([
            'first_name' => '%'.$names[0].'%',
            'last_name' => '%'.$names[1].'%',
            'user_closed' => 'no',
        ]);
    // Search first OR last name
    }else {
        $users_query = "SELECT * FROM users WHERE (first_name LIKE :first_name OR last_name LIKE :last_name ) AND user_closed=:user_closed LIMIT 8";
        $users_stmt = $con->prepare($users_query);
        $users_stmt->execute([
            'first_name' => '%'.$names[0].'%',
            'last_name' => '%'.$names[0].'%',
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

            echo "<div class='resultDisplay'>
                    <a href='" . $row['username'] . "' style='color: #1485db'>
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

            if( $user->isFriend($row['username']) ){}

        }//End while
    }//End if not empty $query
?>