<?php
include_once 'includes/header.php';
include_once('includes/form_handlers/settings_handler.php');

use FriendsCube\User;
?>

        <div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="news-feed card shadow-sm">
                        <div class="card-body settings-page clearfix">
                            <h4>Account Settings</h4>
                            <?php
                                echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pic' >";
                            ?>
                            <br>
                            <a href="upload.php">Upload new profile picture</a> <br> <br> <br>

                            <p>Modify the values and click 'Update Details'</p>

                            <?php
                                $get_user_data_query = "SELECT first_name, last_name, email FROM users WHERE username=:username";
                                $get_user_data_stmt = $con->prepare($get_user_data_query);
                                $get_user_data_stmt->execute(['username' => $userLoggedIn]);
                                $row = $get_user_data_stmt->fetch();

                                $first_name = $row['first_name'];
                                $last_name = $row['last_name'];
                                $email = $row['email'];
                            ?>

                            <form action="settings.php" method="POST" autocomplete="off">
                                <div class="form-group">
                                    First Name: <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>"> <br>
                                    Last Name: <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>"> <br>
                                    Email: <input type="text" name="email" class="form-control" value="<?php echo $email; ?>"> <br>

                                    <?php echo $message; ?>
                                </div>
                                <input type="submit" name="update_details" id="save_details" class="btn btn-info" value="Update Details"> <br>
                            </form>

                            <h4 class="mt-4">Change Password</h4>
                            <form action="settings.php" method="POST" autocomplete="off">
                                <div class="form-group">
                                    Old Password: <input type="password" name="old_password" class="form-control" autocomplete="old-password" > <br>
                                    New Password: <input type="password" name="new_password_1" class="form-control" autocomplete="new-password-1"> <br>
                                    New Password Again: <input type="password" name="new_password_2" class="form-control" autocomplete="new-password-2"> <br>

                                    <?php echo $password_message; ?>
                                </div>
                                <input type="submit" name="update_password" id="save_new_password" class="btn btn-info" value="Update Password"> <br>
                            </form>

                            <h4 class="mt-4">Close Account</h4>
                            <form action="settings.php" method="POST">
                                <input type="submit" name="close_account" id="close_account" class="btn btn-warning" value="Close Account">
                            </form>

                        </div><!-- /.card-body -->
                    </div><!-- /.news-feed -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->
        </div><!-- /#content -->
    </div><!-- /.container wrapper -->
</body>
</html>