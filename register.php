<?php
require_once 'includes/header.php';

$message = ''; // Variable para mensajes de éxito o error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $tipo_usuario = $_POST['tipo_usuario'];

    $sql = "INSERT INTO usuarios (nombre_usuario, email, contrasena, tipo_usuario) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre_usuario, $email, $contrasena, $tipo_usuario);

    if ($stmt->execute()) {
        $message = "Registro exitoso. Ahora puedes <a href='" . BASE_URL . "login.php'>iniciar sesión</a>.";
        // Si es un artista, creamos su perfil de artista
        if ($tipo_usuario === 'artista') {
            $id_usuario = $stmt->insert_id;
            $sql_artista = "INSERT INTO artistas (id_usuario, nombre_artista) VALUES (?, ?)";
            $stmt_artista = $conn->prepare($sql_artista);
            $stmt_artista->bind_param("is", $id_usuario, $nombre_usuario);
            $stmt_artista->execute();
            $stmt_artista->close();
        }
    } else {
        // Manejar error de email/usuario duplicado
        if ($conn->errno == 1062) {
             $message = "Error: El nombre de usuario o el correo electrónico ya existen.";
        } else {
             $message = "Error en el registro: " . $conn->error;
        }
    }

    $stmt->close();
}
?>

<div class="form-container">
    <h2>Crear una Cuenta</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>register.php" method="post">
        <div class="form-group">
            <label for="nombre_usuario">Nombre de Usuario</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>
        <div class="form-group">
            <label for="tipo_usuario">Tipo de Cuenta</label>
            <select id="tipo_usuario" name="tipo_usuario" required>
                <option value="oyente">Oyente</option>
                <option value="artista">Artista</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit">Registrarse</button>
        </div>
    </form>
    <p>¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>login.php">Inicia sesión aquí</a>.</p>
</div>

<?php require_once 'includes/footer.php'; ?>
