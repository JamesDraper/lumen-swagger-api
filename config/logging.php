<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    'default' => 'daily',
    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path'   => __DIR__ . '/../var/lumen.log',
            'level'  => 'debug',
            'days'   => 14,
        ],
    ],
];
