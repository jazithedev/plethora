<?php

use Plethora\Router\LocalActions;

LocalActions::addLocalAction(__('Reload cache'), 'backend', 'backend')
    ->setConditions([
        'controller' => 'i18n',
        'action'     => 'index',
    ])
    ->setParameters([
        'controller' => 'i18n',
        'action'     => 'reloadcache',
    ])
    ->setIcon('refresh');