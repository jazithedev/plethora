<?php

$config               = [];
$config['transport']  = 'smtp';
$config['cache_path'] = PATH_CACHE.'mailer'.DS;

/** For SMTP **/
$config['hostname'] = 'mail.domain.pl';
$config['port']     = 587;
$config['auth']     = TRUE;
$config['username'] = 'mail@domain.pl';
$config['password'] = 'password123';
$config['timeout']  = 5;
$config['crypto']   = NULL; // null, ssl or tls
$config['newline']  = "\r\n"; // \n or \r\n

$config['queue'] = 5; // How many emails will be sended with Mailer::sendQueue();

return $config;