<?php
session_start();
unset($_SESSION['carrito']);
header('location: index.php');
exit;
?>