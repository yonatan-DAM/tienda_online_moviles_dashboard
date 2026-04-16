<?php
include 'conexion.php';

$pedido_id = $_GET['pedido_id'];

// ===============================
// 1. OBTENER DATOS DEL PEDIDO
// ===============================
$sql = "SELECT c.nombre, c.apellidos, c.telefono, c.email, c.poblacion, c.direccion, p.fecha
        FROM pedidos p
        JOIN clientes c ON p.id_cliente = c.identificador
        WHERE p.identificador = ?";
$stmt = $conexion_a_sql->prepare($sql);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$datos_cliente = $stmt->get_result()->fetch_assoc();

// ===============================
// 2. OBTENER LÍNEAS DEL PEDIDO
// ===============================
$sql = "SELECT pr.titulo AS nombre, pr.precio, lp.cantidad
        FROM lineaspedido lp
        JOIN productos pr ON lp.producto_id = pr.identificador
        WHERE lp.pedido_id = ?";
$stmt = $conexion_a_sql->prepare($sql);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$lineas = $stmt->get_result();

$lista_productos = "";
$total = 0;

while ($fila = $lineas->fetch_assoc()) {

    $subtotal = $fila['precio'] * $fila['cantidad'];
    $total += $subtotal;

    // Formato alineado tipo tabla
    $lista_productos .= sprintf(
        "%-28s | %7.2f€ | %4d | %8.2f€\n",
        $fila['nombre'],
        $fila['precio'],
        $fila['cantidad'],
        $subtotal
    );
}

// ===============================
// 3. CREAR PROMPT PARA LA IA
// ===============================
$prompt = "
Genera una factura en texto plano con el siguiente formato EXACTO, bien alineado y profesional:

===========================
          FACTURA
===========================

EMISOR:
Nombre: Tienda5
CIF/NIF: 00000000X
Dirección: Calle Principal 123
Localidad: Valencia
Email: tienda@correo.com

RECEPTOR:
Nombre: {$datos_cliente['nombre']} {$datos_cliente['apellidos']}
Teléfono: {$datos_cliente['telefono']}
Email: {$datos_cliente['email']}
Población: {$datos_cliente['poblacion']}
Dirección: {$datos_cliente['direccion']}

DATOS DE LA FACTURA:
Fecha: {$datos_cliente['fecha']}
Número: F-{$pedido_id}

LÍNEAS DE PRODUCTO:
Producto                     | Precio   | Cant | Subtotal
----------------------------------------------------------
$lista_productos
----------------------------------------------------------

BASE IMPONIBLE: $total €
IVA (21%): " . ($total * 0.21) . " €
TOTAL: " . ($total * 1.21) . " €

Muchas gracias por tu pedido.
";

// ===============================
// 4. ENVIAR A OLLAMA
// ===============================
$payload = json_encode([
    "model" => "qwen2.5-coder:1.5b",
    "prompt" => $prompt,
    "stream" => false
]);

$ch = curl_init("http://192.168.1.131:11434/api/generate");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$respuesta = curl_exec($ch);
curl_close($ch);

$respuesta_json = json_decode($respuesta, true);
$factura = $respuesta_json["response"] ?? "Error generando factura";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura generada</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<pre style="
    white-space: pre;
    background: #000000;
    color: #f5f5f5;
    padding: 25px;
    border-radius: 10px;
    font-size: 16px;
    font-family: 'Courier New', monospace;
    line-height: 1.4;
    border: 1px solid #333;
">
<?= htmlspecialchars($factura) ?>
</pre>

<a href="index.php" style="display:inline-block; margin-top:20px; font-size:18px; color:white;">
    Volver a la tienda
</a>

</body>
</html>


</body>
</html>
