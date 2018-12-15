<?php
    include_once("../../config/config.php");
    include_once("../classes/User.php");
    include_once("../classes/Message.php");

    use FriendsCube\Message;

    $limit = 3; //Number of messages to load

    // $_REQUEST['userLoggedIn'] comes from the ajax call in our friendscube.js file
    $message = new Message($con, $_REQUEST['userLoggedIn']);
    echo $message->getConvosDropdown($_REQUEST, $limit);
?>