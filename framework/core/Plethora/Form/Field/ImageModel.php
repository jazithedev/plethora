<?php

namespace Plethora\Form\Field;
use Plethora\ModelCore;

/**
 * ImageModel form field.
 *
 * @package        Plethora\Form
 * @subpackage     Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ImageModel extends FileModel {

    /**
     * Path to the View of this field.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/image_model';

    /**
     * Default image path.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sDefaultImage = NULL;

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $sName
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($sName) {
        return static::singletonByType($sName, 'ImageModel');
    }

    /**
     * Get path to the default image for this field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getDefaultImage() {
        return $this->sDefaultImage;
    }

    /**
     * Set path to the default image for this field.
     *
     * @access   public
     * @param    string $sDefaultImagePath
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setDefaultImage($sDefaultImagePath) {
        $this->sDefaultImage = $sDefaultImagePath;

        return $this;
    }

    /**
     * Make some operations related to this field before Model Entity will be
     * completely removed.
     *
     * @access   public
     * @param    ModelCore $oModel
     * @param    mixed     $mValue
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function whenRemovingEntity(ModelCore $oModel, $mValue) {
        parent::whenRemovingEntity($oModel, $mValue);

        if(empty($mValue)) {
            return FALSE;
        }

        $aValues = [];

        if(!is_array($mValue)) {
            $aValues[] = $mValue;
        }

        foreach($aValues as $oFileBroker) {
            /* @var $oFileBroker \Plethora\ModelCore\FileBroker */
            $oFile = $oFileBroker->getFile();
            /* @var $oFile \Model\File */

            \Plethora\ImageStyles::removeStyledImgCache($oFile->getPath().DS.$oFile->getNameWithExt());
        }
    }

}
