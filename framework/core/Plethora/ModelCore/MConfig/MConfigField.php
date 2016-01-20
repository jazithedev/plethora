<?php

namespace Plethora\ModelCore\MConfig;

use Plethora\Helper;
use Plethora\ModelCore\MConfig;
use Plethora\Exception;

/**
 * Single field in MConfig configuration class.
 *
 * @package        Plethora
 * @subpackage     ModelCore\MConfig
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class MConfigField {
    const TYPE_TEXT     = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT   = 'select';
    const TYPE_HIDDEN   = 'hidden';
    const TYPE_EDITOR   = 'editor';
    const TYPE_TINYMCE  = 'tinymce';

    /**
     * @access  private
     * @var     MConfig
     * @since   1.0.0-alpha
     */
    private $oModelConfig = NULL;

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aConfig = [];

    /**
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sName = NULL;

    /**
     * @static
     * @access   public
     * @param    MConfig $modelConfig
     * @param    string  $name
     * @param    string  $type
     * @return   MConfigField
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory(MConfig $modelConfig, $name, $type = self::TYPE_TEXT) {
        return new MConfigField($modelConfig, $name, $type);
    }

    /**
     * Constructor.
     *
     * @access   public
     * @param    MConfig $modelConfig
     * @param    string  $name
     * @param    string  $type
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct(MConfig $modelConfig, $name, $type = self::TYPE_TEXT) {
        $this->oModelConfig = $modelConfig;
        $this->setType($type);
        $this->setName($name);
    }

    /**
     * @access   public
     * @return   MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function end() {
        return $this->oModelConfig;
    }

    /**
     * @access   public
     * @param    string $sName
     * @param    string $mValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setConfigValue($sName, $mValue) {
        $this->aConfig[$sName] = $mValue;

        return $this;
    }

    /**
     * @access     public
     * @param    string $sName
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getConfigValue($sName) {
        return Helper\Arrays::get($this->aConfig, $sName, NULL);
    }

    /**
     * @static
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.1, 2014-01-19
     */
    public static function getAllTypes() {
        return [
            self::TYPE_CHECKBOX,
            self::TYPE_EDITOR,
            self::TYPE_HIDDEN,
            self::TYPE_SELECT,
            self::TYPE_TEXT,
            self::TYPE_TEXTAREA,
            self::TYPE_TINYMCE,
        ];
    }

    /**
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setType($sValue) {
        if(!in_array($sValue, $this->getAllTypes())) {
            throw new Exception('Wrong type of field.');
        }

        $this->setConfigValue('type', $sValue);

        return $this;
    }

    /**
     * @access     public
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getType() {
        return $this->getConfigValue('type');
    }

    /**
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setLabel($sValue) {
        $this->setConfigValue('label', $sValue);

        return $this;
    }

    /**
     * @access     public
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getLabel() {
        return $this->getConfigValue('label');
    }

    /**
     * Set new rules.
     *
     * @access   public
     * @param    array $aValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRules(array $aValue) {
        $this->setConfigValue('rules', $aValue);

        return $this;
    }

    /**
     * Get all rules (as an array).
     *
     * @access     public
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getRules() {
        $mRules = $this->getConfigValue('rules');

        return ($mRules === NULL) ? [] : $mRules;
    }

    /**
     * @access   public
     * @param    array $aValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setUses(array $aValue) {
        $this->setConfigValue('uses', $aValue);

        return $this;
    }

    /**
     * @access   public
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getUses() {
        $mUses = $this->getConfigValue('uses');

        return ($mUses === NULL) ? [] : $mUses;
    }

    /**
     * Set new name for this field.
     *
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setName($sValue) {
        $this->sName = $sValue;

        return $this;
    }

    /**
     * Get name of this particular field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getName() {
        return $this->sName;
    }
}