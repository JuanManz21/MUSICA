<?php
session_start();
require_once 'db.php';

// Lógica para determinar la URL base dinámicamente
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
define('BASE_URL', $base_path);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Música</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <a href="<?php echo BASE_URL; ?>index.php" class="logo">MusicHub</a>
        <nav>
            <a href="<?php echo BASE_URL; ?>index.php">Inicio</a>
            <a href="<?php echo BASE_URL; ?>albums.php">Álbumes</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>login.php">Iniciar Sesión</a>
                <a href="<?php echo BASE_URL; ?>register.php">Registrarse</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
