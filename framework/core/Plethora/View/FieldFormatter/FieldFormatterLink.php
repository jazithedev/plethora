<?php

namespace Plethora\View\FieldFormatter;

use Plethora\Route;
use Plethora\Helper;
use Plethora\View;

/**
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterLink extends View\FieldFormatter implements View\ViewFieldFormatterInterface {

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $modelFieldNameAsTitle = '';

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $routeName = NULL;

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $routeAttributes = [];

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $url = NULL;

    /**
     * Set title prefix.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $titlePrefix = NULL;

    /**
     * Set title suffix.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $titleSuffix = NULL;

    /**
     * Flag which says to set URL of this link from value.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bValueAsURL = FALSE;

    /**
     * Flag which says to set URL of this link from other field value.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $valueFromField = NULL;

    /**
     * Factory config.
     *
     * @access   public
     * @return   FieldFormatterLink
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new FieldFormatterLink();
    }

    /**
     * Format value.
     *
     * @access   public
     * @param    string $value
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function format($value) {
        $oLink = Helper\Link::factory();

        // set title attribute
        $sTitle   = '';
        $sURL     = '';
        $oModel   = $this->getField()->getEntity()->getModel();
        $oLocales = $oModel->getLocales();

        if($oModel->hasField($this->getConfigTitle())) {
            $sTitle = $oModel->{$this->getConfigTitle()};
        } elseif($oLocales !== NULL && $oLocales->hasField($this->getConfigTitle())) {
            $sTitle = $oLocales->{$this->getConfigTitle()};
        }

        $sTitle = $this->titlePrefix.$sTitle.$this->titleSuffix;

        if(!empty($sTitle)) {
            $oLink->setTitle($sTitle);
        }

        // set URL of the link
        if($this->bValueAsURL) {
            $sURL = $value;
        } elseif(!empty($this->valueFromField)) {
            $sURL = $this->getField()->getEntity()->getModel()->getValue($this->valueFromField);
        } elseif($this->getUrl() !== NULL) {
            $sURL = $this->getUrl();
        } elseif($this->getRouteName() !== NULL) {
            $aArgs = $this->getRouteAttributes();

            foreach($aArgs as $sAttrKey => $sField) {
                $sAttrValue = NULL;

                if($oModel->hasField($sField)) {
                    $sAttrValue = $oModel->{$sField};
                } elseif($oLocales !== NULL && $oLocales->hasField($sField)) {
                    $sAttrValue = $oLocales->{$sField};
                }

                $aArgs[$sAttrKey] = Helper\String::prepareToURL($sAttrValue);
            }

            $sURL = Route::factory($this->getRouteName())
                ->url($aArgs);
        }

        // return newly generated link code
        return $this->sPrefix.$oLink->code($value, $sURL).$this->sSuffix;
    }

    /**
     * Set Model field name which will be used as link title.
     *
     * @access   public
     * @param    string $modelFieldName
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setConfigTitle($modelFieldName) {
        $this->modelFieldNameAsTitle = $modelFieldName;

        return $this;
    }

    /**
     * Get Model field name which will be used as link title.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getConfigTitle() {
        return $this->modelFieldNameAsTitle;
    }

    /**
     * Set route and it's attributes.
     *
     * @access   public
     * @param    string $routeName
     * @param    array  $attributes
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRoute($routeName, array $attributes = []) {
        $this->routeName       = $routeName;
        $this->routeAttributes = $attributes;

        return $this;
    }

    /**
     * Get route name. It will be used to generate URL with it's attributes.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRouteName() {
        return $this->routeName;
    }

    /**
     * Get route attributes values. Each value is a name of field from Model.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRouteAttributes() {
        return $this->routeAttributes;
    }

    /**
     * Get URL.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setUrl($value) {
        $this->url = $value;

        return $this;
    }

    /**
     * Get URL.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set title prefix.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTitlePrefix($value) {
        $this->titlePrefix = $value;

        return $this;
    }

    /**
     * Set title suffix.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTitleSuffix($value) {
        $this->titleSuffix = $value;

        return $this;
    }

    /**
     * Set URL of this formatter from field value.
     *
     * @access   public
     * @return   FieldFormatterLink
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setUrlFromValue() {
        $this->bValueAsURL = TRUE;

        return $this;
    }

    /**
     * Set URL of this formatter from other field value.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setUrlFromField($value) {
        $this->valueFromField = $value;

        return $this;
    }

}
