<?php
    session_start();

    if(isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        if(!empty($lang)) {
            $_SESSION['lang'] = $lang;
        }
    }

    if(isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
        require $lang.'.php';
    }
    else {
        require 'pt.php';
    }
?>