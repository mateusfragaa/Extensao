<?php

function formatDate(?string $date, string $format = 'd/m/Y'): string
{
    if (empty($date)) {
        return '';
    }

    try {
        return (new DateTime($date))->format($format);
    } catch (Exception $e) {
        return '';
    }
}