<?php
include '../proteger.php';
include '../conexion.php'; 

// Detección de conexión
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

if (!$db) { die("Error de conexión."); }

if (!isset($_GET['id'])) { header("Location: ver_productos.php"); exit; }
$id = intval($_GET['id']);

$stmt_carga = $db->prepare("SELECT * FROM productos WHERE identificador = ?");
$stmt_carga->bind_param("i", $id);
$stmt_carga->execute();
$producto = $stmt_carga->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $nueva_imagen = file_get_contents($_FILES['imagen']['tmp_name']);
        $sql = "UPDATE productos SET titulo=?, descripcion=?, precio=?, imagen=? WHERE identificador=?";
        $stmt = $db->prepare($sql);
        $null = NULL;
        $stmt->bind_param("ssdbi", $titulo, $descripcion, $precio, $null, $id);
        $stmt->send_long_data(3, $nueva_imagen);
    } else {
        $sql = "UPDATE productos SET titulo=?, descripcion=?, precio=? WHERE identificador=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssdi", $titulo, $descripcion, $precio, $id);
    }

    if ($stmt->execute()) {
        header("Location: ver_productos.php?editado=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar <?= htmlspecialchars($producto['titulo']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --blue: #3b82f6;
            --green: #10b981;
            --text: #f8fafc;
            --border: #334155;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: var(--card);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid var(--border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header i { font-size: 2.5rem; color: var(--blue); margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 1.5rem; letter-spacing: -0.5px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #94a3b8; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        
        input, textarea {
            width: 100%;
            padding: 14px;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .image-section {
            display: flex;
            background: rgba(15, 23, 42, 0.5);
            padding: 15px;
            border-radius: 16px;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            border: 1px dashed var(--border);
        }

        .image-section img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: var(--bg);
            border-radius: 10px;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--blue);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .btn-cancel:hover { color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <i class="fas fa-pen-nib"></i>
            <h1>Editar Producto</h1>
            <p style="color: #94a3b8; font-size: 0.9rem;">ID de referencia: #<?= $id ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nombre del Dispositivo</label>
                <input type="text" name="titulo" value="<?= htmlspecialchars($producto['titulo']) ?>" required>
            </div>

            <div class="form-group">
                <label>Descripción del Producto</label>
                <textarea name="descripcion" rows="4" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Precio de Venta (€)</label>
                <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>
            </div>

            <div class="image-section">
                <img src="data:image/png;base64,<?= base64_encode($producto['imagen']) ?>" alt="Miniatura">
                <div style="flex: 1;">
                    <span style="display: block; font-size: 0.8rem; font-weight: bold; color: var(--blue);">IMAGEN ACTUAL</span>
                    <input type="file" name="imagen" accept="image/*" style="border: none; padding: 5px 0; background: transparent; font-size: 0.8rem; color: #94a3b8;">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>

        <a href="ver_productos.php" class="btn-cancel">
            <i class="fas fa-times"></i> Cancelar y volver
        </a>
    </div>
</div>

</body>
</html>