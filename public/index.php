<?php

// URL de la página a la que voy a hacer la solicitud
$url = 'https://www.baloto.com/colorloto';

// Función para realizar la solicitud HTTP
function obtenerContenido($url) {
    // Inicializo cURL para hacer la solicitud
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Agrego un User-Agent para evitar un error 403 Forbidden
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, Gecko) Chrome/58.0.3029.110 Safari/537.36');

    // Ejecuto la solicitud y obtengo el resultado
    $resultado = curl_exec($ch);
    // Cierro la sesión cURL
    curl_close($ch);
    return $resultado;
}

// Función para extraer los datos del HTML
function extraerDatos($html) {
    $dom = new DOMDocument;
    // Cargo el HTML en el DOMDocument
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // Selecciono el contenedor principal de las balotas
    $contenedorBalotas = $xpath->query('//div[@id="balotasContainer"]');


    // Estructura para almacenar los datos extraídos
    $datos = [
        'balotas' => [],
        'numeroSorteo' => extraerNumeroSorteo($xpath),
        'fechaSorteo' => extraerFechaSorteo($xpath),
        'fechaProximoSorteo' => extraerFechaProximoSorteo($xpath),
    ];

    // Si encuentro el contenedor, extraigo las balotas
    if ($contenedorBalotas->length > 0) {
        // Extraigo todas las balotas dentro del contenedor
        $balotas = $xpath->query('.//div[contains(@class, "balota")]', $contenedorBalotas->item(0));

        foreach ($balotas as $balota) {
            $datos['balotas'][] = trim($balota->nodeValue);
        }
    }

    return $datos;
}

// Función para extraer el número de sorteo
function extraerNumeroSorteo($xpath) {
    $nodos = $xpath->query('//div[contains(@class, "white-color") and contains(@class, "fs-3")]/text()');
    if ($nodos->length > 0) {
        $texto = trim($nodos->item(0)->nodeValue);
        $partes = explode(' - ', $texto);
        return $partes[0] ?? 'Desconocido';
    }
    return 'Desconocido';
}

// Función para extraer la fecha del sorteo
function extraerFechaSorteo($xpath) {
    $nodos = $xpath->query('//div[contains(@class, "white-color") and contains(@class, "fs-3")]/text()');
    if ($nodos->length > 0) {
        $texto = trim($nodos->item(0)->nodeValue);
        $partes = explode(' - ', $texto);
        return $partes[1] ?? 'Desconocida';
    }
    return 'Desconocida';
}

function extraerFechaProximoSorteo($xpath){
    $proximoSorteo = $xpath->query('//div[contains(@class, "fs-2") and contains(@class, "white-color") and contains(@class, "nunito-black") and contains(@class, "mt-4")]/text()');
    if ($proximoSorteo->length > 0) {
        $texto = trim($proximoSorteo->item(0)->nodeValue);

        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];

        $subcadena = "";

        foreach ($dias as $dia) {
            $posicion = strpos($texto, $dia);
            if ($posicion !== false) {
                // Si encuentro uno de los días, extraigo la subcadena y salgo del bucle
                $subcadena = substr($texto, $posicion);
                break;
            }
        }
        
        if ($subcadena) {
            return $subcadena;  // Imprime la subcadena comenzando desde el día encontrado
        } else {
            echo "No se encontró ningún día de la semana en el texto.";
        }
    }
}



// Obtengo el contenido de la página
$html = obtenerContenido($url);

// Extraigo los datos del contenido HTML
$datos = extraerDatos($html);

// Me aseguro de no haber enviado ningún contenido antes de enviar los encabezados JSON
if (!headers_sent()) {
    header('Content-Type: application/json');
}

// Imprimo los datos en formato JSON
echo json_encode($datos, JSON_PRETTY_PRINT);

?>
