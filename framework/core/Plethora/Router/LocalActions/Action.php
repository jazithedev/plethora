<?php

namespace Plethora\Router\LocalActions;

/**
 * Local actions class.
 * 
 * @package        Plethora
 * @subpackage     Router
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Action {

    /**
     * Local action title.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sTitle = NULL;

    /**
     * Route name, on which local action appears.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sRelatedRoute = NULL;

    /**
     * Local action route name.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sRoute = NULL;

    /**
     * Class to build local action URL (advanced).
     *
     * @access    private
     * @var        \Closure
     * @since     1.0.0-alpha
     */
    private $oBuilder = NULL;

    /**
     * List of conditions that indicates whether the local action can appear.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aConditions = [];

    /**
     * List of parameters to generate local action URL.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aParameters = [];

    /**
     * Icon (from glyphicons) of the local action.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sIcon = 'plus';

    /**
     * Factory method.
     *
     * @access   public
     * @param    string $sTitle
     * @param    string $sRelatedRoute
     * @param    string $sRoute
     * @return   Action
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($sTitle, $sRelatedRoute, $sRoute) {
        return new Action($sTitle, $sRelatedRoute, $sRoute);
    }

    /**
     * Constructor method.
     *
     * @access     public
     * @param    string $sTitle
     * @param    string $sRelatedRoute
     * @param    string $sRoute
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct($sTitle, $sRelatedRoute, $sRoute) {
        $this->sTitle        = $sTitle;
        $this->sRelatedRoute = $sRelatedRoute;
        $this->sRoute        = $sRoute;
    }

    /**
     * Set route.
     *
     * @access   public
     * @param    string $sRoute
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRoute($sRoute) {
        $this->sRoute = $sRoute;

        return $this;
    }

    /**
     * Get route.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getRoute() {
        return $this->sRoute;
    }

    /**
     * Set local action conditions.
     *
     * @access   public
     * @param    array $aConditions
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setConditions(array $aConditions) {
        $this->aConditions = $aConditions;

        return $this;
    }

    /**
     * Get local action conditions.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getConditions() {
        return $this->aConditions;
    }

    /**
     * Set local action URL parameters.
     *
     * @access   public
     * @param    array $aParameters
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setParameters(array $aParameters) {
        $this->aParameters = $aParameters;

        return $this;
    }

    /**
     * Set single parameter for this action.
     *
     * @access     public
     * @param    string $sParamName
     * @param    string $sParamValue
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setParameter($sParamName, $sParamValue) {
        $this->aParameters[$sParamName] = $sParamValue;
    }

    /**
     * Get local action URL parameters.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getParameters() {
        return $this->aParameters;
    }

    /**
     * Set building class.
     *
     * @access   public
     * @param    \Closure $oLambda
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setBuilder(\Closure $oLambda) {
        $this->oBuilder = $oLambda;

        return $this;
    }

    /**
     * Get building class.
     *
     * @access     public
     * @return    \Closure
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getBuilder() {
        return $this->oBuilder;
    }

    /**
     * Set title.
     *
     * @access   public
     * @param    string $sTitle
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTitle($sTitle) {
        $this->sTitle = $sTitle;

        return $this;
    }

    /**
     * Get title.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getTitle() {
        return $this->sTitle;
    }

    /**
     * Set local action icon.
     *
     * @access     public
     * @param    string $sValue
     * @return    Action
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setIcon($sValue) {
        $this->sIcon = $sValue;

        return $this;
    }

    /**
     * Get icon of this local action.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getIcon() {
        return $this->sIcon;
    }
}
