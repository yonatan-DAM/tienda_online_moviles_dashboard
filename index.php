<?php
session_start();
include 'conexion.php'; 

/** * DETECCIÓN DE CONEXIÓN
 * Usamos la variable $conexion o $conexion_a_sql según tu archivo conexion.php
 */
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

if (!$db) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>⚠️ Error: No se pudo conectar a la base de datos.</div>");
}

/** * CONSULTA DE PRODUCTOS
 * Filtramos por 'activo = 1' para que no aparezcan los productos "borrados"
 */
$sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY identificador DESC";
$ver_resultados = $db->query($sql);

if (!$ver_resultados) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>⚠️ Error al cargar la tienda: " . $db->error . "</div>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda5 - Tech Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --accent-blue: #3b82f6;
            --accent-green: #22c55e;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
        }

        /* Navegación Premium */
        header {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .menu {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 25px;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a:hover { color: var(--accent-blue); }

        /* Grid de Productos */
        main {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .productos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        article {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        article:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-color: var(--accent-blue);
        }

        .foto_producto {
            width: 100%;
            height: 220px;
            object-fit: contain;
            border-radius: 15px;
            background: #0f172a;
            margin-bottom: 15px;
            padding: 10px;
            box-sizing: border-box;
        }

        article h3 { margin: 10px 0; font-size: 1.2rem; font-weight: 700; }
        
        article p.descripcion {
            color: var(--text-muted);
            font-size: 0.85rem;
            height: 45px;
            overflow: hidden;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .precio {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fbbf24;
            margin-bottom: 20px;
        }

        .btn-carrito {
            background: var(--accent-blue);
            color: white;
            text-decoration: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: bold;
            transition: 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-carrito:hover {
            background: #2563eb;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.5);
        }

        .empty-state {
            text-align: center;
            grid-column: 1 / -1;
            padding: 100px 0;
            color: var(--text-muted);
        }

        footer {
            text-align: center;
            padding: 50px;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            margin-top: 80px;
        }
    </style>
</head>
<body>
    <header>
        <div class="menu">
            <a href="index.php" class="logo">
                <i class="fas fa-microchip"></i> TECH STORE
            </a>
            <nav class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
                <a href="ver_carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
                <a href="vaciar_carrito.php"><i class="fas fa-trash-alt"></i> Limpiar</a>
                <a href="login.php" style="color: var(--accent-blue);"><i class="fas fa-user-shield"></i> Admin</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="productos">
            <?php if ($ver_resultados->num_rows > 0): ?>
                <?php while ($p = $ver_resultados->fetch_assoc()): ?>    
                    <article>
                        <img src="data:image/png;base64,<?= base64_encode($p['imagen']) ?>" 
                             alt="<?= htmlspecialchars($p['titulo']) ?>"
                             class="foto_producto">

                        <h3><?= htmlspecialchars($p['titulo']) ?></h3>
                        <p class="descripcion"><?= htmlspecialchars($p['descripcion']) ?></p>

                        <div class="precio"><?= number_format($p['precio'], 2) ?> €</div>

                        <a href="añadir_al_carrito.php?identificador=<?= $p['identificador'] ?>" class="btn-carrito">
                            <i class="fas fa-cart-plus"></i> Añadir al carrito
                        </a>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open fa-4x" style="margin-bottom: 20px;"></i>
                    <h2>No hay productos disponibles en este momento</h2>
                    <p>Vuelve pronto para ver nuestras novedades.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        &copy; <?= date('Y') ?> Yonatan Mora Ruiz | Tech Store Premium
    </footer>
</body>
</html>