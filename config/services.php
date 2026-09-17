<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    // Wa.me no requiere credenciales (son links). Si más adelante se evalúa
    // WhatsApp Business API, sus credenciales van acá.
    'whatsapp' => [
        'business_number' => env('WHATSAPP_BUSINESS_NUMBER'),
    ],

];
