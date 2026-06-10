<?php

if (!function_exists('timeAgo')) {
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return "hace $diff seg";
    if ($diff < 3600) return "hace " . floor($diff / 60) . " min";
    if ($diff < 86400) return "hace " . floor($diff / 3600) . " h";
    if ($diff < 86400 * 2) return "ayer";
    if ($diff < 2592000) {
        $days = floor($diff / 86400);
        return "hace " . $days . ($days == 1 ? " día" : " días");
    }
    if ($diff < 31536000) return "hace " . floor($diff / 2592000) . " meses";

    $years = floor($diff / 31536000);
    return "hace " . $years . ($years == 1 ? " año" : " años");
}
}

?>