<?php
require_once 'includes/header.php';

// --- Verificación de Seguridad ---
// 1. Comprobar si el usuario ha iniciado sesión.
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// 2. Comprobar si el usuario es un 'artista'.
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'artista') {
    // Si no es un artista, redirigir a la página de inicio con un mensaje de error.
    $_SESSION['error_message'] = "Acceso denegado. Esta página es solo para artistas.";
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// --- Obtener el ID del Artista ---
// Necesitamos encontrar el ID del artista basado en el ID del usuario.
$id_usuario = $_SESSION['user_id'];
$sql_artista = "SELECT id FROM artistas WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_artista);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $artista = $result->fetch_assoc();
    $id_artista = $artista['id'];
} else {
    // Manejar un caso improbable donde el usuario es 'artista' pero no tiene entrada en la tabla artistas.
    echo "<p>Error: No se encontró el perfil del artista.</p>";
    require_once 'includes/footer.php';
    exit();
}
$stmt->close();
?>

<style>
/* Estilos para el dashboard */
.dashboard-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.dashboard-section {
    background-color: var(--color-surface);
    padding: 30px;
    border-radius: 12px;
}

.dashboard-section h2 {
    margin-top: 0;
    border-bottom: 2px solid var(--color-primary);
    padding-bottom: 10px;
}
</style>

<h1>Panel del Artista</h1>
<p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>. Desde aquí puedes gestionar tu música.</p>

<div class="dashboard-container">

<?php
// --- Lógica para Crear Álbum ---
$album_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_album'])) {
    $album_titulo = $_POST['album_titulo'];

    // Procesar la imagen de portada
    if (isset($_FILES['album_portada']) && $_FILES['album_portada']['error'] === 0) {
        $upload_dir = 'img/';
        $file_name = uniqid() . '-' . basename($_FILES['album_portada']['name']);
        $target_path = $upload_dir . $file_name;

        // Mover el archivo subido
        if (move_uploaded_file($_FILES['album_portada']['tmp_name'], $target_path)) {
            $portada_url = $target_path;

            // Insertar en la base de datos
            $sql_insert = "INSERT INTO albumes (id_artista, titulo, portada_url, fecha_lanzamiento) VALUES (?, ?, ?, CURDATE())";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iss", $id_artista, $album_titulo, $portada_url);

            if ($stmt_insert->execute()) {
                $album_message = "Álbum creado con éxito.";
            } else {
                $album_message = "Error al crear el álbum: " . $conn->error;
            }
            $stmt_insert->close();
        } else {
            $album_message = "Error al subir la imagen de portada.";
        }
    } else {
        $album_message = "Debes seleccionar una imagen de portada.";
    }
}
?>

    <!-- Sección para crear álbumes (Paso 2 del plan) -->
    <div class="dashboard-section" id="create-album">
        <h2>Crear Nuevo Álbum</h2>

        <?php if (!empty($album_message)): ?>
            <p><?php echo $album_message; ?></p>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>dashboard.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="album_titulo">Título del Álbum</label>
                <input type="text" id="album_titulo" name="album_titulo" required>
            </div>
            <div class="form-group">
                <label for="album_portada">Portada del Álbum</label>
                <input type="file" id="album_portada" name="album_portada" accept="image/*" required>
            </div>
            <div class="form-group">
                <button type="submit" name="create_album">Crear Álbum</button>
            </div>
        </form>
    </div>

<?php
// --- Lógica para Subir Canción ---
$song_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_song'])) {
    $song_titulo = $_POST['song_titulo'];
    $id_album = $_POST['id_album'];

    // Validar que el álbum pertenezca al artista (seguridad)
    $sql_verify = "SELECT id FROM albumes WHERE id = ? AND id_artista = ?";
    $stmt_verify = $conn->prepare($sql_verify);
    $stmt_verify->bind_param("ii", $id_album, $id_artista);
    $stmt_verify->execute();
    if ($stmt_verify->get_result()->num_rows === 0) {
        $song_message = "Error: El álbum seleccionado no es válido.";
    } else {
        // Procesar archivos si la validación es correcta
        $portada_url = '';
        $mp3_url = '';

        // Subir portada de la canción
        if (isset($_FILES['song_portada']) && $_FILES['song_portada']['error'] === 0) {
            $upload_dir_img = 'img/';
            $img_name = uniqid() . '-' . basename($_FILES['song_portada']['name']);
            if (move_uploaded_file($_FILES['song_portada']['tmp_name'], $upload_dir_img . $img_name)) {
                $portada_url = $upload_dir_img . $img_name;
            }
        } else {
            $song_message = "Error al subir la portada de la canción.";
        }

        // Subir archivo MP3
        if (empty($song_message) && isset($_FILES['song_mp3']) && $_FILES['song_mp3']['error'] === 0) {
            $upload_dir_audio = 'audio/';
            $mp3_name = uniqid() . '-' . basename($_FILES['song_mp3']['name']);
            if (move_uploaded_file($_FILES['song_mp3']['tmp_name'], $upload_dir_audio . $mp3_name)) {
                $mp3_url = $upload_dir_audio . $mp3_name;
            }
        } else {
             $song_message = "Error al subir el archivo MP3.";
        }

        // Si ambos archivos se subieron, insertar en la base de datos
        if (!empty($portada_url) && !empty($mp3_url)) {
            $sql_insert = "INSERT INTO canciones (id_album, id_artista, titulo, archivo_mp3, portada_url) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iisss", $id_album, $id_artista, $song_titulo, $mp3_url, $portada_url);
            if ($stmt_insert->execute()) {
                $song_message = "Canción subida con éxito.";
            } else {
                $song_message = "Error al registrar la canción: " . $conn->error;
            }
            $stmt_insert->close();
        }
    }
    $stmt_verify->close();
}


