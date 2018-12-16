<?php

namespace FriendsCube;

use FriendsCube\User;

class Message {
    private $user_obj;
    private $con;

    /**
     * Message constructor method
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
     *  use Message::dateDiffToString($sdate, $edate)
     *
     * @param DateTime $start_date
     * @param DateTime $end_date
     * @return string of time difference
     */
    protected static function dateDiffToString(\DateTime $start_date, \DateTime $end_date){
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

    public function getMostRecentUser(){
        $userLoggedIn = $this->user_obj->getUsername();

        $select_users_query = "SELECT user_to, user_from FROM messages WHERE user_to=:user_to OR user_from=:user_from ORDER BY id DESC LIMIT 1";
        $select_users_stmt = $this->con->prepare($select_users_query);
        $select_users_stmt->execute([
            'user_to' =>$userLoggedIn,
            'user_from' => $userLoggedIn
        ]);

        if($select_users_stmt->rowCount() == 0){
            return false;
        }

        // We use fetchAll and since we have 1 result we access it with [0]
        // **with fetch we got false as a result**
        $row = $select_users_stmt->fetchAll()[0];

        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if($user_to !== $userLoggedIn){
            return $user_to;
        }else {
            return $user_from;
        }

    }

    protected function getUserLoggedIn(){
        return $this->user_obj->getUsername();
    }


    public function sendMessage($user_to, $body, $date){

        if($body !== ''){
            $userLoggedIn = $this->getUserLoggedIn();

            $message_data = [
                'user_to'      => $user_to,
                'userLoggedIn' => $userLoggedIn,
                'body'         => $body,
                'date'         => $date,
                'opened'       => 'no',
                'viewed'       => 'no',
                'deleted'      => 'no',
            ];

            $insert_message_query = "INSERT INTO messages VALUES('', :user_to, :userLoggedIn, :body, :date, :opened, :viewed, :deleted)";
            $this->con->prepare($insert_message_query)->execute($message_data);

        }

    }

    public function getMessages($otherUser){
        $userLoggedIn = $this->getUserLoggedIn();
        $data = "";

        /* Update messages between 2 users to 'opened' */
        $update_messages_to_open_query = "UPDATE messages SET opened=:opened WHERE user_to=:user_to AND user_from=:user_from";
        $update_messages_to_open_stmt = $this->con->prepare($update_messages_to_open_query);
        $update_messages_to_open_stmt->execute([
            'opened'    => 'yes',
            'user_to'   => $userLoggedIn,
            'user_from' => $otherUser,
        ]);

        /* Get the messages between 2 users */
        $get_messages_query = "SELECT * FROM messages WHERE (user_to=:user_to_a AND user_from=:user_from_a) OR (user_from=:user_from_b AND user_to=:user_to_b)";
        $get_messages_stmt = $this->con->prepare($get_messages_query);
        $get_messages_stmt->execute([
            'user_to_a'   => $userLoggedIn,
            'user_from_a' => $otherUser,
            'user_from_b' => $userLoggedIn,
            'user_to_b'   => $otherUser,
        ]);

        while( $row = $get_messages_stmt->fetch() ) {
            $user_to   = $row['user_to'];
            $user_from = $row['user_from'];
            $body      = $row['body'];

            $div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
            $data = $data . $div_top . $body . "</div><br><br>";
        }
        return $data;
    }

    public function getLatestMessage($userLoggedIn, $user2) {

        $details_array = [];

        $get_lmessage_query = "SELECT body, user_to, date FROM messages WHERE (user_to=:user_to_a AND user_from=:user_from_a) OR (user_to=:user_to_b AND user_from=:user_from_b) ORDER BY id DESC LIMIT 1";
        $get_lmessage_stmt = $this->con->prepare($get_lmessage_query);
        $get_lmessage_stmt->execute([
            'user_to_a'   => $userLoggedIn,
            'user_from_a' => $user2,
            'user_to_b'   => $user2,
            'user_from_b' => $userLoggedIn,
        ]);

        // Get result
        $row = $get_lmessage_stmt->fetchAll()[0];

        $send_by = ($row['user_to'] === $userLoggedIn) ? "They said: " : "You said: ";

        //Timeframe
        $start_date = new \DateTime($row['date']); //Time of message
        $end_date = new \DateTime( date("Y-m-d H:i:s") ); //Current time
        $time_message = Self::dateDiffToString($start_date, $end_date);

        // Push data to our array
        $details_array[] = $send_by;
        $details_array[] = $row['body'];
        $details_array[] = $time_message;

        return $details_array;
    }

    public function getConvos() {
        $userLoggedIn = $this->getUserLoggedIn();

        $return_string = "";
        $convos = [];

        $get_messages_query = "SELECT user_to, user_from FROM messages WHERE user_to=:user_to OR user_from=:user_from ORDER BY id DESC";
        $get_messages_stmt = $this->con->prepare($get_messages_query);
        $get_messages_stmt->execute([
            'user_to'   => $userLoggedIn,
            'user_from' => $userLoggedIn,
        ]);

        while( $row = $get_messages_stmt->fetch() ){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $convos)) {
                $convos[] = $user_to_push;
            }
        }

