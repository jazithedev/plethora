<?php

namespace Controller\Backend;

use Doctrine\ORM;
use Controller\Backend;
use Plethora\Cache;
use Plethora\DB;
use Plethora\Form;
use Plethora\Router;
use Plethora\Session;
use Plethora\View;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @package          db_update
 * @subpackage       classes\controller
 * @version          1.2.0-dev
 */
class DbUpdate extends Backend
{

    /**
     * Default action for database updating.
     *
     * @access   public
     * @return   View
     * @since    2014-08-17
     * @version  1.2.0-dev
     */
    public function actionDefault()
    {
        $this->addToTitle('Database updating module');

        // create update form
        $oForm = new Form('db_update');
        $oForm->setSubmitValue(__('make update'));

        // check if update button has been clicked
        if($oForm->isSubmittedAndValid()) {
            $sUpdateOutput = static::makeUpdateNoExec();

            Cache::set($sUpdateOutput, 'output', 'dbupdate');
            Session::flash(Router::getCurrentUrl(), __('Database updated successfully.'));
        }

        // return View
        return View::factory('db_update/backend/default')
            ->bind('oForm', $oForm);
    }

//	/**
//	 * Update database by external script.
//	 * 
//	 * @static
//	 * @access	public
//	 * @return	\Plethora\View
//	 * @since	2014-08-17
//	 * @version	1.1.0-dev, 2015-06-27
//	 */
//	private static function makeUpdate() {
//		$sModulePath = \Plethora\Router::getModulePath('db_update');
//		$sOldPath	 = getcwd();
//		$sPath		 = $sModulePath.DS.'tools'.DS.'doctrine';
//		$sCmd		 = 'php '.$sPath.' orm:schema-tool:update --force';
//		$sCmdDump	 = 'php '.$sPath.' orm:schema-tool:update --dump-sql';
//
//		chdir($sModulePath);
//		$sOutput = '-----------------<br />QUERIES:<br />-----------------<br />'.shell_exec($sCmdDump.'  2>&1');
//		$sOutput.= '<br />-----------------<br />MAIN OUTPUT:<br />-----------------<br />'.shell_exec($sCmd.'  2>&1');
//		chdir($sOldPath);
//
//		return $sOutput;
//	}

    /**
     * Update database.
     *
     * @static
     * @access   public
     * @return   View
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    private static function makeUpdateNoExec()
    {
        $entityManager = DB::getEntityManager();
        $tool          = new ORM\Tools\SchemaTool($entityManager);
        $classes       = [];

        // get list of Model classes
        foreach(DB::getModelsNames() as $sClass) {
            $classes[] = $entityManager->getClassMetadata($sClass);
        }

        // make schemas update
        try {
            $sql = $tool->getUpdateSchemaSql($classes);
            /* @var $sql array */

            $tool->updateSchema($classes);

            $output = View::factory('db_update/backend/update_output')
                ->bind('aSQL', $sql)
                ->renderAndMinify();
        } catch(\Exception $e) {
            $output = __('Error').': '.$e->getMessage();
        }

        // return output
        return $output;
    }

}
