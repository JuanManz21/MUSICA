<?php
require_once 'includes/header.php';
?>

<style>
/* Estilos específicos para la lista de canciones para que se vea bien */
.song-list-container {
    padding: 20px;
}
.song-list-container h2 {
    font-size: 2em;
    border-bottom: 2px solid var(--color-primary);
    padding-bottom: 10px;
    margin-bottom: 30px;
}
.all-songs-list {
    list-style: none;
    padding: 0;
}
.all-songs-list li {
    margin-bottom: 15px;
}
.all-songs-list a {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: var(--color-card);
    border-radius: 8px;
    transition: background-color 0.3s;
}
.all-songs-list a:hover {
    background-color: var(--color-card-hover);
}
.all-songs-list img {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    margin-right: 15px;
}
.song-info {
    flex-grow: 1;
}
.song-info .title {
    font-weight: bold;
    color: var(--color-text-primary);
}
.song-info .artist {
    font-size: 0.9em;
    color: var(--color-text-secondary);
}
</style>

<div class="song-list-container">
    <h2>Todas las Canciones</h2>
    <ul class="all-songs-list">
        <?php
        // Consulta para obtener todas las canciones con detalles del artista
        $sql = "SELECT c.id, c.titulo, c.portada_url, a.nombre_artista
                FROM canciones c
                JOIN artistas a ON c.id_artista = a.id
                ORDER BY c.titulo ASC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($song = $result->fetch_assoc()) {
                echo '<li>';
                echo '  <a href="' . BASE_URL . 'play.php?id=' . $song['id'] . '">';
                echo '    <img src="' . htmlspecialchars($song['portada_url']) . '" alt="Portada de ' . htmlspecialchars($song['titulo']) . '">';
                echo '    <div class="song-info">';
                echo '      <span class="title">' . htmlspecialchars($song['titulo']) . '</span><br>';
                echo '      <span class="artist">' . htmlspecialchars($song['nombre_artista']) . '</span>';
                echo '    </div>';
                echo '  </a>';
                echo '</li>';
            }
        } else {
            echo '<li>No hay canciones disponibles en este momento.</li>';
        }
        ?>
    </ul>
</div>

<?php
require_once 'includes/footer.php';
?>
