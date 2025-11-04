<?php require_once 'includes/header.php'; ?>

<?php
// Consulta para obtener todos los álbumes con el nombre del artista
$sql = "SELECT al.id, al.titulo, al.portada_url, ar.nombre_artista
        FROM albumes al
        JOIN artistas ar ON al.id_artista = ar.id
        ORDER BY ar.nombre_artista, al.titulo";

$result = $conn->query($sql);
?>

<div class="albums-container">
    <h2>Todos los Álbumes</h2>
    <div class="album-grid">
        <?php
        if ($result->num_rows > 0) {
            while($album = $result->fetch_assoc()) {
                echo '<div class="album-card">';
                echo '  <a href="' . BASE_URL . 'album_songs.php?id=' . $album['id'] . '">';
                echo '    <img src="' . htmlspecialchars($album['portada_url']) . '" alt="Portada de ' . htmlspecialchars($album['titulo']) . '">';
                echo '    <h3>' . htmlspecialchars($album['titulo']) . '</h3>';
                echo '    <p>' . htmlspecialchars($album['nombre_artista']) . '</p>';
                echo '  </a>';
                echo '</div>';
            }
        } else {
            echo '<p>No hay álbumes disponibles en este momento.</p>';
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
