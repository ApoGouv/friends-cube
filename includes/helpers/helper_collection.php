<?php
    namespace FriendsCube\Helpers\FunctionLib;

    if (!function_exists('dateDiffToString')) {
        function dateDiffToString(\DateTime $start_date, \DateTime $end_date){
            $time_message = '';
            $interval = $start_date->diff($end_date); //Difference between dates
            /*
             * $interval->y |Number of years.
             * $interval->m |Number of months.
             * $interval->d |Number of days.
             * $interval->h |Number of hours.
             * $interval->i |Number of minutes.
             * $interval->s |Number of seconds.
             * */
            if( $interval->y >= 1 ){
                if($interval == 1){
                    $time_message = $interval->y . " year ago"; //1 year ago
                }else {
                    $time_message = $interval->y ." years ago"; //1+ years ago
                }
            }else if ( $interval->m >= 1 ){
                if( $interval->d == 0 ){
                    $days = " ago";
                }else if( $interval->d == 1 ){
                    $days = $interval->d . " day ago";
                }else {
                    $days = $interval->d ." days ago";
                }

                if( $interval->m == 1 ){
                    $time_message = $interval->m . " month". $days;
                }else{
                    $time_message = $interval->m . " months". $days;
                }
            }else if ( $interval->d >= 1 ){
                if( $interval->d == 1 ){
                    $time_message = "Yesterday";
                }else {
                    $time_message = $interval->d ." days ago";
                }
            }else if ( $interval->h >= 1 ){
                if( $interval->h == 1 ){
                    $time_message = $interval->h . " hour ago";
                }else {
                    $time_message = $interval->h ." hours ago";
                }
            }else if ( $interval->i >= 1 ){
                if( $interval->i == 1 ){
                    $time_message = $interval->i . " minute ago";
                }else {
                    $time_message = $interval->i ." minutes ago";
                }
            }else if ( $interval->s <= 30 ){
                if( $interval->h == 1 ){
                    $time_message = "Just now";
                }else {
                    $time_message = $interval->s ." seconds ago";
                }
            }
            return $time_message;
        }
    }