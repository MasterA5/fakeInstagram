<?php

// Funcion para saber cuanto timepo a pasado desde la creacion del post de manera mas agradable para el usuario
// NOTE: Funcion Extraida Desde Stackoverflow
// No Timestamp (yyyy-mm-dd - hh-mm-ss) o (año-mes-dia - hora-minutos-segundos) EJ: (2026-04-19 - 10-02-40)
// - Params:
// -- $datetime -> variable del timestamp para poder trabajar el parseo de la fecha
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return "hace $diff seg";
    if ($diff < 3600) return "hace " . floor($diff / 60) . " min";
    if ($diff < 86400) return "hace " . floor($diff / 3600) . " h";
    if ($diff < 172800) return "ayer";
    if ($diff < 2592000) return "hace " . floor($diff / 86400) . " días";
    if ($diff < 31536000) return "hace " . floor($diff / 2592000) . " meses";

    return "hace " . floor($diff / 31536000) . " años";
}

?>