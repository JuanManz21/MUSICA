<?php require_once 'includes/header.php'; ?>

<?php
// Consulta para obtener todas las canciones con el nombre del artista
$sql = "SELECT c.id, c.titulo, c.portada_url, a.nombre_artista
        FROM canciones c
        JOIN artistas a ON c.id_artista = a.id
        ORDER BY RAND()"; // Orden aleatorio para que se vea diferente cada vez

$result = $conn->query($sql);
$canciones = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $canciones[] = $row;
    }
}
?>

<div class="home-container">
    <h2>Canciones del Momento</h2>

    <!-- Hilera 1: De derecha a izquierda -->
    <div class="scrolling-wrapper-container">
        <div class="scrolling-wrapper right-to-left">
            <?php
            // Duplicamos el array para un scroll infinito
            $canciones_duplicadas = array_merge($canciones, $canciones);
            foreach ($canciones_duplicadas as $cancion) {
                echo '<div class="song-item">';
                echo '  <a href="play.php?id=' . $cancion['id'] . '">';
                echo '    <img src="' . htmlspecialchars($cancion['portada_url']) . '" alt="' . htmlspecialchars($cancion['titulo']) . '">';
                echo '    <p class="song-title">' . htmlspecialchars($cancion['titulo']) . '</p>';
                echo '    <p class="artist-name">' . htmlspecialchars($cancion['nombre_artista']) . '</p>';
                echo '  </a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Hilera 2: De izquierda a derecha -->
    <div class="scrolling-wrapper-container">
        <div class="scrolling-wrapper left-to-right">
            <?php
            // Usamos el mismo array duplicado
             foreach ($canciones_duplicadas as $cancion) {
                echo '<div class="song-item">';
                echo '  <a href="play.php?id=' . $cancion['id'] . '">';
                echo '    <img src="' . htmlspecialchars($cancion['portada_url']) . '" alt="' . htmlspecialchars($cancion['titulo']) . '">';
                echo '    <p class="song-title">' . htmlspecialchars($cancion['titulo']) . '</p>';
                echo '    <p class="artist-name">' . htmlspecialchars($cancion['nombre_artista']) . '</p>';
                echo '  </a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
