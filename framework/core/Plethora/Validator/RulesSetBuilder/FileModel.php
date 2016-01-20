<?php

namespace Plethora\Validator\RulesSetBuilder;

use Plethora\Validator;

/**
 * @package        Plethora
 * @subpackage     Validator\RulesSetBuilder
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FileModel extends Validator\RulesSetBuilder {
    /**
     * Factory static method.
     *
     * @static
     * @access    public
     * @return    FileModel
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function factory() {
        return new FileModel();
    }

    /**
     * Check if particular FileBroker is not empty.
     *
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function notEmpty($sValue) {
        $this->addRule(['\Plethora\Validator\Rules\FileModel::notEmpty', [$sValue]]);

        return $this;
    }

    /**
     * Checks if uploaded file meets the requirements of minimal capacity
     *
     * @access   public
     * @param    array   $aFile
     * @param    integer $iSize File size in KB
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function minSize($aFile, $iSize) {
        $this->addRule(['\Plethora\Validator\Rules\FileModel::minSize', [$aFile, $iSize]]);
        $this->addTip(__('Minimal file size: :sizeKB.', ['size' => $iSize]));

        return $this;
    }

    /**
     * Checks if uploaded file meets the requirements of maximal capacity
     *
     * @access   public
     * @param    array   $aFile
     * @param    integer $iSize File size in KB
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function maxSize($aFile, $iSize) {
        $this->addRule(['\Plethora\Validator\Rules\FileModel::maxSize', [$aFile, $iSize]]);
        $this->addTip(__('Maximal file size: :sizeKB.', ['size' => $iSize]));

        return $this;
    }

    /**
     * Check if particular uploaded file has valid extension.
     *
     * @access   public
     * @param    array $aValue $_FILE like array
     * @param    array $aExts  Extensions list
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function allowedExt($aValue, array $aExts) {
        $this->addRule(['\Plethora\Validator\Rules\FileModel::allowedExt', [$aValue, $aExts]]);
        $this->addTip(__('Can upload files only with these extensions: :ext.', ['ext' => implode(',', $aExts)]));

        return $this;
    }

    /**
     * Checks if uploaded file has required type
     *
     * @access   public
     * @param    array  $aValue $_FILE like array
     * @param    string $aTypes image type
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function type($aValue, $aTypes) {
        $this->addRule(['\Plethora\Validator\Rules\FileModel::type', [$aValue, $aTypes]]);
        $this->addTip(__('Can upload files of only those types: :types.', ['types' => implode(',', $aTypes)]));

        return $this;
    }
}