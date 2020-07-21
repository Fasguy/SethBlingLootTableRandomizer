<?php

function Kill() {
    if (file_exists($_SESSION["file"])) unlink($_SESSION["file"]);  //Remove the file afterwards

    $_SESSION = array();                //Reset the session variables

    if (ini_get("session.use_cookies")) {               //Delete session cookies
        $params = session_get_cookie_params();          //*
        setcookie(session_name(), '', time() - 42000,   //*
            $params["path"], $params["domain"],         //*
            $params["secure"], $params["httponly"]      //*
        );                                              //*
    }                                                   //*

    ini_set('session.gc_maxlifetime', 0);   //Session Garbage collection
    ini_set('session.gc_probability', 1);   //*
    ini_set('session.gc_divisor', 1);       //*

    session_destroy();                  //Destroy session
}

?>