// Obtener los álbumes del artista para el menú desplegable
$sql_albums = "SELECT id, titulo FROM albumes WHERE id_artista = ?";
$stmt_albums = $conn->prepare($sql_albums);
$stmt_albums->bind_param("i", $id_artista);
$stmt_albums->execute();
$albums_result = $stmt_albums->get_result();
?>

    <!-- Sección para subir canciones (Paso 3 del plan) -->
    <div class="dashboard-section" id="upload-song">
        <h2>Subir Nueva Canción</h2>

        <?php if (!empty($song_message)): ?>
            <p><?php echo $song_message; ?></p>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>dashboard.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="song_titulo">Título de la Canción</label>
                <input type="text" id="song_titulo" name="song_titulo" required>
            </div>
            <div class="form-group">
                <label for="id_album">Álbum de Pertenencia</label>
                <select id="id_album" name="id_album" required>
                    <option value="">Selecciona un álbum...</option>
                    <?php
                    if ($albums_result->num_rows > 0) {
                        while ($album = $albums_result->fetch_assoc()) {
                            echo '<option value="' . $album['id'] . '">' . htmlspecialchars($album['titulo']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="song_portada">Portada de la Canción</label>
                <input type="file" id="song_portada" name="song_portada" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="song_mp3">Archivo MP3</label>
                <input type="file" id="song_mp3" name="song_mp3" accept="audio/mpeg" required>
            </div>
            <div class="form-group">
                <button type="submit" name="upload_song">Subir Canción</button>
            </div>
        </form>
    </div>

<?php
// --- Lógica para Mostrar Contenido del Artista ---
// Obtener todos los álbumes del artista con sus canciones
$sql_content = "SELECT
                    al.titulo as album_titulo,
                    c.titulo as cancion_titulo,
                    c.id as cancion_id
                FROM albumes al
                LEFT JOIN canciones c ON al.id = c.id_album
                WHERE al.id_artista = ?
                ORDER BY al.titulo, c.titulo";

$stmt_content = $conn->prepare($sql_content);
$stmt_content->bind_param("i", $id_artista);
$stmt_content->execute();
$content_result = $stmt_content->get_result();

$artist_music = [];
if ($content_result->num_rows > 0) {
    while ($row = $content_result->fetch_assoc()) {
        $artist_music[$row['album_titulo']][] = $row;
    }
}
$stmt_content->close();
?>

    <!-- Sección para mostrar contenido (Paso 4 del plan) -->
    <div class="dashboard-section" id="my-music" style="grid-column: span 2;">
        <h2>Mi Música Publicada</h2>

        <?php if (empty($artist_music)): ?>
            <p>Aún no has publicado nada. ¡Crea un álbum y sube tu primera canción!</p>
        <?php else: ?>
            <?php foreach ($artist_music as $album_titulo => $canciones): ?>
                <div class="artist-album-view">
                    <h3>Álbum: <?php echo htmlspecialchars($album_titulo); ?></h3>
                    <ul>
                        <?php
                        if (!is_null($canciones[0]['cancion_id'])):
                            foreach ($canciones as $cancion): ?>
                                <li>
                                    <a href="<?php echo BASE_URL . 'play.php?id=' . $cancion['cancion_id']; ?>">
                                        <?php echo htmlspecialchars($cancion['cancion_titulo']); ?>
                                    </a>
                                </li>
                            <?php endforeach;
                        else: ?>
                            <li>Este álbum aún no tiene canciones.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php
require_once 'includes/footer.php';
?>
