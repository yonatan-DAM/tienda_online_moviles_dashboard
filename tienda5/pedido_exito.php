<?php
/**
 * PEDIDO_EXITO.PHP
 * Página de confirmación profesional para el cliente.
 */
$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu compra! | Tech Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --accent-green: #22c55e;
            --accent-blue: #3b82f6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        /* Tarjeta principal con animación de entrada */
        .success-card {
            background: var(--card-bg);
            padding: 60px 40px;
            border-radius: 32px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            max-width: 450px;
            width: 90%;
            animation: slideUp 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Contenedor del icono con pulso animado */
        .icon-circle {
            width: 100px;
            height: 100px;
            background: rgba(34, 197, 94, 0.1);
            color: var(--accent-green);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3.5rem;
            margin: 0 auto 30px;
            position: relative;
            animation: popIn 0.5s ease-out forwards;
        }

        @keyframes popIn {
            0% { transform: scale(0); }
            80% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Texto de éxito */
        h1 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        p {
            color: var(--text-muted);
            margin: 15px 0 35px;
            line-height: 1.6;
            font-size: 1.05rem;
        }

        /* Badge con el número de pedido */
        .order-info {
            background: rgba(15, 23, 42, 0.6);
            padding: 15px 25px;
            border-radius: 16px;
            display: inline-block;
            border: 1px dashed var(--accent-blue);
            margin-bottom: 40px;
        }

        .order-info span {
            display: block;
            font-size: 0.75rem;
            color: var(--accent-blue);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .order-number {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.6rem;
            color: var(--text-main);
            font-weight: bold;
        }

        /* Botón de acción */
        .btn-return {
            background: var(--accent-blue);
            color: white;
            padding: 16px 40px;
            border-radius: 18px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }

        .btn-return:hover {
            background: #2563eb;
            transform: translateY(-4px);
            box-shadow: 0 15px 25px -5px rgba(59, 130, 246, 0.5);
        }

        .btn-return i {
            transition: transform 0.3s ease;
        }

        .btn-return:hover i {
            transform: translateX(-5px);
        }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>

        <h1>¡Hecho!</h1>
        <p>Tu pago ha sido procesado correctamente. Hemos enviado los detalles a tu correo electrónico.</p>
        
        <div class="order-info">
            <span>ID de Transacción</span>
            <div class="order-number">#<?php echo ($id_pedido > 0) ? $id_pedido : '---'; ?></div>
        </div>

        <div style="display: block;">
            <a href="index.php" class="btn-return">
                <i class="fas fa-arrow-left"></i> Volver a la tienda
            </a>
        </div>
    </div>

</body>
</html>