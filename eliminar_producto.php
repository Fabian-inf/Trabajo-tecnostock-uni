<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'conexion.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmtMov = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE id_producto = :id");
        $stmtMov->execute([':id' => $id]);
        $tieneMovimientos = $stmtMov->fetchColumn() > 0;

        if ($tieneMovimientos) {
            $stmt = $pdo->prepare("UPDATE productos SET estado = 0 WHERE id_producto = :id");
            $stmt->execute([':id' => $id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto = :id");
            $stmt->execute([':id' => $id]);
        }

        header('Location: listar_productos.php');
        exit();
    } catch (PDOException $e) {
        echo "Error al procesar el producto: " . $e->getMessage();
    }
}
header('Location: listar_productos.php');
exit();