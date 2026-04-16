<?php
include 'proteger.php';
include '../conexion.php'; // Usando $conexion del archivo optimizado

$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = "";

if ($id_pedido <= 0) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>❌ Pedido inválido.</div>");
}

// 1. Obtener datos del pedido de forma segura
$stmt_ped = $conexion->prepare("SELECT * FROM pedidos WHERE identificador = ?");
$stmt_ped->bind_param("i", $id_pedido);
$stmt_ped->execute();
$resultado_pedido = $stmt_ped->get_result();

if ($resultado_pedido->num_rows === 0) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>❌ Pedido no encontrado.</div>");
}

$pedido = $resultado_pedido->fetch_assoc();

// 2. Obtener lista de clientes para el select
$resultado_clientes = $conexion->query("SELECT identificador, nombre, apellidos FROM clientes ORDER BY nombre");

// 3. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_cliente = intval($_POST['cliente']);
    $nueva_fecha = $_POST['fecha'];

    if ($nuevo_cliente > 0 && !empty($nueva_fecha)) {
        $stmt_upd = $conexion->prepare("UPDATE pedidos SET id_cliente = ?, fecha = ? WHERE identificador = ?");
        $stmt_upd->bind_param("isi", $nuevo_cliente, $nueva_fecha, $id_pedido);

        if ($stmt_upd->execute()) {
            header("Location: ver_pedidos.php");
            exit;
        } else {
            $error = "❌ Error al actualizar el pedido.";
        }
    } else {
        $error = "⚠️ Selecciona un cliente y una fecha válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✏️ Editar Pedido #<?= $id_pedido ?></title>
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
            max-width: 450px;
            background: var(--card-bg);
            padding: 35px;
            border-radius: 20px;
            border: 1px solid #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
        }

        h1 {
            font-size: 1.5rem;
            color: var(--accent-blue);
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        select, input {
            width: 100%;
            padding: 12px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            margin-bottom: 20px;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        select:focus, input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            text-align: center;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .volver:hover { color: var(--text-main); }
    </style>
</head>
<body>

<div class="contenedor">
    <h1><i class="fas fa-edit"></i> Editar Pedido #<?= $id_pedido ?></h1>

    <?php if ($error): ?>
        <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label><i class="fas fa-user"></i> Cliente asignado:</label>
        <select name="cliente" required>
            <?php while ($cliente = $resultado_clientes->fetch_assoc()): ?>
                <option value="<?= $cliente['identificador'] ?>"
                    <?= $cliente['identificador'] == $pedido['id_cliente'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cliente['nombre'] . " " . $cliente['apellidos']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label><i class="fas fa-calendar-alt"></i> Fecha del pedido:</label>
        <input type="datetime-local" name="fecha" 
               value="<?= date('Y-m-d\TH:i', strtotime($pedido['fecha'])) ?>" required>

        <button type="submit">Actualizar Pedido</button>
    </form>

    <a href="ver_pedidos.php" class="volver">
        <i class="fas fa-arrow-left"></i> Descartar y volver
    </a>
</div>

</body>
</html>