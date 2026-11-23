<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = intval($_POST['id_producto'] ?? 0);
    $cantidad = intval($_POST['cantidad'] ?? 0);

    if ($id_producto > 0 && $cantidad > 0) {
        try {
            $stmt = $pdo->prepare("SELECT stock_actual FROM productos WHERE id_producto = :id");
            $stmt->execute([':id' => $id_producto]);
            $producto = $stmt->fetch();

            if ($producto && $producto['stock_actual'] >= $cantidad) {
                $sql_update = "UPDATE productos SET stock_actual = stock_actual - :cantidad WHERE id_producto = :id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([
                    ':cantidad' => $cantidad,
                    ':id' => $id_producto
                ]);

                $sql_mov = "INSERT INTO movimientos (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario) 
                            VALUES (NOW(), 'Salida', :cantidad, 'Descuento / Venta de stock', :id_producto, :id_usuario)";
                $stmt_mov = $pdo->prepare($sql_mov);
                $stmt_mov->execute([
                    ':cantidad' => $cantidad,
                    ':id_producto' => $id_producto,
                    ':id_usuario' => $_SESSION['usuario_id'] ?? 1
                ]);

                header('Location: listar_productos.php?msg=salida_ok');
                exit();
            } else {
                header('Location: listar_productos.php?error=sin_stock');
                exit();
            }
        } catch (PDOException $e) {
            echo "Error al registrar salida: " . $e->getMessage();
        }
    }
}
header('Location: listar_productos.php');
exit();
?>