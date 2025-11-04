<?php
require_once 'includes/header.php';

// Validar que se haya pasado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Álbum no encontrado.</p>";
    require_once 'includes/footer.php';
    exit();
}

$id_album = intval($_GET['id']);

// Consulta para obtener la información del álbum y del artista
$sql_album = "SELECT al.titulo, al.portada_url, ar.nombre_artista
              FROM albumes al
              JOIN artistas ar ON al.id_artista = ar.id
              WHERE al.id = ?";
$stmt_album = $conn->prepare($sql_album);
$stmt_album->bind_param("i", $id_album);
$stmt_album->execute();
$result_album = $stmt_album->get_result();

if ($result_album->num_rows == 0) {
    echo "<p>Álbum no encontrado.</p>";
    require_once 'includes/footer.php';
    exit();
}

$album = $result_album->fetch_assoc();

// Consulta para obtener las canciones del álbum
$sql_canciones = "SELECT id, titulo, duracion FROM canciones WHERE id_album = ?";
$stmt_canciones = $conn->prepare($sql_canciones);
$stmt_canciones->bind_param("i", $id_album);
$stmt_canciones->execute();
$result_canciones = $stmt_canciones->get_result();

?>

<div class="album-details-container">
    <div class="album-header">
        <img src="<?php echo htmlspecialchars($album['portada_url']); ?>" alt="Portada de <?php echo htmlspecialchars($album['titulo']); ?>">
        <div>
            <h2><?php echo htmlspecialchars($album['titulo']); ?></h2>
            <p>Por <?php echo htmlspecialchars($album['nombre_artista']); ?></p>
        </div>
    </div>

    <div class="song-list">
        <h3>Canciones del Álbum</h3>
        <ul>
            <?php
            if ($result_canciones->num_rows > 0) {
                while($cancion = $result_canciones->fetch_assoc()) {
                    echo '<li>';
                    echo '  <a href="' . BASE_URL . 'play.php?id=' . $cancion['id'] . '">';
                    echo '    <span>' . htmlspecialchars($cancion['titulo']) . '</span>';
                    // Si tienes la duración, la puedes mostrar aquí
                    // echo '    <span class="duration">' . htmlspecialchars($cancion['duracion']) . '</span>';
                    echo '  </a>';
                    echo '</li>';
                }
            } else {
                echo '<li>No hay canciones en este álbum.</li>';
            }
            ?>
        </ul>
    </div>
</div>


<?php
$stmt_album->close();
$stmt_canciones->close();
require_once 'includes/footer.php';
?>
