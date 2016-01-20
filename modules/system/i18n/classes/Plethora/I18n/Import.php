<?php
namespace Plethora\I18n;

use Model;
use Plethora\Exception;
use Plethora\Helper;
use Plethora\Router;

/**
 * Class used to import translations located in locale files from
 * Plethora core and installed modules.
 *
 * @author   Krzysztof Trzos
 * @package  I18n
 * @version  1.2.0-dev
 * @since    1.2.0-dev
 */
class Import
{
    /**
     * Already loaded content from multiple locale files.
     *
     * @access  private
     * @var     array
     * @since   1.2.0-dev
     */
    private $loadedContent = [];

    /**
     * Array containing counted data about translations per
     * application part.
     *
     * @access  private
     * @var     array
     * @since   1.2.0-dev
     */
    private $amountLoadedPerType = [];

    /**
     * Factory method.
     *
     * @access   public
     * @return   Import
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    public static function factory()
    {
        return new Import();
    }

    /**
     * Constructor.
     *
     * @access   public
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    public function __construct()
    {

    }

    /**
     * Import all basic translations.
     *
     * @access   public
     * @return   array
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    public function importFiles()
    {
        // core translations
        $this->loadFiles(PATH_FW);

        // modules
        foreach(Router::getModules() as $moduleName => $moduleData) {
            $this->loadFiles(PATH_MODULES.$moduleData['path']);
        }

        // application translations
        $this->loadFiles(PATH_APP);

        // return all loaded translations
        return $this->loadedContent;
    }

    /**
     * Import all basic translations (from the level of Plethora install script).
     *
     * @access   public
     * @param    array $modules
     * @return   array
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    public function importFilesInstallation(array $modules)
    {
        // core translations
        $this->loadFiles(PATH_FW);

        // modules
        foreach($modules as $group => $modulesInGroup) {
            foreach(array_keys($modulesInGroup) as $module) {
                $this->loadFiles(PATH_MODULES.$group.DS.$module);
            }
        }

        // return all loaded translations
        return $this->loadedContent;
    }

    /**
     * Load translations from a given path.
     *
     * @access   private
     * @param    string $path
     * @throws   Exception\Fatal
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    private function loadFiles($path)
    {
        $localePath = $path.DS.'locale';
        $langs      = Router::getLangs();

        if(file_exists($localePath)) {
            $scannedDir = \FileManager::scanDir($localePath, 0);

            foreach($scannedDir as $dirId => &$file) {
                list($lang, $type) = explode('.', $file);

                // check language
                if(!in_array($lang, $langs)) {
                    unset($scannedDir[$dirId]);
                    continue;
                }

                // import data from single file
                switch($type) {
                    case 'json':
                        $fileTranslations = static::importFileJson(
                            $localePath.DS.$file,
                            $lang
                        );
                        break;
                    default:
                        $fileTranslations = [];
                }

                // count translations per type
                $appPart = NULL;

                if(strpos($path, PATH_FW) !== FALSE) {
                    $appPart = 'fw';
                } elseif(strpos($path, PATH_MODULES) !== FALSE) {
                    $appPart = 'module';
                } elseif(strpos($path, PATH_APP) !== FALSE) {
                    $appPart = 'app';
                }

                // do counting per app part
                $counting = 0;

                foreach($fileTranslations as $context => $translation) {
                    $counting += count($translation);
                }

                $temp = &$this->amountLoadedPerType[$appPart];

                switch($appPart) {
                    case 'module':
                        $module               = str_replace(PATH_MODULES, '', $path);
                        $temp[$module][$lang] = $counting;
                        break;
                    case 'app':
                    case 'fw':
                        $temp[$lang] = $counting;
                        break;
                }

                // merge data
                $this->loadedContent = array_replace_recursive(
                    $this->loadedContent,
                    $fileTranslations
                );
            }
        }
    }

    /**
     * Load translations from *.json files.
     *
     * @access   private
     * @param    string $filePath
     * @param    string $lang
     * @return   array
     * @throws   Exception\Fatal
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    private function importFileJson($filePath, $lang)
    {
        $content      = file_get_contents($filePath);
        $translations = json_decode($content, TRUE);

        if(json_last_error() > 0) {
            $err =
                'Error while decoding JSON file on path "'.$filePath.'": '.
                Helper\Json::returnJsonError(json_last_error());

            throw new Exception\Fatal\I18n($err);
        }

        foreach($translations as $context => &$strings) {
            if(!is_array($strings)) {
                $err = 'Your locale JSON file ("'.$filePath.'") has no contexts.';

                throw new Exception\Fatal\I18n($err);
            }

            foreach($strings as $toTranslate => $translated) {
                $strings[$toTranslate] = [$lang => $translated];
            }
        }

        return $translations;
    }

    /**
     * Returns array containing information about counted data per application,
     * module and Plethora framework.
     *
     * @access   public
     * @return   array
     * @version  1.2.0-dev
     * @since    1.2.0-dev
     */
    public function getCountedValues()
    {
        return $this->amountLoadedPerType;
    }
}