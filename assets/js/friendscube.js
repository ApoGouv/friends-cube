$(document).ready(function(){

    //Search form
    $('#search_text_input').focus(function(){
        if(window.matchMedia( "(min-width: 800px)" ).matches){
            $(this).animate({width: '250px'}, 500);
        }
    });
    // $('#search_text_input').focusout(function(){
    //     if(window.matchMedia( "(min-width: 800px)" ).matches){
    //         $(this).animate({width: '160px'}, 500);
    //     }
    // });

    $('.button_holder').on('click', function(){
        document.search_form.submit();
    });

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

$(document).click(function(e){

    if( e.target.class !== "search_results" && e.target.id !== "search_text_input" ){
        $(".search_results").html("");
        $('.search_results_footer').html("");
        $(".search_results_footer").toggleClass("search_results_footer_empty");
        $(".search_results_footer").toggleClass("search_results_footer");
    }

    if( e.target.class !== "dropdown_data_window" ){
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
    }

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


/* Get live users search result*/
function getLiveSearchUsers(value, user){

    $.post("includes/handlers/ajax_search.php",
        {query:value, userLoggedIn: user},
        function(data){
            if( $(".search_results_footer_empty")[0] ){
                $(".search_results_footer_empty").toggleClass("search_results_footer");
                $(".search_results_footer_empty").toggleClass("search_results_footer_empty");
            }

            $('.search_results').html(data);
            $('.search_results_footer').html("<a href='search.php?q=" + value +"'>See All Results</a>");

            if(data == "" || value == ""){
                $('.search_results_footer').html("");
                $(".search_results_footer").toggleClass("search_results_footer_empty");
                $(".search_results_footer").toggleClass("search_results_footer");
            }

        }
    );

}