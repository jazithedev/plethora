<?php

namespace Plethora\Helper;

use Plethora\Exception;
use Plethora\Helper;
use Plethora\Log;

/**
 * ZIP archives helper.
 *
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Zip extends Helper {


    /**
     * Array of invalid files.
     *
     * @access   private
     * @var      array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private $invalidFiles = [];

    /**
     * Array of unextracted files.
     *
     * @access   private
     * @var      array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private $unextractedFiles = [];

    /**
     * Array of extracted files.
     *
     * @access   private
     * @var      array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private $extractedFiles = [];

    /**
     * Array of allowed file extenstions.
     *
     * @access   private
     * @var      array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private $allowedExt = [];

    /**
     * Set list of allowed extenstions in the ZIP archive.
     *
     * @access   private
     * @param    array $array
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setAllowedExt(array $array) {
        if(is_array($array)) {
            foreach($array as $v) {
                $this->allowedExt[] = $v;
            }
        }

        return $this;
    }

    /**
     * Set list of allowed extenstions in the ZIP archive.
     *
     * @access   public
     * @param    string $pathFrom
     * @param    string $pathTo
     * @return   array
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function unpackZip($pathFrom, $pathTo) {
        $valid_files = [];

        // checking for php ZIP extension
        if(!extension_loaded('zip')) {
            $msg = 'No php extension to ZIP decompression.';
            Log::insert($msg, 'ERROR');
            throw new Exception($msg);
        }

        // checking if extensions were given
        if(!count($this->allowedExt)) {
            $msg = 'No file extensions were added. First, use setAllowedExt() method to add valid extensions.';
            Log::insert($msg, 'ERROR');
            throw new Exception($msg);
        }

        $zip     = new \ZipArchive();
        $archive = $zip->open($pathFrom);

        // checking if this archive is valid ZIP archive
        if($archive !== TRUE) {
            return 'This is not a valid ZIP archive.';
        }

        // checking for valid files
        for($i = 0; $i < $zip->numFiles; $i++) {
            $file          = $zip->statIndex($i);
            $file_pathinfo = pathinfo($file['name']);

            if(isset($file_pathinfo['extension']) && in_array(mb_strtolower($file_pathinfo['extension']), $this->allowedExt)) {
                $valid_files[] = $file['name'];
            } else {
                $this->invalidFiles[] = $file['name'];
            }
        }

        // copying files to new location
        if(count($valid_files)) {
            foreach($valid_files as $file) {
                if(!($zip->extractTo($pathTo, $file))) {
                    $this->unextractedFiles[] = $file;
                } else {
                    $this->extractedFiles[] = $file;
                }
            }

            $zip->close();
        } else {
            return 'No valid files (with particular extensions) in this archive.';
        }

        return $this->extractedFiles;
    }

    /**
     * Returns list of invalid files.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getInvalidFiles() {
        return $this->invalidFiles;
    }

    /**
     * Returns number of unextracted files.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function numberOfUnextracted() {
        return count($this->unextractedFiles);
    }

    /**
     * Returns number of extracted files.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function numberOfExtracted() {
        return count($this->extractedFiles);
    }
}

?>