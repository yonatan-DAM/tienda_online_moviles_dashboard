<?php
include '../proteger.php';
include '../conexion.php';

// Detectar conexión automáticamente
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

if (!$db) { die("Error de conexión"); }

// Capturamos el ID del pedido
$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pedido === 0) {
    header("Location: ver_pedidos.php");
    exit;
}

/**
 * 1. OBTENER INFORMACIÓN DEL PEDIDO Y CLIENTE
 * Ajustamos el WHERE para que use 'identificador' que es el que usa tu DB
 */
$sql_pedido = "SELECT p.*, c.nombre, c.email 
               FROM pedidos p 
               INNER JOIN clientes c ON p.id_cliente = c.identificador 
               WHERE p.identificador = $id_pedido";

$res_pedido = $db->query($sql_pedido);

// Si falla por el nombre de la columna, reintentamos con 'id'
if (!$res_pedido) {
    $sql_pedido = "SELECT p.*, c.nombre, c.email FROM pedidos p INNER JOIN clientes c ON p.id_cliente = c.id WHERE p.id = $id_pedido";
    $res_pedido = $db->query($sql_pedido);
}

$datos_pedido = ($res_pedido) ? $res_pedido->fetch_assoc() : null;

if (!$datos_pedido) {
    die("Error: No se encontró el pedido #$id_pedido en la base de datos.");
}

/**
 * 2. OBTENER LÍNEAS DEL PEDIDO (PRODUCTOS COMPRADOS)
 */
$sql_lineas = "SELECT lp.cantidad, prod.titulo, prod.precio, prod.imagen 
               FROM lineaspedido lp 
               INNER JOIN productos prod ON lp.producto_id = prod.identificador 
               WHERE lp.pedido_id = $id_pedido";

$lineas = $db->query($sql_lineas);
$total_pedido = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido #<?= $id_pedido ?> | Detalle Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --blue: #3b82f6; --text: #f8fafc; --border: #334155; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; }
        .sidebar { width: 250px; background: var(--card); height: 100vh; padding: 20px; border-right: 1px solid var(--border); position: fixed; }
        .main { margin-left: 280px; padding: 40px; width: calc(100% - 280px); max-width: 1000px; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-volver { color: #94a3b8; text-decoration: none; font-size: 0.9rem; }
        .btn-volver:hover { color: var(--blue); }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .card { background: var(--card); border-radius: 20px; padding: 25px; border: 1px solid var(--border); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; padding: 12px; border-bottom: 2px solid var(--border); }
        td { padding: 15px 12px; border-bottom: 1px solid var(--border); }

        .prod-img { width: 50px; height: 50px; object-fit: contain; background: var(--bg); border-radius: 8px; }
        .total-box { text-align: right; margin-top: 25px; font-size: 1.4rem; font-weight: bold; color: #fbbf24; }
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
    <div class="header-flex">
        <h1>Pedido #<?= $id_pedido ?></h1>
        <a href="ver_pedidos.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver al listado</a>
    </div>

    <div class="info-grid">
        <div class="card">
            <h3 style="color: var(--blue); margin-top:0;"><i class="fas fa-user"></i> Cliente</h3>
            <p style="margin: 5px 0;"><strong>Nombre:</strong> <?= htmlspecialchars($datos_pedido['nombre']) ?></p>
            <p style="margin: 5px 0;"><strong>Email:</strong> <?= htmlspecialchars($datos_pedido['email']) ?></p>
        </div>
        <div class="card">
            <h3 style="color: var(--blue); margin-top:0;"><i class="fas fa-calendar"></i> Detalles de venta</h3>
            <p style="margin: 5px 0;"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($datos_pedido['fecha'])) ?></p>
            <p style="margin: 5px 0;"><strong>Estado:</strong> <span style="color:#10b981;">Completado</span></p>
        </div>
    </div>

    <div class="card">
        <h3>Productos adquiridos</h3>
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $lineas->fetch_assoc()): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                    $total_pedido += $subtotal;
                ?>
                <tr>
                    <td><img src="data:image/png;base64,<?= base64_encode($item['imagen']) ?>" class="prod-img"></td>
                    <td><strong><?= htmlspecialchars($item['titulo']) ?></strong></td>
                    <td>x<?= $item['cantidad'] ?></td>
                    <td><?= number_format($item['precio'], 2) ?> €</td>
                    <td style="font-weight: bold;"><?= number_format($subtotal, 2) ?> €</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-box">
            <span style="font-size: 0.9rem; color: #94a3b8; font-weight: normal;">TOTAL PAGADO:</span>
            <?= number_format($total_pedido, 2) ?> €
        </div>
    </div>
</main>

</body>
</html>