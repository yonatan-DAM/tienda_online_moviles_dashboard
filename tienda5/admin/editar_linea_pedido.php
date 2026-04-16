<?php
include 'proteger.php';
include '../conexion.php'; // Usando $conexion del archivo optimizado

$id_linea = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_pedido = isset($_GET['pedido']) ? intval($_GET['pedido']) : 0;
$error = "";

if ($id_linea <= 0 || $id_pedido <= 0) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>❌ Datos inválidos.</div>");
}

// 1. Validar con Sentencia Preparada
$stmt = $conexion->prepare("
    SELECT lp.*, p.titulo 
    FROM lineaspedido lp
    JOIN productos p ON lp.producto_id = p.identificador
    WHERE lp.identificador = ? AND lp.pedido_id = ?
");
$stmt->bind_param("ii", $id_linea, $id_pedido);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>❌ Línea no encontrada.</div>");
}

$linea = $resultado->fetch_assoc();

// 2. Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_cantidad = intval($_POST['cantidad']);

    if ($nueva_cantidad > 0) {
        $stmt_update = $conexion->prepare("UPDATE lineaspedido SET cantidad = ? WHERE identificador = ?");
        $stmt_update->bind_param("ii", $nueva_cantidad, $id_linea);
        
        if ($stmt_update->execute()) {
            header("Location: detalle_pedido.php?id=$id_pedido");
            exit;
        } else {
            $error = "❌ Error al actualizar en la base de datos.";
        }
    } else {
        $error = "⚠️ La cantidad debe ser al menos 1.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✏️ Editar Cantidad - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --accent-blue: #3b82f6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .contenedor {
            width: 100%;
            max-width: 400px;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h1 { font-size: 1.4rem; color: var(--accent-blue); margin-bottom: 10px; }
        p.producto-nombre { color: var(--text-muted); margin-bottom: 25px; font-style: italic; }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        input {
            width: 100%;
            padding: 12px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover { filter: brightness(1.1); transform: translateY(-2px); }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .volver {
            display: block;
            margin-top: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .volver:hover { color: var(--accent-blue); }
    </style>
</head>
<body>

<div class="contenedor">
    <h1><i class="fas fa-edit"></i> Editar Cantidad</h1>
    <p class="producto-nombre"><?= htmlspecialchars($linea['titulo']) ?></p>

    <?php if ($error): ?>
        <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Cantidad de productos:</label>
        <input type="number" name="cantidad" min="1" max="999" value="<?= $linea['cantidad'] ?>" required autofocus>

        <button type="submit">Actualizar Línea</button>
    </form>

    <a href="detalle_pedido.php?id=<?= $id_pedido ?>" class="volver">
        <i class="fas fa-times"></i> Cancelar y Volver
    </a>
</div>

</body>
</html>