        foreach( $convos as $username ) {
            $user_found_obj = new User($this->con, $username);
            $latest_mesage_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = ( strlen($latest_mesage_details[1]) >= 12 ) ? "..." : "";
            $split = str_split( $latest_mesage_details[1], 12 );
            $split = $split[0] . $dots;

            $return_string .= "
                <a href='messages.php?u=$username'>
                    <div class='user_found_messages'>
                        <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
                        " . $user_found_obj->getFullName() . "<br>
                        <span class='lead timestamp timestamp_smaller'>" . $latest_mesage_details[2] . "</span>
                        <p class='lead gray' style='margin: 0px;'>" . $latest_mesage_details[0] . $split . "</p>
                    </div>
                </a>";
        }

        return $return_string;

    }

    public function getConvosDropdown($data, $limit){
        $userLoggedIn = $this->getUserLoggedIn();
        $page = $data['page'];

        if($page == 1 ){
            $start = 0;
        }else {
            $start = ($page - 1) * $limit;
        }

        $return_string = "";
        $convos = [];

        $set_viewed_query = "UPDATE messages SET viewed=:viewed WHERE user_to=:user_to";
        $set_viewed_stmt = $this->con->prepare($set_viewed_query);
        $set_viewed_stmt->execute([
            'viewed'  => 'yes',
            'user_to' => $userLoggedIn,
        ]);

        $get_messages_query = "SELECT user_to, user_from FROM messages WHERE user_to=:user_to OR user_from=:user_from ORDER BY id DESC";
        $get_messages_stmt = $this->con->prepare($get_messages_query);
        $get_messages_stmt->execute([
            'user_to'   => $userLoggedIn,
            'user_from' => $userLoggedIn,
        ]);

        while( $row = $get_messages_stmt->fetch() ){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $convos)) {
                $convos[] = $user_to_push;
            }
        }

        $num_iterations = 0; //Number of messages checked
        $count = 1; //Number Of messages posted

        foreach( $convos as $username ) {

            if($num_iterations++ < $start){
                continue;
            }

            if($count > $limit){
                break; // We reached our limit, so stop/break
            }else{
                $count++;
            }

            $is_unread_query = "SELECT  opened FROM messages WHERE user_to=:user_to AND user_from=:user_from ORDER BY id DESC";
            $is_unread_stmt = $this->con->prepare($is_unread_query);
            $is_unread_stmt->execute([
                'user_to'   => $userLoggedIn,
                'user_from' => $username,
            ]);
            $row = $is_unread_stmt->fetch();
            $style = ($row['opened'] === 'no') ? "background-color: #ddedff;" : "";

            $user_found_obj = new User($this->con, $username);
            $latest_mesage_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = ( strlen($latest_mesage_details[1]) >= 12 ) ? "..." : "";
            $split = str_split( $latest_mesage_details[1], 12 );
            $split = $split[0] . $dots;

            $return_string .= "
                <a href='messages.php?u=$username'>
                    <div class='user_found_messages' style='" . $style . "'>
                        <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
                        " . $user_found_obj->getFullName() . "<br>
                        <span class='lead timestamp timestamp_smaller'>" . $latest_mesage_details[2] . "</span>
                        <p class='lead gray' style='margin: 0px;'>" . $latest_mesage_details[0] . $split . "</p>
                    </div>
                </a>";
        }

        //If Messages were loaded
        if($count > $limit){
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1 ). "' >";
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='false' >";
        }else {
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true' >";
            $return_string .= "<p class='text-center'>No more messages to Load!</p>";
        }

        return $return_string;
    }

    public function getUnreadMessagesNumber(){
        $userLoggedIn = $this->getUserLoggedIn();
        $get_unread_messages_number_query = "SELECT * FROM messages WHERE viewed=:viewed AND user_to=:user_to";
        $get_unread_messages_number_stmt = $this->con->prepare($get_unread_messages_number_query);
        $get_unread_messages_number_stmt->execute([
            'viewed'  => 'no',
            'user_to' => $userLoggedIn,
        ]);

        return $get_unread_messages_number_stmt->rowCount();
    }

}//End of Message class

?>

