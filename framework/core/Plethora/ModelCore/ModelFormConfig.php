<?php

namespace Plethora\ModelCore;

/**
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ModelFormConfig {

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $sAction = NULL;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $sMessage = NULL;

    /**
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bDefaultReload = TRUE;

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFields = [];

    /**
     * List of field names. It will be used to remove particular fields from
     * Model form.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFieldsToRemove = [];

    /**
     * This variable tells whether the Model form validation is done by Model
     * instance or manually called by controller.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bManualValidation = FALSE;

    /**
     * This variable tells whether the Model form save is done by Model instance
     * or manually called by controller.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bManualSave = FALSE;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   ModelFormConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new ModelFormConfig();
    }

    /**
     * Set action for form.
     *
     * @access     public
     * @param      string $sValue
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     * @return     $this
     */
    public function setAction($sValue) {
        $this->sAction = $sValue;

        return $this;
    }

    /**
     * Get action for form.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getAction() {
        return $this->sAction;
    }

    /**
     * Set message for form flash (after saving data).
     *
     * @access     public
     * @param      string $sValue
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setMessage($sValue) {
        $this->sMessage = $sValue;

        return $this;
    }

    /**
     * Get message which will be used in flash.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getMessage() {
        return $this->sMessage;
    }

    /**
     * Set to not reload page after data saving.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function noReload() {
        $this->bDefaultReload = FALSE;

        return $this;
    }

    /**
     * Check if page would reload after data saving.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isReloading() {
        return $this->bDefaultReload;
    }

    /**
     * Set fields names array to limit Model form to particular fields.
     *
     * @access   public
     * @param    array $aFields
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFieldsRestriction(array $aFields) {
        $this->aFields = $aFields;

        return $this;
    }

    /**
     * Get fields names array to limit Model form to particular fields.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFieldsRestriction() {
        return $this->aFields;
    }

    /**
     * Set names of fields which will be removed from Model form.
     *
     * @access     public
     * @param    array $aFields
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setFieldsToRemove(array $aFields) {
        $this->aFieldsToRemove = $aFields;

        return $this;
    }

    /**
     * Get names of fields which will be removed from Model form.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFieldsToRemove() {
        return $this->aFieldsToRemove;
    }

    /**
     * Tells whether the Model form validation is done by Model instance or
     * manually called by controller.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isManualValidation() {
        return $this->bManualValidation;
    }

    /**
     * Set validation to manual (called by controller).
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setManualValidation() {
        $this->bManualValidation = TRUE;

        return $this;
    }

    /**
     * Tells whether the Model form save is done by Model instance or
     * manually called by controller.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isManualSave() {
        return $this->bManualSave;
    }

    /**
     * Set save to manual (called by controller).
     *
     * @access     public
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setManualSave() {
        $this->bManualSave = TRUE;

        return $this;
    }
}