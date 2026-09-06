<?php
// se guardan las reseñas en un archivo JSON para que sean permanentes
$archivo = "almacena.json";
// contraseña del admin
$contraseña_admin = "karuri0307";
// leer el archivo JSON si existe
if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $reseñas = json_decode($contenido, true);
    // comprobar que el JSON sea válido
    if (!is_array($reseñas)) {
        $reseñas = [];
    }
} else {
    // si el archivo no existe, crear un arreglo vacío
    $reseñas = [];
}
// agregar una nueva reseña si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // comprobar si hay una nueva reseña
    if (isset($_POST["accion"]) && $_POST["accion"] == "agregar") {
        // Recibe los datos
        $nombre = trim($_POST["name"] ?? "");
        $comentario = trim($_POST["review"] ?? "");
        $calificacion = (int)($_POST["rating"] ?? 0);
        // validar datos
        if ($nombre != "" && $comentario != "" && $calificacion >= 1 && $calificacion <= 5) {
            // crear nueva reseña
            $nueva_reseña = ["nombre" => $nombre,"comentario" => $comentario,"calificacion" => $calificacion];
            // agregar la reseña
            $reseñas[] = $nueva_reseña;
            // convertir a JSON
            $json = json_encode($reseñas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            // guardar en el archivo
            file_put_contents($archivo, $json);
        }
    }


    // eliminar una reseña si se envía el formulario de admin
    if (isset($_POST["accion"]) && $_POST["accion"] == "eliminar") {
        // recibir contraseña
        $contraseña = $_POST["contraseña"] ?? "";
        // recibir índice de la reseña
        $indice = isset($_POST["indice"])
            ? (int)$_POST["indice"]
            : -1;
        // comprobar contraseña
        if ($contraseña === $contraseña_admin && isset($reseñas[$indice])) {
            // eliminar reseña
            unset($reseñas[$indice]);
            // reorganizar los índices
            $reseñas = array_values($reseñas);
            // convertir de nuevo a JSON
            $json = json_encode($reseñas,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            // guardar cambios
            file_put_contents($archivo, $json);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas</title>
</head>
<body>
<h2>Opiniones de nuestros clientes</h2>
<?php
// mostrar reseñas
if (count($reseñas) > 0) {
    foreach ($reseñas as $indice => $reseña) {echo "<article>";
        // Nombre
        echo "<h3>"; echo htmlspecialchars($reseña["nombre"],ENT_QUOTES,"UTF-8"); echo "</h3>";
        // Calificación
        echo "<p>"; echo "<strong>Calificación:</strong> ";
        echo str_repeat("★",$reseña["calificacion"]);
        echo str_repeat("☆",5 - $reseña["calificacion"]);echo "</p>";
        // Comentario
        echo "<p>";echo htmlspecialchars($reseña["comentario"],ENT_QUOTES,"UTF-8");echo "</p>";


// para eliminar reseña 
        echo "<details>";echo "<summary>Administración</summary>";
        echo "<br>";
        echo "<form action='modifica.php' method='post'>";
         // cual eliminar
        echo "<input type='hidden' name='accion' value='eliminar'>";
        // guardar el índice
        echo "<input type='hidden' name='indice' value='" . $indice . "'>";
        // contraseña
        echo "<label for='contraseña_" . $indice . "'>";echo "Contraseña de administrador:";echo "</label>";
        echo "<br>";
        echo "<input type='password' id='contraseña_" . $indice . "' name='contraseña' required>";
        echo "<br><br>";
        // botón eliminar
        echo "<input type='submit' value='Eliminar reseña'>";
        echo "</form>";
        echo "</details>";
        echo "</article>";
        echo "<hr>";
    }
} else {echo "<p>Aún no hay reseñas.</p>";
}

?>
</body>
</html>
```
