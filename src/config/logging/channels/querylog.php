<?php
return [
    'driver' => 'custom',
    'via' => \programming_cat\QueryLog\Services\CreateQueryLogger::class,
    'path' => storage_path('logs/query.log'),
    'level' => 'debug',
    'days' => 30,
];
