$(document).ready(function(){

    //Get form wrappers
    $signinFormWrap = $('#signin-form-wrap');
    $signupFormWrap = $('#signup-form-wrap');

    //On click signup, hide login and show registration form
    $('#signup').click(function(){
        $signinFormWrap.slideUp('slow', function(){
            $signupFormWrap.slideDown('slow');
        });
    });

    //On click signin, hide registration and show login form
    $('#signin').click(function(){
        $signupFormWrap.slideUp('slow', function(){
            $signinFormWrap.slideDown('slow');
        });
    });
})