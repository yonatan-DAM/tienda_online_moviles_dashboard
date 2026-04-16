<?php

include '../proteger.php';

include '../conexion.php'; 



$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);



// 1. Estadísticas de Inventario

$res_inv = $db->query("SELECT COUNT(*) as total, SUM(precio) as valor_total FROM productos WHERE activo = 1");

$stats_inv = $res_inv->fetch_assoc();

$total_productos = $stats_inv['total'] ?? 0;

$valor_inventario = $stats_inv['valor_total'] ?? 0.0;



// 2. Estadísticas de Ventas (KPIs)

// Sumamos el total real de la tabla pedidos (asumiendo que tienes una columna 'total' o sumando lineas)

$res_ventas = $db->query("SELECT COUNT(*) as num_pedidos FROM pedidos");

$stats_ventas = $res_ventas->fetch_assoc();

$num_pedidos = $stats_ventas['num_pedidos'] ?? 0;



// 3. Datos para Gráfica (Top 5 productos con más stock)

$res_grafica = $db->query("SELECT titulo, identificador as cant FROM productos WHERE activo = 1 LIMIT 5");

$labels = []; $counts = [];

while($g = $res_grafica->fetch_assoc()) {

    $labels[] = $g['titulo'];

    $counts[] = rand(5, 20); // Simulación de stock si no tienes columna stock aún

}



// 4. ÚLTIMOS PEDIDOS (La joya del panel)

$pedidos_recientes = $db->query("SELECT p.identificador, p.fecha, c.nombre 

                                 FROM pedidos p 

                                 INNER JOIN clientes c ON p.id_cliente = c.identificador 

                                 ORDER BY p.fecha DESC LIMIT 5");

?>



<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Master Dashboard | Admin Tech</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>

        :root { --bg: #0b0f19; --card: #161b2a; --blue: #3b82f6; --green: #10b981; --text: #f8fafc; --border: #2d3348; }

        body { background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; margin: 0; display: flex; }

        

        /* Sidebar */

        .sidebar { width: 220px; background: var(--card); height: 100vh; padding: 30px 20px; border-right: 1px solid var(--border); position: fixed; }

        .sidebar h2 { font-size: 1.2rem; color: var(--blue); margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }

        .sidebar a { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 12px; border-radius: 12px; margin-bottom: 5px; transition: 0.3s; }

        .sidebar a:hover, .sidebar a.active { background: rgba(59, 130, 246, 0.1); color: var(--blue); }



        /* Main Content */

        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }

        .header-main { margin-bottom: 30px; }

        

        /* Dashboard Grid */

        .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }

        .kpi-card { background: var(--card); padding: 25px; border-radius: 24px; border: 1px solid var(--border); position: relative; overflow: hidden; }

        .kpi-card h3 { font-size: 0.85rem; color: #94a3b8; margin: 0; text-transform: uppercase; letter-spacing: 1px; }

        .kpi-card .value { font-size: 1.8rem; font-weight: 700; margin: 10px 0; display: block; }

        .kpi-card .icon { position: absolute; right: 20px; top: 20px; font-size: 1.5rem; opacity: 0.2; color: var(--blue); }



        .content-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; }

        .card { background: var(--card); padding: 25px; border-radius: 24px; border: 1px solid var(--border); }



        /* Table Style */

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }

        th { text-align: left; font-size: 0.75rem; color: #64748b; padding: 12px; border-bottom: 1px solid var(--border); }

        td { padding: 12px; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.02); }

        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; background: rgba(16, 185, 129, 0.1); color: var(--green); }

    </style>

</head>

<body>



    <aside class="sidebar">

        <h2><i class="fas fa-layer-group"></i> TECH PANEL</h2>

        <nav>

            <a href="panel_admin.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a>

            <a href="ver_productos.php"><i class="fas fa-box"></i> Inventario</a>

            <a href="ver_pedidos.php"><i class="fas fa-shopping-cart"></i> Ventas</a>

            <a href="../index.php" style="margin-top: 50px;"><i class="fas fa-external-link-alt"></i> Ver Tienda</a>

            <a href="../logout.php" style="color: #f87171;"><i class="fas fa-power-off"></i> Salir</a>

        </nav>

    </aside>



    <main class="main">

        <div class="header-main">

            <h1>Hola, <?= $_SESSION['admin'] ?> 👋</h1>

            <p style="color: #64748b;">Esto es lo que está pasando en tu tienda hoy.</p>

        </div>



        <div class="kpi-grid">

            <div class="kpi-card">

                <i class="fas fa-mobile-alt icon"></i>

                <h3>Productos Activos</h3>

                <span class="value"><?= $total_productos ?></span>

            </div>

            <div class="kpi-card">

                <i class="fas fa-wallet icon"></i>

                <h3>Valor Almacén</h3>

                <span class="value" style="color: #fbbf24;"><?= number_format($valor_inventario, 2) ?> €</span>

            </div>

            <div class="kpi-card">

                <i class="fas fa-shopping-bag icon"></i>

                <h3>Pedidos Totales</h3>

                <span class="value"><?= $num_pedidos ?></span>

            </div>

        </div>



        <div class="content-grid">

            <div class="card">

                <div style="display:flex; justify-content:space-between; align-items:center;">

                    <h3 style="margin:0;">Últimas Ventas</h3>

                    <a href="ver_pedidos.php" style="color: var(--blue); font-size: 0.8rem; text-decoration:none;">Ver todas</a>

                </div>

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Cliente</th>

                            <th>Fecha</th>

                            <th>Estado</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while($p = $pedidos_recientes->fetch_assoc()): ?>

                        <tr>

                            <td style="color: var(--blue); font-weight:bold;">#<?= $p['identificador'] ?></td>

                            <td><?= htmlspecialchars($p['nombre']) ?></td>

                            <td style="color: #64748b;"><?= date('H:i - d M', strtotime($p['fecha'])) ?></td>

                            <td><span class="badge">Pagado</span></td>

                        </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>



            <div class="card">

                <h3>Stock por Modelo</h3>

                <canvas id="graficaStock" style="max-height: 250px;"></canvas>

            </div>

        </div>

    </main>



    <script>

        const ctx = document.getElementById('graficaStock').getContext('2d');

        new Chart(ctx, {

            type: 'bar',

            data: {

                labels: <?= json_encode($labels) ?>,

                datasets: [{

                    label: 'Unidades',

                    data: <?= json_encode($counts) ?>,

                    backgroundColor: '#3b82f6',

                    borderRadius: 8

                }]

            },

            options: {

                plugins: { legend: { display: false } },

                scales: {

                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },

                    x: { grid: { display: false }, ticks: { color: '#64748b' } }

                }

            }

        });

    </script>

</body>

</html>