<?php

// Router para el servidor built-in de PHP.
// Sirve archivos estáticos (fotos en /storage) directamente y delega
// el resto de las peticiones a Laravel (public/index.php).

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false; // PHP sirve el archivo real con su Content-Type correcto
}

require __DIR__ . '/index.php';
