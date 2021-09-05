<?php
require_once("connection.php");

ob_start();
session_start();

if (!isset($_SESSION['id']) or ($_SESSION['nivel'] > 1)) {
    header("Location: error.php");
    exit();
}

if (isset($_REQUEST['exit'])) {
    session_destroy();
    session_unset($_SESSION['id']);
    header("Location: index.php");
}
