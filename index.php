<?php
$anyo = date('Y');

if ($_POST) {
    $ciudad = $_POST["ciudad"];
    $ciudad = trim($ciudad);
    $ciudad = urlencode($ciudad);
    // $API_KEY = "57dd0d67b602215d88c9f993d422a09b";
    $API_KEY = "2773a9b09a08c0d8fb33c6a7b7edbae0";
    $units = "metric";
    $lang = "es";
    $URL = "https://api.openweathermap.org/data/2.5/weather?q=$ciudad&appid=$API_KEY&units=$units&lang=$lang";
    

    // Obtener los datos de la API y silenciar errores con @
    $stringRespuesta = @file_get_contents($URL);
    
    // Verificar si hay respuesta válida
    if ($stringRespuesta === FALSE) {
        $error = "No se pudo obtener información para la ciudad ingresada. Verifique el nombre y vuelva a intentar.";
    } else {
        $datos = json_decode($stringRespuesta, true);

        // Extraer la información relevante que queremos mostrar si la ciudad es válida
        if ($datos['cod'] == 200) {
            $descripcion = $datos["weather"][0]["description"];
            $icono = $datos["weather"][0]["icon"];
            $temperaturaActual = round($datos["main"]["temp"]);
            $sensacionReal = round($datos["main"]["feels_like"]);
            $temperaturaMin = round($datos["main"]["temp_min"]);
            $temperaturaMax = round($datos["main"]["temp_max"]);
            $humedad = $datos["main"]["humidity"];
            $puntoRocio = round($datos["main"]["temp"]);
            $presion = $datos["main"]["pressure"];
            $nubosidad = $datos["clouds"]["all"];
            $viento = $datos["wind"]["speed"];
            $rafagasViento = $datos["wind"]["deg"];
            $visibilidad = $datos["visibility"] / 1000; // Convertir de metros a kilómetros
            $techoNubes = $datos["clouds"]["all"];
            $nivelMar = $datos["main"]["sea_level"];
            $amanecer = date('H:i',$datos["sys"]["sunrise"]);
            $atardecer = date('H:i', $datos["sys"]["sunset"]);
            $fecha = date('l, d F Y H:i');
        } else {
            // Si no se encuentra la ciudad o hay error
            $error = "No existen datos para la ciudad solicitada.";
        }
    }
}

// Función para determinar la estación del año
function obtenerEstacion() {
    $mes = date('n');
    if ($mes >= 3 && $mes <= 5) {
        return "primavera";
    } elseif ($mes >= 6 && $mes <= 8) {
        return "verano";
    } elseif ($mes >= 9 && $mes <= 11) {
        return "otoño";
    } else {
        return "invierno";
    }
}

$estacion = obtenerEstacion();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="David Gutierrez">
    <title>Clima en la Ciudad</title>
    <link rel="icon" href="img/clima.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="container <?= $estacion ?>">
        <div class="titulo">
            <h1>¿Qué tiempo hace?</h1>
        </div>
        <form method="POST">
            <label for="ciudad" class="container <?= $estacion ?>">Introduce una ciudad:</label>
            <input type="text" name="ciudad" id="ciudad" placeholder="Ciudad, País" required>
            <button type="submit">Buscar</button>
        </form>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <p><?= $error ?></p>
            </div>
        <?php elseif ($_POST && isset($descripcion)): ?>
            <div class="weather-card">
                <h2>Clima en <?= urldecode($ciudad) ?></h2>
                <p><?= $fecha ?></p>
                <div class="weather-info">
                    <div class="temp-icon">
                        <img src="img/<?= $icono ?>.svg" alt="<?= $descripcion ?>">
                        <span class="temp"><?= $temperaturaActual ?>°C</span>
                    </div>
                    <div class="details">
                        <p><strong>Estado del cielo:</strong> <?= ucfirst($descripcion) ?></p>
                        <p><strong>Sensación Real:</strong> <?= $sensacionReal ?>°C</p>
                        <p><strong>Temperatura Mín.:</strong> <?= $temperaturaMin ?>°C</p>
                        <p><strong>Temperatura Máx.:</strong> <?= $temperaturaMax ?>°C</p>
                        <p><strong>Ráfagas de viento:</strong> <?= $rafagasViento ?> km/h</p>
                        <p><strong>Humedad:</strong> <?= $humedad ?>%</p>
                        <p><strong>Punto de Rocío:</strong> <?= $puntoRocio ?>°C</p>
                        <p><strong>Presión:</strong> <?= $presion ?> mb</p>
                        <p><strong>Nubosidad:</strong> <?= $nubosidad ?>%</p>
                        <p><strong>Visibilidad:</strong> <?= $visibilidad ?> km</p>
                        <p><strong>Techo de nubes:</strong> <?= $techoNubes ?> m</p>
                    </div>
                </div>                  
                <div class="sun-info">
                    <div class="sun-item">
                        <img src="img/sunrise.svg" alt="Icono de salida del sol">
                        <p><strong>Salida del Sol:</strong> <?= $amanecer ?></p>
                    </div>
                    <div class="sun-item">
                        <img src="img/sunset.svg" alt="Icono de puesta del sol">
                        <p><strong>Puesta del Sol:</strong> <?= $atardecer ?></p>
                    </div>
                </div>
            </div>
            <div>
                <br>
                <label for="ciudad" class="container <?= $estacion ?>">&copy Dagura <?= $anyo ?>    Datos de Openweather</label>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>