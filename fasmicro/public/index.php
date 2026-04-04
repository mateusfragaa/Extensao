<?php

    session_start();

    use Core\Library\Ambiente;
    use Core\Library\Routes;

    // PATH padrão da aplicação
    defined('PATHAPP') || define("PATHAPP", ".." . DIRECTORY_SEPARATOR);

    // Carregando a Vendor e as Constantes
    require_once ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
    require_once ".." . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "Config" . DIRECTORY_SEPARATOR . "Constants.php";

    // seta timezone da aplicação
    date_default_timezone_set(DEFAULT_TIME_ZONE);

    // instancinando classe ambinte e rota
    $ambiente = new Ambiente();
    $routes = new Routes();

    // Carregando configurações de ambiente da aplicação
    $ambiente->load();

    // Chamar a rota
    $routes->rota(); 
