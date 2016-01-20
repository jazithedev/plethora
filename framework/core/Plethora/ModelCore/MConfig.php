<?php

namespace Plethora\ModelCore;

use Plethora\Exception;
use Plethora\Form;
use Plethora\Helper;
use Plethora\View\FieldFormatter;

/**
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class MConfig
{
    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFields = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFieldsFormatters = [];

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new MConfig;
    }

    /**
     * Add field form of particular name.
     *
     * @access     public
     * @param      Form\Field $oField
     * @return     MConfig
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addField(Form\Field $oField)
    {
        $this->aFields[$oField->getName()] = $oField;

        return $this;
    }

    /**
     * Get field form.
     *
     * @access     public
     * @param      string $sName
     * @return     Form\Field
     * @throws     Exception
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getField($sName)
    {
        if(isset($this->aFields[$sName])) {
            return $this->aFields[$sName];
        } else {
            throw new Exception('Field of that name ("'.$sName.'") does not exist.');
        }
    }

    /**
     * Check if this config has a particular field.
     *
     * @access     public
     * @param      string $sName
     * @return     bool
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hasField($sName)
    {
        return isset($this->aFields[$sName]);
    }

    /**
     * Get fields names.
     *
     * @access     public
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFieldsNames()
    {
        return array_keys($this->aFields);
    }

    /**
     * Get all fields.
     *
     * @access     public
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFields()
    {
        return $this->aFields;
    }

    /**
     * Add field formatter.
     *
     * @access     public
     * @param      string         $sField
     * @param      FieldFormatter $oFormatter
     * @return     MConfig
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addFieldFormatter($sField, FieldFormatter $oFormatter)
    {
        if(!isset($this->aFieldsFormatters[$sField])) {
            $this->aFieldsFormatters[$sField] = [];
        }

        $this->aFieldsFormatters[$sField][] = $oFormatter;

        return $this;
    }

    /**
     * Get all fields formatters.
     *
     * @access     public
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFieldsFormatters()
    {
        return $this->aFieldsFormatters;
    }

    /**
     * Get particular field formatter.
     *
     * @access     public
     * @param      string $sField
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFieldFormatters($sField)
    {
        return Helper\Arrays::get($this->aFieldsFormatters, $sField, []);
    }
}