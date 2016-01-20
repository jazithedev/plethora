<?php

namespace PlethoraInstall;

use Doctrine;

/**
 * Main class used to update database of a particular aplication which files
 * have been prepared earlier.
 *
 * @author     Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @package    PlethoraInstall
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
class Database {

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Database();
    }

    /**
     * Make database update process.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function update() {
        // add ORM autoloader
        require_once PATH_ROOT.'vendor/autoload.php';

        // Database config
        $aDatabaseConfig = include_once PATH_APP.'config/database.php';

        $aDbParams                  = $aDatabaseConfig['config']['development'];
        $aDbParams['charset']       = 'utf8';
        $aDbParams['driverOptions'] = [1002 => 'SET NAMES utf8'];

        // Create array of models consisting all existing models in project
        $aModules = include_once PATH_APP.'config/modules.php';
//		$aModelsPaths		 = [];
        $aModelsClassesPaths = [];

        foreach($aModules as $sGroupName => $aGroup) {
            foreach(array_keys($aGroup) as $sModuleName) {
                $sPathStart = PATH_ROOT.'modules'.DS.$sGroupName.DS.$sModuleName.DS.'classes'.DS.'Model';

//				$this->scanDirectoryForModels($sPathStart, $aModelsPaths, $aModelsClassesPaths);
                $this->scanDirectoryForModels($sPathStart, $aModelsClassesPaths);
            }
        }

        // Create a simple "default" Doctrine ORM configuration for XML Mapping
//		$oConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($aModelsPaths, TRUE);
        $oConfig = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([], TRUE);

        // database configuration parameters
        $oEntityManager = Doctrine\ORM\EntityManager::create($aDbParams, $oConfig);

        // tool
        $oTool    = new Doctrine\ORM\Tools\SchemaTool($oEntityManager);
        $aClasses = [];

        foreach($aModelsClassesPaths as $sPath) {
            require_once $sPath;
        }

        foreach($aModelsClassesPaths as $sPath) {
            $iModelPos = strpos($sPath, DS.'Model');
            $sClass    = str_replace(['.php', DS], ['', '\\'], substr($sPath, $iModelPos));

            $aClasses[] = $oEntityManager->getClassMetadata($sClass);
        }

        $oTool->updateSchema($aClasses);

        return 'ok';
    }

    /**
     * @access   private
     * @param    string $sPath
     * @param    array  $aModelsClassesPaths
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function scanDirectoryForModels($sPath, &$aModelsClassesPaths) {
//	private function scanDirectoryForModels($sPath, &$aModelsPaths, &$aModelsClassesPaths) {
        if(file_exists($sPath) && is_dir($sPath)) {
//			$aModelsPaths[] = $sPath.DS;

            foreach(scandir($sPath) as $sFileName) {
                if(!in_array($sFileName, ['.', '..'])) {
                    $this->scanDirectoryForModels($sPath.DS.$sFileName, $aModelsClassesPaths);
//					$this->scanDirectoryForModels($sPath.DS.$sFileName, $aModelsPaths, $aModelsClassesPaths);
                }
            }
        } elseif(file_exists($sPath) && !is_dir($sPath)) {
            $aModelsClassesPaths[] = $sPath;
        }
    }

}
