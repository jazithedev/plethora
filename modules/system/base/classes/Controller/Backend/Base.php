<?php

namespace Controller\Backend;

use Controller\Backend;
use Plethora\View;

/**
 * Main backend controller for the "base" module.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     controller/backend
 * @version        1.0.0-alpha
 */
class Base extends Backend
{
    /**
     * Return the whole body of the changelog.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionChangelog()
    {
        $this->addToTitle('Plethora Changelog');
        $this->addBreadCrumb('Plethora Changelog');

        $sPathToChangelog   = PATH_ROOT.'framework'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Plethora'.DIRECTORY_SEPARATOR.'changelog.html';
        $sChangelogHtmlPage = file_get_contents($sPathToChangelog);
        $sChangelogBody     = str_replace('&', '&amp;', $sChangelogHtmlPage);

        return View::factory('base/content')
            ->bind('sContent', $sChangelogBody);
    }
}