<?php
include '../proteger.php';
include '../conexion.php';

// Detectar conexión automáticamente
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$exito = false;

if ($id > 0) {
    // IMPORTANTE: Marcamos como inactivo para proteger el historial de pedidos
    $stmt = $db->prepare("UPDATE productos SET activo = 0 WHERE identificador = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $exito = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminando Producto...</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #0f172a; color: white; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #1e293b; padding: 40px; border-radius: 24px; text-align: center; border: 1px solid #334155; box-shadow: 0 20px 50px rgba(0,0,0,0.5); max-width: 350px; width: 90%; }
        .icon { font-size: 4rem; color: #f87171; margin-bottom: 20px; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        .loader-bar { width: 100%; height: 4px; background: #0f172a; border-radius: 10px; overflow: hidden; margin-top: 20px; }
        .progress { width: 0%; height: 100%; background: #f87171; animation: load 1.5s forwards; }
        @keyframes load { to { width: 100%; } }
    </style>
    <meta http-equiv="refresh" content="1.5;url=ver_productos.php">
</head>
<body>
    <div class="card">
        <div class="icon"><i class="fas fa-trash-alt"></i></div>
        <h2>Eliminando Producto</h2>
        <p>Actualizando base de datos...</p>
        <div class="loader-bar"><div class="progress"></div></div>
    </div>
</body>
</html>