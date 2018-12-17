<?php
    require_once 'config/config.php'; //Require our config
    include_once("includes/classes/User.php");
    include_once("includes/classes/Post.php");
    include_once("includes/classes/Message.php");
    include_once("includes/classes/Notification.php");

    use FriendsCube\User;
    use FriendsCube\Message;
    use FriendsCube\Notification;

    if( isset($_SESSION['username']) ){
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = "SELECT * FROM users WHERE username = :username";
        $user_details_stmt = $con->prepare($user_details_query);
        $user_details_stmt->execute(['username' => $userLoggedIn]);
        $user = $user_details_stmt->fetch();

        /*
        $user = array (
            'id' => 2,
            'first_name' => 'Apo',
            'last_name' => 'Gouv',
            'username' => 'apo_gouv_2',
            'email' => ' the email of the user ',
            'password' => ' the pass of the user ',
            'signup_date' => '2018-10-20',
            'profile_pic' => 'assets/images/profile_pics/defaults/head_green_sea.png',
            'num_posts' => 0,
            'num_likes' => 0,
            'user_closed' => 'no',
            'friend_array' => ',',
        );
        */

    }else {
        // user is not logged in, so redirect to register page
        header("Location: register.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to FriendsCube</title>

    <!-- css -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/jquery.Jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <script src="assets/js/friendscube.js"></script>
</head>
<body>
    <?php echo '<!-- ' . basename($_SERVER['PHP_SELF']) . ' -->'; ?>
    <div id="main-nav">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-expand-md navbar-dark bg-dark fixed-top">
                <a class="navbar-brand" href="index.php">FriendsCube!</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavContent" aria-controls="mainNavContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="search">
                    <form class="form my-2 my-lg-0" action="search.php" method="GET" name="search_form">
                        <input
                            id="search_text_input"
                            class="form-control"
                            type="search"
                            onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')"
                            name="q"
                            placeholder="Search..."
                            aria-label="Search"
                            autocomplete="off"
                            >
                        <div class="button_holder">
                            <i class="fas fa-search"></i>
                        </div><!-- /.button_holder -->
                        <div class="search_results"></div><!-- /.search_results -->
                        <div class="search_results_footer_empty"></div><!-- /.search_results_footer_empty -->
                    </form>
                </div><!-- /.search -->

                <?php
                    //Unread Messages
                    $messages = new Message($con, $userLoggedIn);
                    $unread_num_messages = $messages->getUnreadMessagesNumber();

                    //Unread Notifications
                    $notifications = new Notification($con, $userLoggedIn);
                    $unread_num_notifications = $notifications->getUnreadNotificationsNumber();

                    //New Friend Request Notifications
                    $user_obj = new User($con, $userLoggedIn);
                    $fr_req__num = $user_obj->getNumberOfFriendRequests();
                ?>

                <div class="justify-content-end collapse navbar-collapse" id="mainNavContent">
                    <ul class="navbar-nav main-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $userLoggedIn; ?>">
                                <?php echo $user['first_name']; ?>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home fa-lg"></i> <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                                <i class="fas fa-envelope fa-lg"></i>
                                <?php if( $unread_num_messages > 0 ): ?>
                                <span class="badge badge-danger" id="unread_message"><?php echo $unread_num_messages; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                                <i class="fas fa-bell fa-lg"></i>
                                <?php if( $unread_num_notifications > 0 ): ?>
                                <span class="badge badge-danger" id="unread_notification"><?php echo $unread_num_notifications; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="requests.php">
                                <i class="fas fa-users fa-lg"></i>
                                <?php if( $fr_req__num > 0 ): ?>
                                <span class="badge badge-danger" id="unread_friend_requests"><?php echo $fr_req__num; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog fa-lg"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="includes/handlers/logout.php">
                                <i class="fas fa-sign-out-alt fa-lg"></i>
                            </a>
                        </li>
                    </ul><!-- /.navbar-nav.main-menu-->
                </div><!-- /.collapse.navbar-collapse -->
            </nav><!-- /.navbar.navbar-dark -->

            <div class="dropdown_data_window" style="height:0px;"></div><!-- /.dropdown_data_window -->
            <input type="hidden" id="dropdown_data_type" value="">

        </div><!-- /.container-fluid -->
    </div><!-- /#main-nav -->

    <script>
        /* Autoload Messages and Notifications in the dropdown menu*/
        $(function(){
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var dropdownInProgress = false;

            $(".dropdown_data_window").scroll(function() {
                var bottomElement = $(".dropdown_data_window a").last();
                var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

                // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM
                // object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
                if (isElementInView(bottomElement[0]) && noMoreData == 'false') {
                    loadPosts();
                }
            }); //End $(".dropdown_data_window").scroll(function() {} )


            function loadPosts() {
                //If it is already in the process of loading some posts, just return
                if(dropdownInProgress) {
                    return;
                }

                dropdownInProgress = true;

                // If .nextPage couldn't be found, it must not be on the page yet
                // (it must be the first time loading posts), so use the value '1'
                var page = $('.dropdown_data_window').find('.nextPageDropdownData').val() || 1;

                var pageName; //Holds name of page to send ajax request to
                var type = $('#dropdown_data_type').val();

                if(type == 'notification'){
                    pageName = "ajax_load_notifications.php";
                }else if(type == 'message'){
                    pageName = "ajax_load_messages.php";
                }

                $.ajax({
                    url: "includes/handlers/" + pageName,
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache: false,

                    success: function(response) {

                        $('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage
                        $('.dropdown_data_window').find('.noMoreDropdownData').remove();

                        $('.dropdown_data_window').append(response);

                        dropdownInProgress = false;
                    }
                });
            }

            //Check if the element is in view
            function isElementInView (el) {
                var rect = el.getBoundingClientRect();

                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
                );
            } //End function isElementInView (el)

        }); //End $(function(){...
    </script>

    <div class="container wrapper">