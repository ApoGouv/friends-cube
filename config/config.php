<?php
    ob_start(); //Turns on output buffering
    session_start();

    $timezone = date_default_timezone_set("Europe/Athens");

    //Connect to DB with PDO
    $host = '127.0.0.1';
    $db   = 'friends_cube_db';
    $user = 'root';
    $pass = 'mysql';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
    ];
    try{
        $con = new PDO($dsn, $user, $pass, $options);
    } catch(\PDOException $e){
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    //Declare common variables
    $errors_array = []; //Holds error messages
?>