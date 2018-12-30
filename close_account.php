<?php
    include_once("includes/header.php");

    if( isset($_POST['cancel']) ) {
        header("Location: settings.php");
    }

    if( isset($_POST['close_account']) ) {
        $close_user_query = "UPDATE users SET user_closed=:user_closed WHERE username=:username";
        $close_user_stmt = $con->prepare($close_user_query);
        $close_user_stmt->execute([
            'user_closed' => 'yes',
            'username'    => $userLoggedIn,
        ]);

        session_destroy();

        header("Location: register.php");
    }
?>

<div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="news-feed card shadow-sm">
                        <div class="card-body settings-page">
                            <h4>Close Account</h4>
                            <p>Are you sure you want to close your account?</p>
                            <p>Closing your account will hide your profile and all your activity from other users!</p>
                            <p>You can re-open your account at any time by simply logging in.</p>

                            <form action="close_account.php" method="POST">
                                <input type="submit"
                                    name="close_account"
                                    id="close_account"
                                    class="btn btn-danger"
                                    value="Yes! Close it!">
                                <input type="submit"
                                    name="cancel"
                                    id="cancel_close_account"
                                    class="btn btn-secondary"
                                    value="No way!">
                            </form>

                        </div><!-- /.card-body -->
                    </div><!-- /.news-feed -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->
        </div><!-- /#content -->
    </div><!-- /.container wrapper -->
</body>
</html>