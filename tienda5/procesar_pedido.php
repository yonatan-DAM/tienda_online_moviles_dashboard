<?php
session_start();
include 'conexion.php';

// Detectamos la conexión
$db = isset($conexion_a_sql) ? $conexion_a_sql : (isset($conexion) ? $conexion : null);

if ($db === null || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit;
}

// Simulamos cliente 1 (puedes ajustarlo según tu tabla clientes)
$id_cliente = 1; 
$fecha_actual = date('Y-m-d H:i:s');

// 1. Insertar Pedido
$stmt_pedido = $db->prepare("INSERT INTO pedidos (fecha, id_cliente) VALUES (?, ?)");
$stmt_pedido->bind_param("si", $fecha_actual, $id_cliente);

if ($stmt_pedido->execute()) {
    $id_nuevo_pedido = $db->insert_id;

    // 2. Insertar Líneas
    $stmt_lineas = $db->prepare("INSERT INTO lineaspedido (pedido_id, producto_id, cantidad) VALUES (?, ?, ?)");
    foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
        $stmt_lineas->bind_param("iii", $id_nuevo_pedido, $id_producto, $cantidad);
        $stmt_lineas->execute();
    }

    // 3. Limpiar carrito
    unset($_SESSION['carrito']);

    // 4. ÉXITO: Redirigimos a una página con estilo
    header("Location: pedido_exito.php?id=$id_nuevo_pedido");
    exit;
} else {
    die("Error crítico: " . $db->error);
}
?>