<?php
session_start();
include 'conexion.php'; 

/** * 1. DETECCIÓN AUTOMÁTICA DE LA CONEXIÓN
 * Esto soluciona el error "Undefined variable" de tus capturas.
 */
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

if ($db === null) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>❌ Error: No se encontró la variable de conexión en conexion.php</div>");
}

$total_carrito = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Carrito | Tech Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --accent-blue: #3b82f6;
            --accent-red: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
        }

        .contenedor {
            max-width: 1000px;
            margin: auto;
        }

        .header-carrito {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-carrito h1 { font-size: 2.5rem; margin: 0; }
        .header-carrito i { color: var(--accent-blue); margin-right: 15px; }

        .card-tabla {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            color: var(--text-muted);
            padding: 15px;
            border-bottom: 1px solid #334155;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #334155;
        }

        .foto-mini {
            width: 70px;
            height: 70px;
            object-fit: contain;
            background: #0f172a;
            border-radius: 12px;
            border: 1px solid #334155;
        }

        .precio-final {
            color: #fbbf24;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .resumen-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px;
            background: rgba(0,0,0,0.2);
            border-radius: 18px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-comprar { background: var(--accent-blue); color: white; }
        .btn-comprar:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }

        .btn-vaciar { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-vaciar:hover { background: var(--accent-red); color: white; }

        .btn-volver { color: var(--text-muted); text-decoration: none; font-size: 0.9rem; }
        .btn-volver:hover { color: var(--text-main); }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="header-carrito">
        <h1><i class="fas fa-shopping-cart"></i> Tu Carrito</h1>
        <p style="color: var(--text-muted);">Revisa tus productos antes de finalizar el pedido</p>
    </div>

    <div class="card-tabla">
        <?php if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])): ?>
            <div style="text-align: center; padding: 60px;">
                <i class="fas fa-box-open fa-4x" style="color: #334155; margin-bottom: 20px;"></i>
                <h2 style="color: var(--text-muted);">El carrito está vacío</h2>
                <br>
                <a href="index.php" class="btn btn-comprar">Volver a la tienda</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Detalles</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($_SESSION['carrito'] as $id => $cantidad):
                        // Consulta segura usando la variable detectada $db
                        $stmt = $db->prepare("SELECT * FROM productos WHERE identificador = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $producto = $resultado->fetch_assoc();

                        if ($producto):
                            $subtotal = $producto['precio'] * $cantidad;
                            $total_carrito += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <img src="data:image/png;base64,<?= base64_encode($producto['imagen']) ?>" class="foto-mini">
                            </td>
                            <td>
                                <strong style="display:block;"><?= htmlspecialchars($producto['titulo']) ?></strong>
                                <small style="color: var(--text-muted);">ID: #<?= $id ?></small>
                            </td>
                            <td>
                                <span style="background: #0f172a; padding: 6px 15px; border-radius: 8px; font-weight: bold;">
                                    <?= $cantidad ?>
                                </span>
                            </td>
                            <td><?= number_format($producto['precio'], 2) ?> €</td>
                            <td class="precio-final"><?= number_format($subtotal, 2) ?> €</td>
                        </tr>
                    <?php 
                        endif;
                        $stmt->close();
                    endforeach; 
                    ?>
                </tbody>
            </table>

            <div class="resumen-footer">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.9rem;">TOTAL A PAGAR</span>
                    <div style="font-size: 2.2rem; font-weight: bold; color: #fbbf24;">
                        <?= number_format($total_carrito, 2) ?> €
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 15px; align-items: flex-end;">
                    <div style="display: flex; gap: 10px;">
                        <a href="vaciar_carrito.php" class="btn btn-vaciar" onclick="return confirm('¿Seguro que quieres vaciar el carrito?')">
                            <i class="fas fa-trash"></i> Vaciar
                        </a>
                        <a href="procesar_pedido.php" class="btn btn-comprar">
                            <i class="fas fa-check-circle"></i> Finalizar Compra
                        </a>
                    </div>
                    <a href="index.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Seguir comprando</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>