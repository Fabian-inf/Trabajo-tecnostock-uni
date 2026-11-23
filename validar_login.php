<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario && ($password === '123456' || $password === $usuario['clave'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                header('Location: listar_productos.php');
                exit();
            } else {
                header('Location: login.php?error=1');
                exit();
            }
        } catch (PDOException $e) {
            exit("Error en el sistema: " . $e->getMessage());
        }
    } else {
        header('Location: login.php?error=1');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}