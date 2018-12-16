<?php
    include_once("../../config/config.php");
    include_once("../classes/User.php");
    include_once("../classes/Notification.php");

    use FriendsCube\Notification;

    $limit = 4; //Number of messages to load

    // $_REQUEST['userLoggedIn'] comes from the ajax call in our friendscube.js file
    $notification = new Notification($con, $_REQUEST['userLoggedIn']);
    echo $notification->getNotificationsDropdown($_REQUEST, $limit);
?>