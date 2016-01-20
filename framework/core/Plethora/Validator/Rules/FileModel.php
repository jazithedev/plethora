<?php

namespace Plethora\Validator\Rules;

use Plethora\Exception;
use Plethora\ModelCore;
use Plethora\Helper;

/**
 * Strings validation methods
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FileModel
{

    /**
     * Check if particular FileBroker is not empty.
     *
     * @static
     * @access   public
     * @param    string $oValue
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function notEmpty($oValue)
    {
        if($oValue instanceof ModelCore\FileBroker && ($oValue->getFile() instanceof \Model\File || $oValue->getTempData())) {
            return TRUE;
        }

        return __('Given value cannot be empty.');
    }

    /**
     * Checks if uploaded file meets the requirements of minimal capacity
     *
     * @static
     * @access   public
     * @param    ModelCore\FileBroker $oValue
     * @param    integer              $iSize File size in KB
     * @return   bool|string
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function minSize($oValue, $iSize)
    {
        if(is_null($oValue)) {
            return TRUE;
        }

        if(!is_int($iSize)) {
            throw new Exception\Fatal('Wrong argument value for method minSize()!');
        }

        $aValue = $oValue->getTempData();

        if(!isset($aValue['tmp_name'])) {
            return TRUE;
        }

        if($aValue['size'] / 1024 < $iSize) {
            return __('Uploaded file is too small (:size2KB)! It\'s minimal size should be :sizeKB.', ['size' => $iSize, 'size2' => $aValue['size']]);
        }

        return TRUE;
    }

    /**
     * Checks if uploaded file meets the requirements of maximal capacity
     *
     * @static
     * @access   public
     * @param    ModelCore\FileBroker $value
     * @param    integer              $size File size in KB
     * @return   boolean|string
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function maxSize($value, $size)
    {
        if(is_null($value)) {
            return TRUE;
        }

        if(!is_int($size)) {
            throw new Exception("Wrong argument value for method maxSize()!");
        }

        $aValue = $value->getTempData();

        if(!isset($aValue['tmp_name'])) {
            return TRUE;
        }

        if($aValue['size'] / 1024 > $size) {
            return __('Uploaded file is too big (:sizenewKB)! It\'s maximal size should be :sizeneedKB.', ['sizeneed' => $size, 'sizenew' => round($aValue['size'] / 1024)]);
        }

        return TRUE;
    }

    /**
     * Check if particular uploaded file has valid extension.
     *
     * @static
     * @access   public
     * @param    ModelCore\FileBroker $fileBroker $_FILE like array
     * @param    array                $exts       Extensions list
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function allowedExt(ModelCore\FileBroker $fileBroker, array $exts)
    {
        if(is_null($fileBroker)) {
            return TRUE;
        }

        $value = $fileBroker->getTempData();

        if(!isset($value['tmp_name'])) {
            return TRUE;
        }

        $mimeType      = Helper\MimeTypes::getMimeType($value['tmp_name']);
        $extByMime     = Helper\MimeTypes::getExtByType($mimeType);
        $value['type'] = $mimeType;

        if(!$extByMime || !Helper\Arrays::anyInArray($exts, $extByMime)) {
            return __('File with wrong format uploaded! Allowed types of files: :exts.', ['exts' => implode(', ', $exts)]);
        }

        return TRUE;
    }

    /**
     * Checks if uploaded file has required type
     *
     * @static
     * @access   public
     * @param    ModelCore\FileBroker $fileBroker $_FILE like array
     * @param    string               $types      image type
     * @return   boolean|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function type(ModelCore\FileBroker $fileBroker, $types)
    {
        if(is_null($fileBroker)) {
            return TRUE;
        }

        $value = $fileBroker->getTempData();

        if(!isset($value['tmp_name'])) {
            return TRUE;
        }

        $mimeType = Helper\MimeTypes::getMimeType($value['tmp_name']);

        if(!in_array($mimeType, $types)) {
            return __('Invalid file type (:invalid_type)! List of allowed fields types: :types.', [
                'types'        => implode(', ', $types),
                'invalid_type' => $value['type'],
            ]);
        }

        return TRUE;
    }

}
