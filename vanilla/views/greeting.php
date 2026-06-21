<?php
    date_default_timezone_set('America/Sao_Paulo');
    $hr = date("H");
    if ($hr >= 12 && $hr < 18) {
        $resp = $hello . $_SESSION['nome'] . ', ' . $afternoon;
    } else if ($hr >= 5 && $hr < 12) {
        $resp = $hello . $_SESSION['nome'] . ', ' . $morning;
    } else {
        $resp = $hello . $_SESSION['nome'] . ', ' . $night;
    }
    echo "$resp";
?>