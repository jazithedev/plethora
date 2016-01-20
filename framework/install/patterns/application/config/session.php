<?php

return [
    'driver'      => 'file',
    'path'        => PATH_APP.'/sessions/',
    'cookie_name' => 'session',
    'expire'      => 60 * 60 * 24,
    'validate'    => ['user_agent', 'ip_address'],
];
