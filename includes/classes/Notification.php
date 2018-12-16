<?php

namespace FriendsCube;

use FriendsCube\User;

class Notification {
    private $user_obj;
    private $con;

    /**
     * Notification constructor method
     *
     * @param [PDO connection] $con | the PDO mySQL connection var
     * @param [string] $user | the username
     * @return void
     */
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    protected function getUserLoggedIn(){
        return $this->user_obj->getUsername();
    }

    public function getUnreadNotificationsNumber(){
        $userLoggedIn = $this->getUserLoggedIn();
        $get_unread_notifications_number_query = "SELECT * FROM notifications WHERE viewed=:viewed AND user_to=:user_to";
        $get_unread_notifications_number_stmt = $this->con->prepare($get_unread_notifications_number_query);
        $get_unread_notifications_number_stmt->execute([
            'viewed'  => 'no',
            'user_to' => $userLoggedIn,
        ]);

        return $get_unread_notifications_number_stmt->rowCount();
    }

    /**
     * dateDiffToString
     *  use Notification::dateDiffToString($sdate, $edate)
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

    public function getNotificationsDropdown($data, $limit){
        $userLoggedIn = $this->getUserLoggedIn();
        $page = $data['page'];

        if($page == 1 ){
            $start = 0;
        }else {
            $start = ($page - 1) * $limit;
        }

        $return_string = "";

        $set_viewed_query = "UPDATE notifications SET viewed=:viewed WHERE user_to=:user_to";
        $set_viewed_stmt = $this->con->prepare($set_viewed_query);
        $set_viewed_stmt->execute([
            'viewed'  => 'yes',
            'user_to' => $userLoggedIn,
        ]);

        $get_notifications_query = "SELECT * FROM notifications WHERE user_to=:user_to ORDER BY id DESC";
        $get_notifications_stmt = $this->con->prepare($get_notifications_query);
        $get_notifications_stmt->execute([
            'user_to'   => $userLoggedIn,
        ]);

        if($get_notifications_stmt->rowCount() == 0 ){
            echo "You have no notifications!";
            return;
        }

        $num_iterations = 0; //Number of messages checked
        $count = 1; //Number Of messages posted

        while( $row = $get_notifications_stmt->fetch() ) {

            if($num_iterations++ < $start){
                continue;
            }

            if($count > $limit){
                break; // We reached our limit, so stop/break
            }else{
                $count++;
            }

            $user_from = $row['user_from'];
            $user_from_data = new User($this->con, $user_from);

            //Timeframe
            $start_date = new \DateTime($row['datetime']); //Time of message
            $end_date = new \DateTime( date("Y-m-d H:i:s") ); //Current time
            $time_message = Self::dateDiffToString($start_date, $end_date);

            $opened = $row['opened'];
            $style = ($row['opened'] === 'no') ? "background-color: #ddedff;" : "";

            $return_string .= "
                <a href='" . $row['link'] . "'>
                    <div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
                        <div class='notificationsProfilePic'>
                            <img src='" . $user_from_data->getProfilePic() ."'>
                        </div>
                        <p class='lead timestamp timestamp_smaller'>" . $time_message . "</p>
                        " . $row['message'] . "
                    </div>
                </a>";
        }

        //If Messages were loaded
        if($count > $limit){
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1 ). "' >";
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='false' >";
        }else {
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true' >";
            $return_string .= "<p class='text-center'>No more notifications to Load!</p>";
        }

        return $return_string;
    }

    public function insertNotification($post_id, $user_to, $type){
        $userLoggedIn = $this->getUserLoggedIn();
        $userLoggedInName = $this->user_obj->getFullName();

        $date_time = date("Y-m-d H:i:s");

        switch($type){
            case 'like':
                $message = $userLoggedInName . " liked your post";
                break;
            case 'profile_post':
                $message = $userLoggedInName . " posted on your profile";
                break;
            case 'comment':
                $message = $userLoggedInName . " commented on your post";
                break;
            case 'comment_non_owner':
                $message = $userLoggedInName . " commented on a post you commented on";
                break;
            case 'profile_comment':
                $message = $userLoggedInName . " commented on your profile post";
                break;
        }

        $link = "post.php?id=" . $post_id;

        $notification_data = [
            'user_to'      => $user_to,
            'user_from'    => $userLoggedIn,
            'message'      => $message,
            'link'         => $link,
            'datetime'     => $date_time,
            'opened'       => 'no',
            'viewed'       => 'no',
        ];

        $insert_notification_query = "INSERT INTO notifications VALUES('', :user_to, :user_from, :message, :link, :datetime, :opened, :viewed)";
        $this->con->prepare($insert_notification_query)->execute($notification_data);

    }

}