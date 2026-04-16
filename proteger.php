<?php
session_start();

// 1. Verificar si la sesión de administrador existe
if (!isset($_SESSION['admin'])) {
    // Si no hay sesión, mandamos al usuario al login inmediatamente
    header("Location: login.php");
    exit;
}

// 2. Mejora Pro: Evitar que el navegador guarde copias de las páginas protegidas
// Esto asegura que al dar "atrás" después de logout, no se vean datos.
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
?>