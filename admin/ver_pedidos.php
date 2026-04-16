<?php
include '../proteger.php';
include '../conexion.php';

// Detectar conexión automáticamente ($conexion o $conexion_a_sql)
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

if (!$db) {
    die("Error crítico: No se encuentra la conexión.");
}

/**
 * CONSULTA DE PEDIDOS
 * Ajustada: 'identificador' en lugar de 'id' para coincidir con tu estructura de tienda3
 */
$sql = "SELECT p.identificador, p.fecha, c.nombre as cliente 
        FROM pedidos p 
        LEFT JOIN clientes c ON p.id_cliente = c.identificador 
        ORDER BY p.fecha DESC";

$resultado = $db->query($sql);

if (!$resultado) {
    // Si falla por 'identificador', intentamos con 'id' por si acaso
    $sql = "SELECT p.id, p.fecha, c.nombre as cliente FROM pedidos p LEFT JOIN clientes c ON p.id_cliente = c.id ORDER BY p.fecha DESC";
    $resultado = $db->query($sql);
    
    if (!$resultado) {
        die("Error en la consulta: " . $db->error);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --blue: #3b82f6;
            --text: #f8fafc;
            --border: #334155;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
        }

        /* Sidebar similar al panel_admin */
        .sidebar { width: 250px; background: var(--card); height: 100vh; padding: 20px; border-right: 1px solid var(--border); position: fixed; }
        .main { margin-left: 280px; padding: 40px; width: calc(100% - 280px); }

        .card-tabla {
            background: var(--card);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
        }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; padding: 15px; border-bottom: 2px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); }
        
        .badge-pedido { background: #0f172a; color: var(--blue); padding: 5px 10px; border-radius: 8px; font-family: monospace; font-weight: bold; }
        
        .btn-ver {
            background: rgba(59, 130, 246, 0.1);
            color: var(--blue);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: 0.3s;
            border: 1px solid transparent;
        }

        .btn-ver:hover { background: var(--blue); color: white; }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2 style="color: var(--blue);">Admin Tech</h2>
    <nav>
        <p><a href="panel_admin.php" style="color:white; text-decoration:none;"><i class="fas fa-chart-line"></i> Dashboard</a></p>
        <p><a href="ver_productos.php" style="color:white; text-decoration:none;"><i class="fas fa-box"></i> Productos</a></p>
        <p><a href="ver_pedidos.php" style="color:var(--blue); text-decoration:none;"><i class="fas fa-receipt"></i> Pedidos</a></p>
        <p><a href="../logout.php" style="color:#f87171; text-decoration:none;"><i class="fas fa-sign-out-alt"></i> Salir</a></p>
    </nav>
</aside>

<main class="main">
    <h1 style="margin-bottom: 10px;"><i class="fas fa-receipt" style="color: var(--blue);"></i> Historial de Ventas</h1>
    <p style="color: #94a3b8; margin-bottom: 30px;">Listado de todos los pedidos realizados por los clientes.</p>

    <div class="card-tabla">
        <?php if ($resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nº Pedido</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $resultado->fetch_assoc()): 
                        // Verificamos si la columna se llama id o identificador para mostrarla
                        $id_actual = isset($row['identificador']) ? $row['identificador'] : $row['id'];
                    ?>
                    <tr>
                        <td><span class="badge-pedido">#<?= $id_actual ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['fecha'])) ?></td>
                        <td><strong><?= htmlspecialchars($row['cliente']) ?></strong></td>
                        <td>
                            <a href="detalle_pedido.php?id=<?= $id_actual ?>" class="btn-ver">
                                <i class="fas fa-eye"></i> Detalles
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                <i class="fas fa-shopping-basket fa-3x" style="margin-bottom: 20px; opacity: 0.5;"></i>
                <p>Aún no se ha realizado ninguna venta.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>