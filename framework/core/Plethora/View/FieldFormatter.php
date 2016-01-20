<?php

namespace Plethora\View;

/**
 * Field formatters parent.
 *
 * @package        Plethora
 * @subpackage     View
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatter implements ViewFieldFormatterInterface {

    /**
     * List of available entity profiles.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aAvailableFor = [];

    /**
     * Field reference on which operations are performed.
     *
     * @access    private
     * @var        ViewField
     * @since     1.0.0-alpha
     */
    private $oField;

    /**
     * Container prefix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sPrefix = '';

    /**
     * Container prefix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sSuffix = '';

    /**
     * Factory method.
     *
     * @static
     * @access     public
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new FieldFormatter();
    }

    /**
     * Make value formatting on a given value.
     *
     * @access     public
     * @param    mixed $value
     * @return    string
     * @since      1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function format($value) {
        return $this->sPrefix.$value.$this->sSuffix;
    }

    /**
     * Make formatting on an array value.
     *
     * @access     public
     * @param    array $values
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function formatArray(array $values) {
        foreach($values as &$mSingle) {
            $mSingle = $this->format($mSingle);
        }

        return $values;
    }

    /**
     * Set field reference.
     *
     * @access     public
     * @param    ViewField $oField
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setField(ViewField $oField) {
        $this->oField = $oField;

        return $this;
    }

    /**
     * Get field reference.
     *
     * @access     public
     * @return    ViewField
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function &getField() {
        return $this->oField;
    }

    /**
     * Limit usage of this formatter only to entities lists.
     *
     * @access     public
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function useOnlyOnLists() {
        $this->aAvailableFor = ['list'];

        return $this;
    }

    /**
     * Check if usage of this formatter is available to singletons.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isAvailableForSingletons() {
        return $this->isAvailableFor('singleton');
    }

    /**
     * Limit usage of this formatter only to single entities.
     *
     * @access     public
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function useOnlyOnSingletons() {
        $this->aAvailableFor = ['singleton'];

        return $this;
    }

    /**
     * Check if usage of this formatter is available to entities lists.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isAvailableForLists() {
        return $this->isAvailableFor('list');
    }

    /**
     * Setting availability for a single formatting profile.
     *
     * @access     public
     * @param    string $sProfile
     * @return FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setAvailabilityFor($sProfile) {
        $this->aAvailableFor = [$sProfile];

        return $this;
    }

    /**
     * Adding availability for a single formatting profile.
     *
     * @access     public
     * @param    string $sProfile
     * @return FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addAvailabilityFor($sProfile) {
        $this->aAvailableFor[] = $sProfile;

        return $this;
    }

    /**
     * Check whether this formatting is available for particular entity profile.
     *
     * @access     public
     * @param    string $sProfile
     * @return    boolean
     * @since      1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function isAvailableFor($sProfile) {
        return empty($this->aAvailableFor) || in_array($sProfile, $this->aAvailableFor);
    }

    /**
     * Get all profiles to which this field formatter is available.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getAvailableFor() {
        return $this->aAvailableFor;
    }

    /**
     * Set new value for format.
     *
     * @access     public
     * @param    string $sPrefix
     * @param    string $sSuffix
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setContainer($sPrefix, $sSuffix) {
        $this->sPrefix = $sPrefix;
        $this->sSuffix = $sSuffix;

        return $this;
    }

    /**
     * Get prefix.
     *
     * @access     public
     * @param    string $sPrefix
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setPrefix($sPrefix) {
        $this->sPrefix = $sPrefix;

        return $this;
    }

    /**
     * Get suffix.
     *
     * @access     public
     * @param    string $sSuffix
     * @return    FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setSuffix($sSuffix) {
        $this->sSuffix = $sSuffix;

        return $this;
    }

}