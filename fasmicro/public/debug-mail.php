<?php
define('PATHAPP', dirname(__DIR__) . DIRECTORY_SEPARATOR);

echo "PATHAPP: " . PATHAPP . "\n";
echo "Caminho do .env: " . PATHAPP . '.env' . "\n";
echo "Arquivo existe? " . (file_exists(PATHAPP . '.env') ? 'SIM' : 'NAO') . "\n";

$resultado = parse_ini_file(PATHAPP . '.env', true);
echo "parse_ini_file retornou: ";
var_dump($resultado);