<?php 
    session_name("singedcats");
	session_start();
    unset($_SESSION["singedcats_username"]);
    unset($_SESSION["singedcats_userID"]);
    unset($_SESSION["singedcats_fname"]);
    unset($_SESSION["singedcats_loginTime"]);
    unset($_SESSION["singedcats_loggedIn"]);
    session_destroy();
    header("Location: login.php");
?>