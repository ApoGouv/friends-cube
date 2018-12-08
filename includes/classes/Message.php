<?php

class Message {
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
        $start_date = new DateTime($row['date']); //Time of message
        $end_date = new DateTime( date("Y-m-d H:i:s") ); //Current time
        $time_message = Post::dateDiffToString($start_date, $end_date);

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

}//End of Message class

?>

