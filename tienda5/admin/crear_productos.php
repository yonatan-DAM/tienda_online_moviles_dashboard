<?php
include 'proteger.php'; 
include '../conexion.php'; // Usamos $conexion del archivo optimizado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $marca = trim($_POST['marca']); // ¡Nuevo! Necesario para la gráfica
    $precio = floatval($_POST['precio']);

    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir la imagen");
    }

    $mime = mime_content_type($_FILES['imagen']['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
        die("Formato de imagen no permitido");
    }

    $imagen = file_get_contents($_FILES['imagen']['tmp_name']);

    // Añadimos la columna 'marca' a tu INSERT
    $sql = "INSERT INTO productos (titulo, descripcion, marca, precio, imagen) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    $null = NULL;
    // Cambiamos a "sssdb" (String, String, String, Double, Blob)
    $stmt->bind_param("sssdb", $titulo, $descripcion, $marca, $precio, $null);
    $stmt->send_long_data(4, $imagen);

    if (!$stmt->execute()) {
        die("Error SQL: " . $stmt->error);
    }

    header("Location: ./panel_admin.php"); // Redirigir al nuevo dashboard
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>➕ Crear producto - Admin</title>
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

        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            text-align: center;
            color: var(--accent-blue);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-blue);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--accent-green);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, filter 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: var(--accent-blue);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1><i class="fas fa-plus-circle"></i> Nuevo Producto</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Título del Producto</label>
                <input type="text" name="titulo" placeholder="Ej: Samsung Galaxy S25" required>
            </div>

            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" placeholder="Ej: Samsung, Apple, Xiaomi" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3" placeholder="Detalles técnicos..." required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Precio (€)</label>
                    <input type="number" step="0.01" name="precio" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Imagen</label>
                    <input type="file" name="imagen" accept="image/*" style="padding: 8px;" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">Publicar Producto</button>
        </form>

        <a href="panel_admin.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al panel</a>
    </div>
</div>

</body>
</html>