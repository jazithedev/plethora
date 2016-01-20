<?php

namespace Controller\Frontend;

use Model;
use Controller\Frontend;
use Plethora\DB;
use Plethora\Router;
use Plethora\View;
use \Plethora\View\ViewEntity;
use Plethora\Exception;

/**
 * Simple pages frontend controller.
 *
 * @author         Krzysztof Trzos
 * @package        pages
 * @subpackage     classes\Controller
 * @since          1.0.1-dev, 2015-04-11
 * @version        1.2.0-dev
 */
class Pages extends Frontend
{
    /**
     * ACTION - Particular page.
     *
     * @access   public
     * @return   View
     * @throws   Exception\Code404
     * @throws   Exception\Fatal
     * @since    1.0.1-dev, 2015-04-11
     * @version  1.2.0-dev
     */
    public function actionPage()
    {
        $query = DB::query('SELECT p FROM \Model\Page p WHERE p.rewrite = :rewrite');
        $query->param('rewrite', Router::getParam('rewrite'));

        $page = $query->single();

        if(!$page instanceof Model\Page) {
            throw new Exception\Code404('Page does not exist.');
        }

        $this->addBreadCrumb($page->getTitle());
        $this->setTitle($page->getTitle());
        $this->setDescription($page->getDescription());
        $this->setKeywords($page->getKeywords());

        $entityConfig = ViewEntity\Configurator::factory($page);
        $entityConfig->setFields(['content']);

        $viewEntity = ViewEntity::factory($entityConfig);

        return $viewEntity->getView();
    }

}
