<?php

$environmentFile = dirname(__DIR__) . '/.env';

if (is_file($environmentFile)) {
    $variables = parse_ini_file($environmentFile, false, INI_SCANNER_RAW);

    if ($variables === false) {
        throw new RuntimeException('Unable to parse backend/.env.');
    }

    foreach ($variables as $name => $value) {
        if (getenv($name) === false) {
            putenv($name . '=' . $value);
        }
    }
}
