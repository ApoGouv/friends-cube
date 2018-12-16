$(document).ready(function(){

    //Button for profile post
    $('#submit_profile_post').on('click', function() {
        $.ajax({
            type: "POST",
            url: "includes/handlers/ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: function(msg) {
                $('#post_form').modal('hide');
                location.reload();
            },
            error: function() {
                alert('Oopps! Something went wrong! Please, try again!');
            }
        });
    });

});

/**
 * getUsers(value, user)
 * @param {*} value: the input value, the current user is typing
 * @param {*} user: the logged-in user
 */
function getUsers(value, user){
    $.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data){
        $(".results").html(data);
    });
}

/**
 * getDropdownData(user, type)
 * @param {*} user: the username
 * @param {*} type: the type of data we want to load
 */
function getDropdownData(user, type){
    if( $(".dropdown_data_window").css("height") == "0px" ){// If is closed
        var pageName;

        if( type === 'notification' ){
            pageName = "ajax_load_notifications.php";
            $("span").remove("#unread_notification");
        }else if( type === 'message' ){
            pageName = "ajax_load_messages.php";
            $("span").remove("#unread_message");
        }

        var ajaxReq = $.ajax({
            url: "includes/handlers/" + pageName,
            type: "POST",
            data: "page=1&userLoggedIn=" + user,
            cache: false,

            success: function(response){
                $(".dropdown_data_window").html(response);
                $(".dropdown_data_window").css({
                    "padding": "0px",
                    "height" : "205px",
                    "border" : "1px solid #dadada"
                });
                $("#dropdown_data_type").val(type);
            }
        });
    }else {// If is opened
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({
            "padding" : "0px",
            "height"  : "0px",
            "border" : "none"
        });
    }
}
