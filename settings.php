<?php
include_once 'includes/header.php';
//include_once('includes/handlers/settings_handler.php');

use FriendsCube\User;
?>

        <div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="news-feed card shadow-sm">
                        <div class="card-body settings-page">
                            <h4>Account Settings</h4>
                            <?php
echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pics' >";
?>
<br>
<a href="upload.php">Upload new profile picture</a> <br> <br>
                        </div><!-- /.card-body -->
                    </div><!-- /.news-feed -->
                </div><!-- /.col-12 -->
            </div><!-- /.row -->
        </div><!-- /#content -->
    </div><!-- /.container wrapper -->
</body>
</html>