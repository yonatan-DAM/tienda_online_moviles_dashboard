<?php
session_start();

/**
 * 1. Validar el identificador
 * Usamos filter_input para mayor seguridad y limpiamos el ID.
 */
$id_producto = filter_input(INPUT_GET, 'identificador', FILTER_VALIDATE_INT);

// Si no hay ID o es inválido, volvemos a la tienda
if (!$id_producto) {
    header("Location: index.php"); 
    exit;
}

/**
 * 2. Inicializar el carrito
 * Si no existe la variable de sesión 'carrito', la creamos como un array vacío.
 */
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

/**
 * 3. Gestión de cantidades
 * Si el producto ya está en el array, incrementamos su valor (cantidad).
 * Si no está, lo añadimos con valor 1.
 */
if (isset($_SESSION['carrito'][$id_producto])) {
    $_SESSION['carrito'][$id_producto]++;
} else {
    $_SESSION['carrito'][$id_producto] = 1;
}

/**
 * 4. Redirección
 * Una vez añadido, mandamos al cliente a ver su carrito.
 */
header("Location: ver_carrito.php");
exit;
?>