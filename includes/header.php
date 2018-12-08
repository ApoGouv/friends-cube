<?php
    require 'config/config.php'; //Require our config
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");

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
            <nav class="navbar navbar-expand-lg navbar-expand-md navbar-dark bg-dark">
                <a class="navbar-brand" href="index.php">FriendsCube!</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavContent" aria-controls="mainNavContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
                <div class="justify-content-end collapse navbar-collapse" id="mainNavContent">
                    <ul class="navbar-nav main-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $userLoggedIn; ?>"><?php echo $user['first_name']; ?></a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="index.php"><i class="fas fa-home fa-lg"></i> <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-envelope fa-lg"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-bell fa-lg"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="requests.php"><i class="fas fa-users fa-lg"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-cog fa-lg"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt fa-lg"></i></a>
                        </li>
                    </ul><!-- /.navbar-nav.main-menu-->
                </div><!-- /.collapse.navbar-collapse -->
            </nav><!-- /.navbar.navbar-dark -->
        </div><!-- /.container-fluid -->
    </div><!-- /#main-nav -->

    <div class="container wrapper">