<?php
include '../proteger.php';
include '../conexion.php'; 

$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

// IMPORTANTE: WHERE activo = 1
$sql = "SELECT identificador, titulo, precio FROM productos WHERE activo = 1 ORDER BY identificador DESC";
$resultado = $db->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #0f172a; color: white; font-family: sans-serif; padding: 40px; }
        .contenedor { max-width: 900px; margin: auto; }
        .card { background: #1e293b; padding: 25px; border-radius: 20px; border: 1px solid #334155; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #334155; }
        .btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; }
        .edit { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .delete { background: rgba(239,68,68,0.1); color: #f87171; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Inventario Activo</h1>
            <a href="panel_admin.php" style="color:#94a3b8;">Volver</a>
        </div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $p['identificador'] ?></td>
                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                        <td style="color:#fbbf24;"><?= number_format($p['precio'], 2) ?> €</td>
                        <td>
                            <a href="editar_productos.php?id=<?= $p['identificador'] ?>" class="btn edit">Editar</a>
                            <a href="eliminar_productos.php?id=<?= $p['identificador'] ?>" class="btn delete" onclick="return confirm('¿Borrar producto?')">Borrar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>