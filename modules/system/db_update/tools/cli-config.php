<?php

// See :doc:`Configuration <../reference/configuration>` for up to date autoloading details.
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

define('DS', DIRECTORY_SEPARATOR);

$sPathFromArgv	 = explode(DS, $argv[0]);
$sAppName		 = $sPathFromArgv[array_search('public_html', $sPathFromArgv) + 1];

define('APPLICATION', $sAppName);
define('PATH_MODULES', PATH_ROOT.'modules/');
define('PATH_APP', PATH_ROOT.'application/'.APPLICATION.'/'); // App path

require_once PATH_ROOT."framework/core/Plethora/ModelCore/ModelInterface.php";
require_once PATH_ROOT."framework/core/Plethora/Model.php";
require_once PATH_ROOT."framework/core/Plethora/ModelCore/FileBroker.php";
require_once PATH_ROOT."framework/core/Plethora/ModelCore/Locales.php";

function scanDirectoryForModels($sPath, &$aModels) {
	if(file_exists($sPath) && is_dir($sPath)) {
		$aModels[] = $sPath.DS;

		foreach(scandir($sPath) as $sFileName) {
			if(!in_array($sFileName, array('.', '..'))) {
				scanDirectoryForModels($sPath.DS.$sFileName, $aModels);
			}
		}
	}
}

// Database config
$aDatabaseConfig = include_once PATH_APP.'config/database.php';

$aDbParams					 = $aDatabaseConfig['config']['development'];
$aDbParams['charset']		 = 'utf8';
$aDbParams['driverOptions']	 = array(1002 => 'SET NAMES utf8');

// Create array of models consisting all existing models in project
$aModules		 = include_once PATH_APP.'config/modules.php';
$aModelsPaths	 = [];

foreach($aModules as $sGroupName => $aGroup) {
	foreach($aGroup as $sModuleName => $aModule) {
		$sPathStart = PATH_ROOT.'modules'.DS.$sGroupName.DS.$sModuleName.DS.'classes'.DS.'Model';

		scanDirectoryForModels($sPathStart, $aModelsPaths);
	}
}

// Create a simple "default" Doctrine ORM configuration for XML Mapping
$oConfig = Setup::createAnnotationMetadataConfiguration($aModelsPaths, TRUE);

// database configuration parameters
//$aDbParams = array(
//	'driver'		 => 'pdo_mysql',
//	'dbname'		 => APPLICATION,
//	'user'			 => 'root',
//	'password'		 => '',
//	'charset'		 => 'utf8',
//	'driverOptions'	 => array(1002 => 'SET NAMES utf8')
//);

$entityManager = EntityManager::create($aDbParams, $oConfig);
