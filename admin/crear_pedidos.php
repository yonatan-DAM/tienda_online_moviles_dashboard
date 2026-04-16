<?php
include 'proteger.php';
include '../conexion.php'; // Usando $conexion del archivo optimizado

$mensaje = "";

// 1. Obtener lista de clientes con la variable $conexion
$clientes = [];
$consulta_clientes = "SELECT identificador, nombre, apellidos FROM clientes ORDER BY nombre";
$resultado_clientes = $conexion->query($consulta_clientes);

if ($resultado_clientes) {
    while ($fila = $resultado_clientes->fetch_assoc()) {
        $clientes[] = $fila;
    }
}

// 2. Procesar formulario con Sentencias Preparadas (Seguridad Pro)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = trim($_POST['fecha']);
    $id_cliente = trim($_POST['id_cliente']);

    if ($fecha === '' || $id_cliente === '') {
        $mensaje = '⚠️ Todos los campos son obligatorios';
    } else {
        // Usamos prepare para evitar Inyección SQL
        $stmt = $conexion->prepare("INSERT INTO pedidos (fecha, id_cliente) VALUES (?, ?)");
        $stmt->bind_param("si", $fecha, $id_cliente); // "s" para string, "i" para integer

        if ($stmt->execute()) {
            $mensaje = "✅ Pedido creado correctamente.";
        } else {
            $mensaje = "❌ Error al crear el pedido: " . $conexion->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>➕ Crear pedido - Admin</title>
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
            max-width: 480px;
            background: var(--card-bg);
            padding: 35px;
            border-radius: 20px;
            border: 1px solid #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 1.6rem;
            margin-bottom: 25px;
            text-align: center;
            color: var(--accent-blue);
            font-weight: 700;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 12px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        input:focus, select:focus {
            outline: none;
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
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }

        .mensaje {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            text-align: center;
            border: 1px solid transparent;
        }

        /* Colores dinámicos para mensajes */
        .mensaje-exito { background: rgba(34, 197, 94, 0.1); color: #4ade80; border-color: rgba(34, 197, 94, 0.2); }
        .mensaje-error { background: rgba(239, 68, 68, 0.1); color: #f87171; border-color: rgba(239, 68, 68, 0.2); }

        .volver {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .volver:hover {
            color: var(--text-main);
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h1><i class="fas fa-cart-plus"></i> Crear Pedido</h1>

    <?php if ($mensaje): 
        $clase = (strpos($mensaje, '✅') !== false) ? 'mensaje-exito' : 'mensaje-error';
    ?>
        <div class="mensaje <?= $clase ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <label><i class="fas fa-calendar-alt"></i> Fecha y Hora:</label>
        <input type="datetime-local" name="fecha" required>

        <label><i class="fas fa-user"></i> Seleccionar Cliente:</label>
        <select name="id_cliente" required>
            <option value="">-- Elige un cliente --</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['identificador'] ?>">
                    <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Confirmar Pedido</button>
    </form>

    <a href="ver_pedidos.php" class="volver"><i class="fas fa-chevron-left"></i> Volver a pedidos</a>
</div>

</body>
</html>