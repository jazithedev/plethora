<?php

namespace PlethoraInstall;

/**
 * Provide bunch of methods which operates on views of framework install system.
 *
 * @author           Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          PlethoraInstall
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class InstallView {

    /**
     * View path.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewPath = '';

    /**
     * List of all variables for particular View.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aVars = [];

    /**
     * Returns new instance of View.
     *
     * @static
     * @access   public
     * @param    string $sPath
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($sPath) {
        return new InstallView($sPath);
    }

    /**
     * Constructor.
     *
     * @access   public
     * @param    string $sPath
     * @throws   \Exception
     * @since    1.0
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($sPath = NULL) {
        $sFinalPath = $this->readViewPath($sPath);

        $this->setPath($sFinalPath);
    }

    /**
     * Setting path to particular View.
     *
     * @access   public
     * @param    string $sPath
     * @return   InstallView
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setPath($sPath) {
        $this->sViewPath = $sPath;

        return $this;
    }

    /**
     * Final rendering of view
     *
     * @access   public
     * @param    array $aArgs
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render(array $aArgs = []) {
        $aAllArgs = array_merge($aArgs, $this->aVars);

        foreach($aAllArgs as $sViewSingleArgumentName => $mViewSingleArgumentValue) {
            ${$sViewSingleArgumentName} = $mViewSingleArgumentValue;
        }

        unset($mViewSingleArgumentValue);
        unset($sViewSingleArgumentName);

        ob_start();
        require $this->sViewPath;
        $sContentGetByObGetClean = ob_get_clean();

        foreach(array_keys($aArgs) as $sName) {
            if(isset(${$sName})) {
                unset(${$sName});
            }
        }

        foreach(array_keys($this->aVars) as $sName) {
            if(isset(${$sName})) {
                unset(${$sName});
            }
        }

        return $sContentGetByObGetClean;
    }

    /**
     * Getting view path based on parameter
     *
     * @static
     * @access   private
     * @param    string $sToRender Contains information about particular view destination (pattern:
     *                             viewPath.viewExtension)
     * @return   string
     * @throws   \Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function readViewPath($sToRender) {
        if(strpos($sToRender, '::') === FALSE) {
            $sViewPath = $sToRender;
            $sExt      = 'php';
        } else {
            $aExploded = explode('::', $sToRender);
            $sViewPath = $aExploded[0];
            $sExt      = $aExploded[1];

            unset($aExploded);
        }

        if(file_exists($sGlobalPath = PATH_INSTALL.'views'.DS.$sViewPath.'.'.$sExt)) {
            return $sGlobalPath;
        }

        throw new \Exception('View with path "'.$sToRender.'" does not exist!');
    }

    /**
     * Set variable in View.
     *
     * @access   public
     * @param    string $sName
     * @param    mixed  $mValue
     * @return   InstallView
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function set($sName, $mValue) {
        $this->aVars[$sName] = $mValue;

        return $this;
    }

    /**
     * Bind variable to the View.
     *
     * @access   public
     * @param    string $sName
     * @param    mixed  $mValue
     * @return   InstallView
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function bind($sName, &$mValue) {
        $this->aVars[$sName] = &$mValue;

        return $this;
    }

    /**
     * Check if particular View exists.
     *
     * @static
     * @access   public
     * @param    string $sPath
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function viewExists($sPath) {
        try {
            static::readViewPath($sPath);

            return TRUE;
        } catch(\Exception $ex) {
            return FALSE;
        }
    }

}
