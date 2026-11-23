<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: listar_productos.php');
    exit();
}
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <main class="container">
        <div class="card shadow-sm mx-auto" style="max-width: 400px;">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">TecnoStock</h2>
                <h5 class="card-title text-center mb-4">Iniciar Sesión</h5>

                <?php if ($error === '1'): ?>
                    <div class="alert alert-danger">Correo o contraseña incorrectos.</div>
                <?php endif; ?>

                <form action="validar_login.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="admin@tecnostock.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>