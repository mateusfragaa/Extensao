<?php

function formatNumber(
    float|int|null $value,
    int $decimals = 2,
    string $decimalSeparator = ',',
    string $thousandsSeparator = '.'
): string {
    if ($value === null) {
        return '';
    }

    return number_format(
        $value,
        $decimals,
        $decimalSeparator,
        $thousandsSeparator
    );
}