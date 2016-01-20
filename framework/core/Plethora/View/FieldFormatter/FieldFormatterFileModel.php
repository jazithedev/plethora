<?php

namespace Plethora\View\FieldFormatter;

use Plethora\Helper;
use Plethora\Router;
use Plethora\View;
use Plethora\ModelCore;

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
class FieldFormatterFileModel extends View\FieldFormatter implements View\ViewFieldFormatterInterface {

    /**
     * Factory config.
     *
     * @access     public
     * @return    FieldFormatterFileModel
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new FieldFormatterFileModel();
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
     * Format value.
     *
     * @access     public
     * @param    string $value
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function format($value) {
        $sOutput = '';

        if($value instanceof ModelCore\FileBroker) {
            $oAttributes = Helper\Attributes::factory();
            $oField      = $this->getField();
            /* @var $oField View\ViewField */
            $sFieldName = $oField->getName();
            $oFile      = $value->getFile();
            /* @var $oFile \Model\File */

            // get file path
            $sFilePath = $oFile->getFullPath();

            // generate HTML
            $oAttributes->addToAttribute('href', Router::getBase().'/'.$sFilePath);

            $sOutput = '<img src="/assets/system/file_icons/'.$oFile->getExt().'.png" alt="" /> <a '.$oAttributes->renderAttributes().'>'.$sFilePath.'</a>';

            // remove objects
            unset($oFileManager);
        }

        // return final output
        return $this->sPrefix.$sOutput.$this->sSuffix;
    }

}
