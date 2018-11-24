<?php
    require 'config/config.php'; //Require our config
    require 'includes/form_handlers/login_handler.php'; //Require login form handler
    require 'includes/form_handlers/register_handler.php'; //Require register form handler
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Join into FriendsCube</title>

    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>
<body>
    <?php
        if(isset($_POST['register_button'])){
            echo '
                <script>
                    $(document).ready(function(){
                        $("#signin-form-wrap").hide();
                        $("#signup-form-wrap").show();
                    });
                </script>
            ';
        }
    ?>

    <div class="wrapper">
        <div class="login_box">
            <div class="login_header">
                <h1>FriendsCube</h1>
                Login or sign up below!
            </div><!-- /.login_header -->
            <?php  if (count($errors_array) > 0) : ?>
            <div class="notice error">
                <em>Please fix the following errors:</em>
                <?php foreach ($errors_array as $error) : ?>
                <p>▸ <?php echo $error ?></p>
                <?php endforeach ?>
            </div><!-- /.error -->
            <?php  endif ?>
            <div id="signin-form-wrap">
                <form action="register.php" method="POST" autocomplete="off">
                    <input type="email" name="log_email" placeholder="Email Address"
                        value="<?php echo ( isset($_SESSION['log_email']) ) ? $_SESSION['log_email'] : ''; ?>"
                        required autocomplete="off">
                    <input type="password" name="log_password" placeholder="Password" autocomplete="new-password">
                    <input type="submit" name="login_button" value="Login">
                    <br>
                    <a href="#" id="signup" class="signup">Need an account? Register here!</a>

                </form>
            </div><!-- /#signin-form-wrap -->
            <div id="signup-form-wrap">
                <form action="register.php" method="POST" autocomplete="off">
                    <input type="text" name="reg_fname" placeholder="First Name"
                        value="<?php echo ( isset($_SESSION['reg_fname']) ) ? $_SESSION['reg_fname'] : ''; ?>"
                        required autocomplete="off">
                    <input type="text" name="reg_lname" placeholder="Last Name"
                        value="<?php echo ( isset($_SESSION['reg_lname']) ) ? $_SESSION['reg_lname'] : ''; ?>"
                        required autocomplete="off">
                    <input type="email" name="reg_email" placeholder="Email"
                        value="<?php echo ( isset($_SESSION['reg_email']) ) ? $_SESSION['reg_email'] : ''; ?>"
                        required autocomplete="off">
                    <input type="email" name="reg_email2" placeholder="Confirm Email"
                        value="<?php echo ( isset($_SESSION['reg_email2']) ) ? $_SESSION['reg_email2'] : ''; ?>"
                        required autocomplete="off">
                    <input type="password" name="reg_password" placeholder="Password" required autocomplete="off">
                    <input type="password" name="reg_password2" placeholder="Confirm Password" required autocomplete="off">
                    <input type="submit" name="register_button" value="Register">
                    <br>
                    <?php if (isset($_SESSION['success'])) : ?>
                    <div class="notice success" >
                        <h3>
                        <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                        ?>
                        </h3>
                    </div>
                    <?php endif ?>
                    <br>
                    <a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>
                </form>
            </div><!-- /#signin-form-wrap -->
        </div><!-- /.login_box -->
    </div><!-- /.wrapper -->
</body>
</html>