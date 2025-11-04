<?php
require_once 'includes/header.php';

// Redirigir si el usuario ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT id, nombre_usuario, contrasena, tipo_usuario FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($contrasena, $user['contrasena'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];

            // Redirigir a la página de inicio
            header("Location: " . BASE_URL . "index.php");
            exit();
        } else {
            $error = "La contraseña es incorrecta.";
        }
    } else {
        $error = "No se encontró ningún usuario con ese correo electrónico.";
    }
    $stmt->close();
}
?>

<div class="form-container">
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)): ?>
        <p style="color: #F44336; text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="<?php echo BASE_URL; ?>login.php" method="post">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>
        <div class="form-group">
            <button type="submit">Iniciar Sesión</button>
        </div>
    </form>
    <p>¿No tienes una cuenta? <a href="<?php echo BASE_URL; ?>register.php">Regístrate aquí</a>.</p>
</div>

<?php require_once 'includes/footer.php'; ?>
