<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    // Disco donde se guardan las fotos subidas (foto_url / logo_url).
    // En local alcanza con "public". En producción con disco efímero
    // (Railway, Heroku, etc.) usar "s3" u otro almacenamiento persistente,
    // si no las fotos se pierden en cada deploy.
    'uploads' => env('UPLOADS_DISK', 'public'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Listo para usar: poner UPLOADS_DISK=s3 y las credenciales AWS_*.
        // Requiere `composer require league/flysystem-aws-s3-v3`.
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
