<?php
include("connection.php");

ob_start();
session_start();

if (!isset($_SESSION['id']) or ($_SESSION['nivel'] < 2)) {
    header("Location: error.php");
    exit();
}

if (isset($_REQUEST['exit'])) {
    session_destroy();
    session_unset($_SESSION['uuid']);
    header("Location: index.php");
}
