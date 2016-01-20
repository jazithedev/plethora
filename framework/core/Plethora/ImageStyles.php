<?php

namespace Plethora;

use Plethora\Helper;
use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\ImageWorkshop;

/**
 * Main class for image styles.
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @package          Plethora
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class ImageStyles
{
    const STYLE_ADMIN_PREVIEW = 'admin_preview';
    const STYLE_COLORBOX      = 'colorbox';

    /**
     * Images main upload directory.
     *
     * @since  1.0.0-alpha
     */
    const DIR_UPLOADS = 'uploads/';

    /**
     * Image styles upload directory.
     *
     * @since   1.0.0-alpha
     */
    const DIR_IMG_STYLES = 'uploads/image_styles/';

    /**
     * Actual image style name.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sImageStyleName = '';

    /**
     * Factory method.
     *
     * @static
     * @access     public
     * @return     \Plethora\ImageStyles
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory()
    {
        $aDbt    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $sCaller = isset($aDbt[1]['function']) ? $aDbt[1]['function'] : NULL;

        return new ImageStyles($sCaller);
    }

    /**
     * Constructor.
     *
     * @static
     * @access     public
     * @param      string $sCaller
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct($sCaller)
    {
        $sDecamelized          = Helper\String::decamelize($sCaller);
        $this->sImageStyleName = str_replace('style_', '', $sDecamelized);
    }

    /**
     * Fast method using image styles.
     *
     * @static
     * @access     public
     * @param      string $sImageStyle
     * @param      string $sArgImageDir
     * @param      string $sImageName
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function useStyle($sImageStyle, $sArgImageDir, $sImageName = NULL)
    {
        $sImageDir = str_replace(DIRECTORY_SEPARATOR, '/', $sArgImageDir);

        if(!file_exists($sImageDir)) {
            return 'file_do_not_exist';
        }

        if($sImageName === NULL) {
            $aExploded  = explode('/', $sImageDir);
            $sImageName = array_pop($aExploded);
            $sImageDir  = implode('/', $aExploded);
        }

        if(substr($sImageDir, 0, strlen(static::DIR_UPLOADS)) == static::DIR_UPLOADS) {
            $sImageStyleDirPath = substr($sImageDir, strlen(static::DIR_UPLOADS));
        } else {
            $sImageStyleDirPath = $sImageDir;
        }

        if(static::isImageStyled($sImageStyle, $sImageStyleDirPath, $sImageName)) {
            return static::DIR_IMG_STYLES.$sImageStyle.'/'.$sImageStyleDirPath.'/'.$sImageName;
        } else {
            $sMethodName = 'style'.Helper\String::camelize($sImageStyle);

            return call_user_func(['\\'.get_called_class(), $sMethodName], $sImageDir, $sImageName);
        }
    }

    /**
     * Save styled image in the correct location.
     *
     * @access     public
     * @param      ImageWorkshopLayer $oLayer
     * @param      string             $sImageDir
     * @param      string             $sImageName
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function save(ImageWorkshopLayer $oLayer, $sImageDir, $sImageName)
    {
        if(substr($sImageDir, 0, strlen(static::DIR_UPLOADS)) == static::DIR_UPLOADS) {
            $sImageDir = substr($sImageDir, strlen(static::DIR_UPLOADS));
        }

        $sSaveDir = static::DIR_IMG_STYLES.$this->sImageStyleName.'/'.$sImageDir;

        $oLayer->save($sSaveDir, $sImageName, TRUE, NULL, Config::get('base.images_quality'));

        return $sSaveDir.'/'.$sImageName;
    }

    /**
     * Check if particular image was already styled earlier.
     *
     * @static
     * @access     public
     * @param      string $sImageStyle
     * @param      string $sImageDir
     * @param      string $sImageName
     * @return     boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function isImageStyled($sImageStyle, $sImageDir, $sImageName)
    {
        $sFilePath = PATH_PUBLIC.static::DIR_IMG_STYLES.$sImageStyle.DIRECTORY_SEPARATOR.$sImageDir.DIRECTORY_SEPARATOR.$sImageName;

        return file_exists($sFilePath);
    }

    /**
     * Preview style for images on backend.
     *
     * @static
     * @access     public
     * @param      string $sImageDir
     * @param      string $sImageName
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function styleAdminPreview($sImageDir, $sImageName)
    {
        $oLayer = ImageWorkshop::initFromPath($sImageDir.'/'.$sImageName);
        $oLayer->resizeToFit(200, 200, TRUE);

        return static::factory()->save($oLayer, $sImageDir, $sImageName);
    }

    /**
     * Preview style for images on backend.
     *
     * @static
     * @access     public
     * @param      string $sImageDir
     * @param      string $sImageName
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function styleUserLogo($sImageDir, $sImageName)
    {
        $oLayer = ImageWorkshop::initFromPath($sImageDir.'/'.$sImageName);
        $oLayer->resizeToFit(100, 100, TRUE);

        return static::factory()->save($oLayer, $sImageDir, $sImageName);
    }

    /**
     * Preview style for images on backend.
     *
     * @static
     * @access     public
     * @param      string $sImageDir
     * @param      string $sImageName
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function styleColorbox($sImageDir, $sImageName)
    {
        $oLayer = ImageWorkshop::initFromPath($sImageDir.'/'.$sImageName);
        $oLayer->resizeToFit(400, 400, TRUE);

        return static::factory()->save($oLayer, $sImageDir, $sImageName);
    }

    /**
     * Remove one image from image styles cache.
     *
     * @static
     * @access     public
     * @param      string $sArgImagePath
     * @return     boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function removeStyledImgCache($sArgImagePath)
    {
        $sImagePath      = \FileManager::prepareDirPath($sArgImagePath);
        $sUploadsDir     = \FileManager::prepareDirPath(static::DIR_UPLOADS);
        $sStylesImageDir = \FileManager::prepareDirPath(static::DIR_IMG_STYLES);

        if(!file_exists($sStylesImageDir)) {
            return TRUE;
        }

        $aStyles         = scandir($sStylesImageDir);
        $bReturn         = FALSE;

        if(strpos($sImagePath, $sUploadsDir) === 0) {
            $sImagePath = str_replace($sUploadsDir, '', $sImagePath);
        }

        foreach($aStyles as $sImageStyle) {
            $sStyledImgPath = \FileManager::prepareDirPath($sStylesImageDir.$sImageStyle.DIRECTORY_SEPARATOR.$sImagePath);

            if(!in_array($sImageStyle, ['.', '..']) && file_exists($sStyledImgPath)) {
                unlink($sStyledImgPath);

                $bReturn = TRUE;
            }
        }

        return $bReturn;
    }

}
