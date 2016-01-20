<?php

return [
    'dashboard'  => [
        'parent'   => 'system',
        'url'      => \Plethora\Route::backendUrl('dashboard'),
        'priority' => 1,
    ],
    'run_cron'   => [
        'parent'   => 'system',
        'url'      => \Plethora\Route::backendUrl('cron', 'runCron'),
        'priority' => 2,
    ],
    'files_list' => [
        'parent'   => 'system',
        'url'      => \Plethora\Route::backendUrl('files', 'list'),
        'priority' => 3,
    ],
];