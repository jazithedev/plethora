<?php

use Plethora\Route;
use Plethora\Router;

# home
Router::addRoute('home', '/');

# 404
Router::addRoute('err404', '/err404')
    ->setController('Frontend\Error404');

# cron
Router::addRoute('cron', '/cron/{token}')
    ->setController('Cron');

# cron - clear temp directory
Router::addRoute('cron_clear_temp', '/cron_clear_temp')
    ->setController('Cron')
    ->setAction('ClearTemp');

# changelog
Router::addRoute('framework_changelog', '/fw/changelog')
    ->setController('Base')
    ->setAction('Changelog')
    ->addDefault('package', 'Backend');

# backend
Router::addRoute('backend', '/a(/{controller}(/{action}(/{id}(/{extra}))))')
    ->setController('Dashboard')
    ->addParameterType('id', '[a-zA-Z0-9]+')
    ->addParameterType('extra', '[a-zA-Z0-9]+')
    ->addParameterType('controller', '[a-zA-Z0-9_]+')
    ->addParameterType('action', '[a-zA-Z0-9_]+')
    ->addDefault('package', 'Backend')
    ->addDefault('css', 'backend')
    ->addDefault('id', NULL)
    ->addDefault('extra', NULL)
    ->addAccessFunction(function (Route $oRoute, array $aParams = []) {
        $sClass      = '\Controller\Backend\\'.str_replace('_', '\\', ucfirst($aParams['controller']));
        $sPrefix     = call_user_func([$sClass, 'getPermissionPrefix']);
        $sPermission = $sPrefix.$aParams['action'];

        return UserPermissions::hasPerm($sPermission);
    });

# AJAX
Router::addRoute('ajax', '/ajax(/{controller}(/{action}(/{id}(/{extra}))))')
    ->setController('Dashboard')
    ->addParameterType('id', '[a-zA-Z0-9]+')
    ->addParameterType('extra', '[a-zA-Z0-9]+')
    ->addParameterType('controller', '[a-zA-Z0-9_]+')
    ->addParameterType('action', '[a-zA-Z0-9_]+')
    ->addDefault('package', 'Ajax')
    ->addDefault('css', 'backend')
    ->addDefault('id', NULL)
    ->addDefault('extra', NULL)
    ->addAccessFunction(function (Route $oRoute, array $aParams = []) {
        $sClass      = '\Controller\Backend\\'.str_replace('_', '\\', ucfirst($aParams['controller']));
        $sPrefix     = call_user_func([$sClass, 'getPermissionPrefix']);
        $sPermission = $sPrefix.$aParams['action'];

        return UserPermissions::hasPerm($sPermission);
    });
