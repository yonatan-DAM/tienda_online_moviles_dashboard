<?php
session_start();
include 'conexion.php';


$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Usamos la variable de conexión detectada (compatible con tus archivos anteriores)
    $db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

    // Consulta segura
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['admin'] = $usuario;
        header("Location: admin/panel_admin.php");
        exit;
    } else {
        $error = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador | TechStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* IMAGEN RELACIONADA CON MÓVILES DE UNSPLASH */
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), 
                        url('https://images.unsplash.com/photo-1556656793-062ff987b50c?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }

        .login-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-icon {
            background: var(--primary);
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.5);
        }

        h1 {
            color: white;
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 700;
        }

        p.subtitle {
            color: #94a3b8;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            font-size: 16px;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .footer-links {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-icon">
            <i class="fas fa-mobile-screen-button"></i>
        </div>
        <h1>TechStore Admin</h1>
        <p class="subtitle">Panel de Gestión de Inventario</p>

        <?php if($error): ?>
            <div class="error-msg">
                <i class="fas fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="usuario" placeholder="Usuario" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn-login">
                Entrar al Sistema <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="footer-links">
            <a href="index.php"><i class="fas fa-store"></i> Volver a la tienda</a>
            <a href="#"><i class="fas fa-question-circle"></i> Ayuda</a>
        </div>
    </div>

</body>
</html>