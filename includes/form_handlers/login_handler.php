<?php
    if(isset($_POST['login_button'])){
        $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //Remove all illegal characters from email

        $_SESSION['log_email'] = $email; // store $email in session to auto-fill form in case of error

        $password = strip_tags($_POST['log_password']); //Get user's posted password

        $get_db_data_query = "SELECT * FROM users WHERE email = :email";
        $get_db_data_stmt = $con->prepare($get_db_data_query);
        $get_db_data_stmt->execute(array('email' => $email));
        $possible_user_data = $get_db_data_stmt->fetch();

        // if we got the data from the DB
        if($possible_user_data){
            $username = $possible_user_data['username']; //Get username from the DB
            $hpass = $possible_user_data['password'];
            //first we verify stored hash against plain-text password
            if ( password_verify($password, $hpass) === true ) {
                // verify legacy password to new password_hash options
                if ( password_needs_rehash($hpass, PASSWORD_DEFAULT, ['cost' => 12]) === true ) {
                    // rehash/store plain-text password using new hash
                    $newHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                }

                // update DB if pass has been rehashed or user had closed account
                if ( isset($newHash) && $possible_user_data['user_closed'] === 'yes'){
                    // update password AND user_closed
                    $update_pass_n_user_closed_query = "UPDATE users SET password=:newhpass, user_closed=:user_closed WHERE email= :email";
                    $update_pass_n_user_closed = $con->prepare($update_pass_n_user_closed_query)->execute(array(
                        'newhpass'    => $newHash,
                        'user_closed' => 'no',
                        'email'       => $email
                    ));
                    $update_pass_n_user_closed_done = $update_pass_n_user_closed->rowCount() ? true : false;
                }else{
                    // Update password
                    if( isset($newHash) ){
                        $update_pass_query = "UPDATE users SET password=:newhpass WHERE email= :email";
                        $update_pass = $con->prepare($update_pass_query)->execute(array(
                                'newhpass'    => $newHash,
                                'email'       => $email
                        ));
                        $update_pass_done = $update_pass->rowCount() ? true : false;
                    }
                    // Update user_closed option
                    if($possible_user_data['user_closed'] === 'yes'){
                        $update_user_closed_query = "UPDATE users SET user_closed=:user_closed WHERE email= :email";
                        $update_user_closed = $con->prepare($update_user_closed_query)->execute(array(
                            'user_closed' => 'no',
                            'email'       => $email
                        ));
                        $update_user_closed_done = $update_user_closed->rowCount() ? true : false;
                    }

                }

                //Clear $_SESSION variables
                $_SESSION['log_email'] = "";

                $_SESSION['username'] = $username; //Store username in the session
                header("Location: index.php"); //Redirect user to index.php
                exit(); //Stop further execution
            }else {
                array_push($errors_array, "Email or password was incorrect<br>");
            }
        }else {
            array_push($errors_array, "Email or password was incorrect<br>");
        }
    }
?>