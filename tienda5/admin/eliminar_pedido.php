<?php
include 'proteger.php';
include '../conexion.php'; // Usando la variable $conexion optimizada

$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pedido <= 0) {
    die("<div style='color:white; background:#0f172a; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>❌ ID de pedido no válido.</div>");
}

/* USO DE SENTENCIAS PREPARADAS POR SEGURIDAD 
   -----------------------------------------
   Borramos en orden para respetar la integridad referencial.
*/

// 1. Eliminar primero las líneas del pedido (Hijos)
$stmt1 = $conexion->prepare("DELETE FROM lineaspedido WHERE pedido_id = ?");
$stmt1->bind_param("i", $id_pedido);

if (!$stmt1->execute()) {
    die("❌ Error al eliminar las líneas del pedido: " . $conexion->error);
}
$stmt1->close();

// 2. Eliminar el pedido (Padre)
$stmt2 = $conexion->prepare("DELETE FROM pedidos WHERE identificador = ?");
$stmt2->bind_param("i", $id_pedido);

if (!$stmt2->execute()) {
    die("❌ Error al eliminar el pedido: " . $conexion->error);
}
$stmt2->close();

// 3. Redirigir al listado con un parámetro de éxito (opcional para mostrar alertas)
header("Location: ver_pedidos.php?mensaje=eliminado");
exit;
?>