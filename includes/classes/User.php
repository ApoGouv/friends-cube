<?php
class User {
    private $user;
    /* in the end we will get something like this
    $this->user = array (
        'id' => 2,
        'first_name' => 'Apo',
        'last_name' => 'Gouv',
        'username' => 'apo_gouv_2',
        'email' => 'tester001@social-cube.gr',
        'password' => '$2y$12$HbIqgckB5yhbJQDYHj2VQug4cqTvU4GoZ1uk7MDksIxPVjj6m4BTy',
        'signup_date' => '2018-10-20',
        'profile_pic' => 'assets/images/profile_pics/defaults/head_green_sea.png',
        'num_posts' => 0,
        'num_likes' => 0,
        'user_closed' => 'no',
        'friend_array' => ',',
    );
    */
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
        $user_details_query = "SELECT * FROM users WHERE username = :username";
        $user_details_stmt = $con->prepare($user_details_query);
        $user_details_stmt->execute(array('username' => $user));
        $this->user = $user_details_stmt->fetch();
    }

    /**
     * getFullName() function
     * @return [string] 'first_name lastname'
     */
    public function getFullName() {
        return $this->user['first_name'] . ' ' . $this->user['last_name'];
    }

    /**
     * getUsername() function
     * @return [string] 'username'
     */
    public function getUsername(){
        return $this->user['username'];
    }

    public function getNumPosts(){
        return $this->user['num_posts'];
    }
    public function isClosed(){
        return ($this->user['user_closed'] == 'yes' ) ? true : false;
    }

    public function getFriendArray(){
        return $this->user['friend_array'];
    }

    public function getProfilePic(){
        return $this->user['profile_pic'];
    }

    public function getNumLikes(){
        return $this->user['num_likes'];
    }

    public function isFriend($username_to_check){
        $usernameComma = "," . $username_to_check . ",";

        if( strstr($this->getFriendArray(), $usernameComma) || $username_to_check == $this->getUsername() ){
            return true;
        }else {
            return false;
        }
    }

    public function didReceiveRequest($user_from){
        $user_to = $this->user['username'];

        $check_request_query = "SELECT * FROM friend_requests WHERE user_to=? AND user_from=?";
        $check_request_stmt = $this->con->prepare($check_request_query);
        $check_request_stmt->execute(array($user_to, $user_from));

        if( $check_request_stmt->rowCount() > 0 ){
            return true;
        }else {
            return false;
        }
    }

    public function didSendRequest($user_to){
        $user_from = $this->user['username'];

        $check_request_query = "SELECT * FROM friend_requests WHERE user_to=? AND user_from=?";
        $check_request_stmt = $this->con->prepare($check_request_query);
        $check_request_stmt->execute(array($user_to, $user_from));

        if( $check_request_stmt->rowCount() > 0 ){
            return true;
        }else {
            return false;
        }
    }

    public function removeFriend($user_to_remove){
        $logged_in_user = $this->user['username'];

        $query = "SELECT friend_array FROM users WHERE username = ?";
        $stmt = $this->con->prepare($query);
        $stmt->execute(array($user_to_remove));
        $row = $stmt->fetch();

        $friend_array_username = $row['friend_array'];

        $update_user_friend_array_query = "UPDATE users SET friend_array = ? WHERE username = ? ";

        $new_friend_array = str_replace( $user_to_remove . ",", "", $this->user['friend_array'] );
        $update_user_friend_array_stmt = $this->con->prepare($update_user_friend_array_query);
        $update_user_friend_array_stmt->execute(array($new_friend_array, $logged_in_user));

        $new_user_to_remove_friend_array = str_replace( $this->user['username'] . ",", "", $friend_array_username );
        $update_user_to_remove_friend_array_stmt = $this->con->prepare($update_user_friend_array_query);
        $update_user_to_remove_friend_array_stmt->execute(array($new_user_to_remove_friend_array, $user_to_remove));
    }

    // when pressing Add Friend
    public function sendRequest($user_to){
        $user_from = $this->user['username'];

        $query = "INSERT INTO friend_requests VALUES('', ? , ?)";
        $stmt = $this->con->prepare($query);
        $stmt->execute(array($user_to, $user_from));
    }

    public function getMutualFriends($user_to_check){
        $mutualFriends = 0;
        $user_array_explode = explode(',', $this->getFriendArray());

        $user_to_check_query = "SELECT friend_array FROM users WHERE username = ?";
        $stmt = $this->con->prepare($user_to_check_query);
        $stmt->execute(array($user_to_check));
        $row = $stmt->fetch();

        $user_to_check_friend_array = $row['friend_array'];
        $user_to_check_friend_array_explode = explode(',', $user_to_check_friend_array);

        foreach( $user_array_explode as $i ){
            foreach($user_to_check_friend_array_explode as $j) {
                if( $i == $j && $i != "" ){
                    $mutualFriends++;
                }
            }
        }

        return $mutualFriends;
    }

} //End User

?>