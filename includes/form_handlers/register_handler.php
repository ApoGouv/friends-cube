<?php
    //Declaring variables to prevent errors
    $fname = ""; //First name
    $lname = "";//Last name
    $em = ""; //email
    $em2 = ""; //email2
    $password = ""; //password
    $password2 = ""; //password2
    $date = ""; //Sign up date

    //Check if register form is submitted
    if( isset( $_POST['register_button'] ) ){

        //Registration form values

        //First name
        $fname = strip_tags($_POST['reg_fname']);
        $fname = str_replace(' ', '', $fname); //remove spaces
        $fname = ucfirst( strtolower($fname) ); //Uppercase first letter
        //$fname = ucfirst( strtolower( str_replace(' ', '', strip_tags($_POST['reg_fname'])) ) );
        $_SESSION['reg_fname'] = $fname; //Stores first name into session variable

        //Last name
        $lname = strip_tags($_POST['reg_lname']);
        $lname = str_replace(' ', '', $lname); //remove spaces
        $lname = ucfirst( strtolower($lname) ); //Uppercase first letter
        $_SESSION['reg_lname'] = $lname; //Stores last name into session variable

        //Email
        $em = strip_tags($_POST['reg_email']);
        $em = str_replace(' ', '', $em); //remove spaces
        $em = strtolower($em); //lowercase letters only
        $_SESSION['reg_email'] = $em; //Stores email into session variable

        //Email2
        $em2 = strip_tags($_POST['reg_email2']);
        $em2 = str_replace(' ', '', $em2); //remove spaces
        $em2 = strtolower($em2); //lowercase letters only
        $_SESSION['reg_email2'] = $em2; //Stores first name into session variable

        //Password
        $password = strip_tags( $_POST['reg_password']);

        //Password2
        $password2 = strip_tags($_POST['reg_password2']);

        //Date
        $date = date("Y-m-d"); //Current date

        if($em == $em2){
            //Check if email is in valid format
            if( filter_var($em, FILTER_VALIDATE_EMAIL) ){
                $em = filter_var($em, FILTER_VALIDATE_EMAIL);

                //Check if email already exists
                $email_exist_check_query = "SELECT 1 FROM users WHERE email = :email";
                $email_exist_stmt = $con->prepare($email_exist_check_query);
                $email_exist_stmt->execute(['email' => $em]);
                $count_email_exist = $email_exist_stmt->fetchColumn();

                //count the number of rows returned
                if( $count_email_exist > 0 ){
                    $errors_array[] = 'Email already in use.<br>';
                }

            }else {
                $errors_array[] = 'Invalid Email format.<br>';
            }

        }else {
            $errors_array[] = 'Emails do not match.<br>';
        }

        if( strlen($fname) > 25 || strlen($fname) < 2 ){
            $errors_array[] = 'Your first name must be between 2 and 25 characters.<br>';
        }

        if( strlen($lname) > 25 || strlen($lname) < 2 ){
            $errors_array[] = 'Your last name must be between 2 and 25 characters.<br>';
        }

        if($password != $password2){
            $errors_array[] = 'Your passwords do not match.<br>';
        }else {
            if ( preg_match('/[^A-Za-z0-9]/', $password) ){
                $errors_array[] = 'Your passwords can only contain english characters or numbers.<br>';
            }
        }

        if(strlen($password) > 30 || strlen($password) < 5){
            $errors_array[] = 'Your password must be between 5 and 30 characters.<br>';
        }

        if(empty($errors_array)) {
            // Hash password before sending to database
            $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
            //Generate username by concatenating first and last names
            $username = strtolower($fname . "_" . $lname);

            // Check DB if username exists
            $check_username_query = "SELECT username FROM users WHERE username = :username";
            $stmt_username = $con->prepare($check_username_query);
            $stmt_username->execute(['username' => $username]);

            $i = 0;
            while ($stmt_username->fetch()) {
                //username exists, so add _# and try again
                $i++; //Add 1 to $i
                $username_changed = $username . "_" . $i;
                $stmt_username->execute(['username' => $username_changed]);
            }
            $username = isset($username_changed) ? $username_changed : $username;

            //Profile picture random assignment Method #1
            /*
            $rand = rand(1,16); //Random number between 1 and 16
            if ($rand == 1)
                $profile_pic = "assets/images/profile_pics/defaults/head_alizarin.png";
            else if($rand == 2)
                $profile_pic = "assets/images/profile_pics/defaults/head_amethyst.png";
            ...
            */

            /**
             * Suppose, you are browsing in your localhost
             * http://localhost/myproject/index.php?id=8
             * From: https://stackoverflow.com/a/37071036
             */
            function getBaseUrl() {
                // output: /myproject/index.php
                $currentPath = $_SERVER['PHP_SELF'];

                // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
                $pathInfo = pathinfo($currentPath);

                // output: localhost
                $hostName = $_SERVER['HTTP_HOST'];

                // output: http://
                $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';

                // return: http://localhost/myproject/
                return $protocol.'://'.$hostName.$pathInfo['dirname']."/";
            }

            //Profile picture random assignment Method #2
            $default_profile_pics_folder = "assets/images/profile_pics/defaults/";
            $profile_pics = glob( $default_profile_pics_folder . "head_". "*.png" );
            $profile_pic = getBaseUrl().$profile_pics[array_rand($profile_pics)];

            $user_data = [
                'fname' => $fname,
                'lname' => $lname,
                'username' => $username,
                'em' => $em,
                'password' => $password,
                'date' => $date,
                'profile_pic' => $profile_pic,
                'num_posts' => 0,
                'num_likes' => 0,
                'user_closed' => 'no',
                'friend_array' => ','
            ];
            //Insert user to the DB
            $insert_user_query = "INSERT INTO users VALUES ('', :fname, :lname, :username, :em, :password, :date, :profile_pic, :num_posts, :num_likes, :user_closed, :friend_array)";
            $con->prepare($insert_user_query)->execute($user_data);

            $_SESSION['success'] = "You're all set! Go ahead and login!";

            //Clear $_SESSION variables
            $_SESSION['reg_fname'] = "";
            $_SESSION['reg_lname'] = "";
            $_SESSION['reg_email'] = "";
            $_SESSION['reg_email2'] = "";
        }

    }//END of "Check if register form is submitted"
?>