<?php

if( isset($_POST['update_details']) ){

    $first_name = ucfirst( strtolower( str_replace(' ', '', strip_tags( $_POST['first_name'] ) ) ) );
    $last_name = ucfirst( strtolower( str_replace(' ', '', strip_tags( $_POST['last_name'] ) ) ) );
    $email = strtolower( str_replace(' ', '', strip_tags( $_POST['email'] ) ) );

    $email_check_query = "SELECT * FROM users WHERE email = :email";
    $email_check_stmt = $con->prepare($email_check_query);
    $email_check_stmt->execute(['email' => $email]);
    $matched_user_data = $email_check_stmt->fetch();
    $matched_user = $matched_user_data['username'];

    if( $matched_user === "" || $matched_user === $userLoggedIn ){

        $update_user_details_query = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email WHERE username=:username";
        $update_user_details_stmt = $con->prepare($update_user_details_query);
        $update_user_details_stmt->execute([
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'username'   => $userLoggedIn,
        ]);

        $message = "<br><span class='alert alert-success'>Details updated!</span><br><br>";
    }else {
        $message = "<br><span class='alert alert-warning'>That email is already in use!</span><br><br>";
    }

}else {
    $message = "";
}

// ********************************************************

if( isset($_POST['update_password']) ) {
    $old_password = strip_tags($_POST['old_password']);
    $new_password_1 = strip_tags($_POST['new_password_1']);
    $new_password_2 = strip_tags($_POST['new_password_2']);

    $get_user_pass_query = "SELECT password FROM users WHERE username = :username";
    $get_user_pass_stmt = $con->prepare($get_user_pass_query);
    $get_user_pass_stmt->execute(['username' => $userLoggedIn]);
    $row = $get_user_pass_stmt->fetch();
    $db_password = $row['password'];

    //check if entered 'old_password' is the same as the one in the db
    if ( password_verify($old_password, $db_password) === true ) {

        //check if new_password_1 is the same as new_password_2
        if( $new_password_1 === $new_password_2 ){

            if(strlen($new_password_1) > 30 || strlen($new_password_1) < 5){
                $password_message = "<br><span class='alert alert-warning'>Your password must be between 5 and 30 characters.</span><br><br>";
            }else {
                $new_password_hashed = password_hash($new_password_1, PASSWORD_DEFAULT, ['cost' => 12]);

                $update_user_password_query = "UPDATE users SET password=:password WHERE username=:username";
                $update_user_password_stmt = $con->prepare($update_user_password_query);
                $update_user_password_stmt->execute([
                    'password' => $new_password_hashed,
                    'username' => $userLoggedIn,
                ]);

                $password_message = "<br><span class='alert alert-success'>Password has been changed!</span><br><br>";
            }

        }else {
            $password_message = "<br><span class='alert alert-warning'>Your two new passwords need to match!</span><br><br>";
        }
    }else {
        $password_message = "<br><span class='alert alert-warning'>The old password is incorrect!</span><br><br>";
    }
}else {
    $password_message = "";
}

// ********************************************************

if( isset($_POST['close_account']) ) {
    header("Location: close_account.php");
}