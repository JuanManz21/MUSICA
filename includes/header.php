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
            <a href="<?php echo BASE_URL; ?>songs.php">Canciones</a>
            <a href="<?php echo BASE_URL; ?>albums.php">Álbumes</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php // Si el usuario es un artista, mostrar el enlace al panel
                if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'artista'): ?>
                    <a href="<?php echo BASE_URL; ?>dashboard.php" style="color: var(--color-primary);">Mi Panel</a>
                <?php endif; ?>

                <a href="<?php echo BASE_URL; ?>logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>login.php">Iniciar Sesión</a>
                <a href="<?php echo BASE_URL; ?>register.php">Registrarse</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
        <?php
        // Mostrar y luego limpiar mensajes de error de sesión
        if (isset($_SESSION['error_message'])) {
            echo '<p style="color: #F44336; text-align: center; background-color: var(--color-card); padding: 15px; border-radius: 8px;">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']);
        }
        ?>
