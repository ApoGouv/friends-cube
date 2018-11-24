<?php
    include("../../config/config.php");

    if( isset( $_GET['post_id'] ) ){
        $post_id = $_GET['post_id'];
    }

    if( isset( $_POST['result'] ) ){
        $post_deleted = 'yes';
        if( $_POST['result'] == 'true' ){
            //Update deleted post
            $update_deleted_post_query = "UPDATE posts SET deleted=? WHERE id=?";
            $update_deleted_post_stmt = $con->prepare($update_deleted_post_query);
            $update_deleted_post_stmt->execute(array(
                    $post_deleted,
                    $post_id
                ));
        }
    }

?>