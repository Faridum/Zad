<?php

require_once __DIR__ . '/environment.php';

$secret = getenv('JWT_SECRET');
if ($secret === false || strlen($secret) < 32) {
    throw new RuntimeException('JWT_SECRET must be configured with at least 32 characters.');
}

$expiresIn = getenv('JWT_EXPIRES_IN') ?: '604800';
if (!ctype_digit($expiresIn) || (int) $expiresIn < 1) {
    throw new RuntimeException('JWT_EXPIRES_IN must be a positive integer.');
}

return [
    'secret' => $secret,
    'expires_in' => (int) $expiresIn,
    'issuer' => getenv('JWT_ISSUER') ?: 'zad_backend',
    'audience' => getenv('JWT_AUDIENCE') ?: 'zad_app',
];
