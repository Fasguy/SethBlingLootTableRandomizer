<?php

ignore_user_abort(true);                //Ensure the script keeps running even if the user leaves the page. Failsafe to remove unnecessary file.

require_once("lib/functions.php");

session_start();                        //Grab the session from before again

if(!isset($_SESSION["file"]) || !isset($_SESSION["name"]) || empty($_SESSION["file"]) || empty($_SESSION["name"])) {    //If 'file' or 'name' are not available in the current session...
    header("Location: index.php");                                                                                      //Go back to the starting page
}

header('Content-Description: File Transfer');                                       //I still do not fully understand headers, but these allow the file to be downloadable
header('Content-Type: application/zip');                                            //*
header('Content-Disposition: attachment; filename="' . $_SESSION["name"] . '"');    //*
header('Content-Transfer-Encoding: binary');                                        //*
header('Expires: 0');                                                               //*
header('Cache-Control: must-revalidate');                                           //*
header('Pragma: public');                                                           //*
header('Content-Length: ' . filesize($_SESSION["file"]));                           //*

readfile($_SESSION["file"]);            //Download the file

Kill();                                 //Failsafe to make sure no orphaned files and sessions are being left behind

?>