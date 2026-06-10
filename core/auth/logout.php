<?php
session_start();

// Destruir sesión
session_unset();
session_destroy();

// Redirigir al inicio
header("Location: ../../index.php");
exit;
?>