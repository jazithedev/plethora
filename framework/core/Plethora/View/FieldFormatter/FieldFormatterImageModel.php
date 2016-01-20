<?php

namespace Plethora\View\FieldFormatter;

use Plethora\ImageStyles;
use Plethora\ModelCore;
use Plethora\Helper;
use Plethora\Router;
use Plethora\View;

/**
 * Formatter for images.
 *
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterImageModel extends View\FieldFormatter implements View\ViewFieldFormatterInterface {

    /**
     * This variable stores image style name used to stylize image (change shape, for example).
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sImageStyle = '';

    /**
     * Pattern for image ALT attribute.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sImageAltPattern = '';

    /**
     * Flag used to determine whether image is linked to it's original size
     * equivalent.
     *
     * @access    private
     * @var        boolean
     * @since     1.0.0-alpha
     */
    private $bLinkToOriginalSize = FALSE;

    /**
     * Attributes of this image link.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aLinkAttributes = [];

    /**
     * Factory config.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new FieldFormatterImageModel();
    }

    /**
     * Class constructor.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct() {

    }

    /**
     * Set flag to link this formatted image to the original sized image.
     *
     * @access     public
     * @param    array $aImageAttrs
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function linkToOriginalSize($aImageAttrs = []) {
        $this->bLinkToOriginalSize = TRUE;
        $this->aLinkAttributes     = $aImageAttrs;

        return $this;
    }

    /**
     * Stylize image if needed.
     *
     * @access     public
     * @param    string $sImageStyleName
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setImageStyle($sImageStyleName) {
        $this->sImageStyle = $sImageStyleName;

        return $this;
    }

    /**
     * Set name of field which will be used as <i>alt</i> attribute.
     *
     * @access     public
     * @param    string $sFieldName
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setFieldAsAlt($sFieldName) {
        $this->sImageAltPattern = ':'.$sFieldName;

        return $this;
    }

    /**
     * Set image ALT attribute pattern.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setAltPattern($sValue) {
        $this->sImageAltPattern = $sValue;

        return $this;
    }

    /**
     * Format value.
     *
     * @access     public
     * @param    ModelCore\FileBroker $oImageBroker
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function format($oImageBroker) {
        $sOutput = '';

        if($oImageBroker instanceof ModelCore\FileBroker && $oImageBroker->getFile() instanceof \Model\File) {
            $oAttributes = Helper\Attributes::factory();
            $oField      = $this->getField();
            /* @var $oField \Plethora\View\ViewField */
            $oFile = $oImageBroker->getFile();
            /* @var $oFile \Model\File */

            // set proper ALT
            if(!empty($this->sImageAltPattern)) {
                $oModel  = $oField->getEntity()->getModel();
                $aFields = $oModel->getMetadata()->getFieldNames();

                if($oModel->hasLocales()) {
                    $aFields = array_merge($aFields, $oModel->getLocalesMetadata()->getFieldNames());
                }

                $sAlt = $this->sImageAltPattern;

                foreach($aFields as $sField) {
                    if(strpos($sAlt, ':'.$sField) !== FALSE) {
                        $sAlt = str_replace(':'.$sField, $oModel->{$sField}, $sAlt);
                    }

                    if(strpos($sAlt, ':') === FALSE) {
                        break;
                    }
                }

                $oAttributes->addToAttribute('alt', $sAlt);
            }

            // get image path
            $sFieldName = $oField->getName();
            $oFormField = $oField->getEntity()->getModel()->getConfig()->getField($sFieldName);
            /* @var $oFormField \Plethora\Form\Field\ImageModel */
            $sImagePath = $oFile->getFullPath();

            if(empty($sImagePath) && $oFormField->getDefaultImage() === NULL) {
                return NULL;
            } elseif(empty($sImagePath) && $oFormField->getDefaultImage() !== NULL) {
                $sImagePath = $oFormField->getDefaultImage();
            }

            // stylize image
            if(!empty($this->sImageStyle)) {
                $sImagePath = ImageStyles::useStyle($this->sImageStyle, $sImagePath);
            }

            $oAttributes->addToAttribute('src', Router::getBase().'/'.$sImagePath);

            $sOutput = '<img '.$oAttributes->renderAttributes().' />';

            // if this image should be linked to it's original-sized equivalent
            if($this->bLinkToOriginalSize) {
                $sImagePathOriginal = '/'.$oFormField->getUploadPath(TRUE).'/'.$oFile->getNameWithExt();

                $oAttributes = Helper\Attributes::factory()->setAttributes($this->aLinkAttributes);
                $sOutput     = Helper\Html::a($sImagePathOriginal, $sOutput, $oAttributes);
            }
        }

        // return final output
        return $this->sPrefix.$sOutput.$this->sSuffix;
    }

}
