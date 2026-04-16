<?php
// Credenciales
$host = 'localhost';
$user = 'yonatan';
$pass = 'yonatan';
$db   = 'tienda3';

// Crear conexión
$conexion = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conexion->connect_error) {
    // Error con estilo para no romper la estética del dashboard
    die("<div style='color:white; background:#1e293b; padding:20px; font-family:sans-serif;'>
            <h2 style='color:#f87171;'>⚠️ Error de Conexión</h2>
            <p>No se pudo conectar a la base de datos. Verifica tus credenciales.</p>
         </div>");
}

// Configurar charset para tildes y ñ
$conexion->set_charset("utf8mb4");

// Opcional: Para que sea más fácil de usar en tus otros archivos
// definimos una variable global si fuera necesario, 
// aunque con $conexion es suficiente.
?>