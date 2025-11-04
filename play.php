<?php
require_once 'includes/header.php';

// Validar que se haya pasado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Canción no encontrada.</p>";
    require_once 'includes/footer.php';
    exit();
}

$id_cancion = intval($_GET['id']);

// Consulta para obtener los detalles de la canción y el artista
$sql = "SELECT c.titulo, c.archivo_mp3, c.portada_url, a.nombre_artista
        FROM canciones c
        JOIN artistas a ON c.id_artista = a.id
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cancion);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p>Canción no encontrada.</p>";
    require_once 'includes/footer.php';
    exit();
}

$cancion = $result->fetch_assoc();
?>

<div class="player-container">
    <h2>Reproduciendo ahora</h2>
    <img src="<?php echo htmlspecialchars($cancion['portada_url']); ?>" alt="Portada de <?php echo htmlspecialchars($cancion['titulo']); ?>">
    <h3><?php echo htmlspecialchars($cancion['titulo']); ?></h3>
    <p><?php echo htmlspecialchars($cancion['nombre_artista']); ?></p>

    <audio controls autoplay>
        <source src="<?php echo htmlspecialchars($cancion['archivo_mp3']); ?>" type="audio/mpeg">
        Tu navegador no soporta el elemento de audio.
    </audio>
</div>

<?php
$stmt->close();
require_once 'includes/footer.php';
?>
