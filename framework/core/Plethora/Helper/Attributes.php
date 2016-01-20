<?php

namespace Plethora\Helper;

use Plethora\Helper;

/**
 * Helper used to group in one object and parse HTML tag attributes.
 *
 * @package        Plethora
 * @subpackage     Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Attributes extends Helper {

    /**
     * Array of attributes and their values.
     *
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aAttributes = [];

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   Attributes
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Attributes();
    }

    /**
     * Get all attributes as an array.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttributes() {
        return $this->aAttributes;
    }

    /**
     * Get attribute value of particular name.
     *
     * @access     public
     * @param    string $sName
     * @return    string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttribute($sName) {
        return Arrays::get($this->aAttributes, $sName);
    }

    /**
     * Remove particular attribute from form field.
     *
     * @access     public
     * @param    string $sName
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function removeAttribute($sName) {
        if($this->hasAttribute($sName)) {
            unset($this->aAttributes[$sName]);

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check if form field has particular attribute.
     *
     * @access     public
     * @param    string $sName
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hasAttribute($sName) {
        return isset($this->aAttributes[$sName]);
    }

    /**
     * Set HTML attributes list.
     *
     * @access     public
     * @param    array $aAttributes
     * @return    Attributes
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setAttributes($aAttributes = []) {
        $this->aAttributes = $aAttributes;

        return $this;
    }

    /**
     * Set HTML attribute.
     *
     * @access     public
     * @param    string  $sName
     * @param    string  $sValue
     * @param    boolean $bKeepOriginalValue
     * @return    Attributes
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setAttribute($sName, $sValue, $bKeepOriginalValue = FALSE) {
        if($bKeepOriginalValue) {
            $this->aAttributes[$sName] = trim(Arrays::get($this->aAttributes, $sName, '').' '.$sValue);
        } else {
            $this->aAttributes[$sName] = $sValue;
        }

        return $this;
    }

    /**
     * Add content to HTML attribute.
     *
     * @access     public
     * @param    string  $sName
     * @param    string  $sValue
     * @param    boolean $bWithSpace
     * @return    Attributes
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToAttribute($sName, $sValue, $bWithSpace = TRUE) {
        $this->aAttributes[$sName] = trim(Arrays::get($this->aAttributes, $sName, '').($bWithSpace ? ' ' : '').$sValue);

        return $this;
    }

    /**
     * Add content to HTML attribute.
     *
     * @access   public
     * @param    string $sName
     * @param    string $sValue
     * @return   Attributes
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function concatToAttribute($sName, $sValue) {
        $this->addToAttribute($sName, $sValue, FALSE);

        return $this;
    }

    /**
     * Parse all attributes and return them as a string.
     *
     * @access   public
     * @param    array   $aAddContent
     * @param    boolean $bSaveOldValue
     * @param    boolean $bWithSpace
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function renderAttributes($aAddContent = [], $bSaveOldValue = FALSE, $bWithSpace = TRUE) {
        $sReturn = '';

        // render main attributes
        foreach($this->aAttributes as $sName => $sValue) {
            if(!isset($aAddContent[$sName])) {
                $sReturn .= ' '.$sName.'="'.trim($sValue).'"';
            }
        }

        // render the rest of the attributes with additional content
        foreach($aAddContent as $sName => $sValue) {
            $sOldValue = $bSaveOldValue ? Arrays::get($this->aAttributes, $sName, '') : '';

            $sReturn .= ' '.$sName.'="'.trim($sOldValue.($bWithSpace ? ' ' : '').$sValue).'"';
        }

        // return rendered attributes
        return ltrim($sReturn);
    }

}
