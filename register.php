<?php
require_once 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $tipo_usuario = $_POST['tipo_usuario'];

    $sql = "INSERT INTO usuarios (nombre_usuario, email, contrasena, tipo_usuario) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre_usuario, $email, $contrasena, $tipo_usuario);

    if ($stmt->execute()) {
        echo "<p>Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>.</p>";
        // Si es un artista, podríamos redirigir a una página para crear el perfil de artista
        if ($tipo_usuario === 'artista') {
            // Obtener el ID del usuario recién creado
            $id_usuario = $stmt->insert_id;
            // Insertar en la tabla de artistas
            $nombre_artista_default = "Nuevo Artista"; // O podrías pedirlo en el formulario
            $sql_artista = "INSERT INTO artistas (id_usuario, nombre_artista) VALUES (?, ?)";
            $stmt_artista = $conn->prepare($sql_artista);
            $stmt_artista->bind_param("is", $id_usuario, $nombre_usuario); // Usamos el nombre de usuario como nombre de artista por defecto
            $stmt_artista->execute();
            $stmt_artista->close();
        }
    } else {
        echo "<p>Error en el registro: " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<div class="form-container">
    <h2>Crear una Cuenta</h2>
    <form action="register.php" method="post">
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
    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
</div>

<?php require_once 'includes/footer.php'; ?>
