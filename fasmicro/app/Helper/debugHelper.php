<?php

if (!function_exists('d')) {

    function d(
        mixed ...$data
    ): void {
        echo "<pre>";
        var_dump($data);
        echo "<pre>";
    }
}

if (!function_exists('dd')) {

    function dd(
        mixed ...$data
    ): void {
        echo "<pre>";
        var_dump($data);
        echo "<pre>";
        die();
    }
}