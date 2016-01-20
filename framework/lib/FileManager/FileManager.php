<?php

use Plethora\Helper;
use Plethora\Exception;

/**
 * FileManager library.
 *
 * @package    lib
 * @author     Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
class FileManager
{

    const UPLOAD_SAVE_BOTH         = 1;
    const UPLOAD_REMOVE_OLD        = 2;
    const UPLOAD_ERROR_ON_EXISTING = 3;

    /**
     * Time after which the temporary files will be deleted by garbage collector.
     *
     * @staticvar
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private static $iTempTime = 43200;

    /**
     * Stores instance of actual class..
     *
     * @staticvar
     * @access  private
     * @var     $this
     * @since   1.0.0-alpha
     */
    private static $oInstance;

    /**
     * Stores data from $_FILES global array.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFile;

    /**
     * Stores file path.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $sFilePath;

    /**
     * Stores file name.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $sFileName;

    /**
     * Stores file extension.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $sFileExt;

    /**
     * Destructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __destruct()
    {
        static::clearTemp();
    }

    /**
     * Method which initialize class.
     *
     * @static
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        if(!(self::$oInstance instanceof FileManager)) {
            self::$oInstance = new FileManager();
        }

        return self::$oInstance;
    }

    /**
     * Parsing $_FILE data for particular file input
     *
     * @access   public
     * @param    array  $aFile     array based on $_FILE variable
     * @param    string $sFileName New file name
     * @return   $this
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function parseFileData(array $aFile, $sFileName = NULL)
    {
        if($aFile['name'] === '') {
            throw new Exception\Fatal('File data is empty. File was not uploaded.');
        }

        $this->aFile = $aFile;

        $aExplodedName = explode('.', $this->aFile['name']);
        $this->setExt(array_pop($aExplodedName));

        if(!is_null($sFileName)) {
            $this->setName($sFileName);
        } else {
            $this->setName('temp_'.time().uniqid());
        }

        $this->setPath('');

        return $this;
    }

    /**
     * Preparing file with given path.
     *
     * @param   string $sPathToFile
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function prepareFileByPath($sPathToFile)
    {
        $aExploded = explode('/', $sPathToFile);
        $sFile     = array_pop($aExploded);
        $sPath     = implode('/', $aExploded);

        $aFile     = explode('.', $sFile);
        $sExt      = array_pop($aFile);
        $sFileName = implode('.', $aFile);

        $this->setExt($sExt);
        $this->setName($sFileName);
        $this->setPath($sPath);

        return $this;
    }

    /**
     * Setting file name
     *
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setName($sValue)
    {
        $this->sFileName = \Plethora\Helper\String::prepareToURL($sValue);

        return $this;
    }

    /**
     * Getting file name
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getName()
    {
        return $this->sFileName;
    }

    /**
     * Setting file extension
     *
     * @access   public
     * @param    string $sExt
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setExt($sExt)
    {
        $this->sFileExt = $sExt;

        return $this;
    }

    /**
     * Getting file extension
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getExt()
    {
        return $this->sFileExt;
    }

    /**
     * Get file MIME type.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMime()
    {
        return \Plethora\Helper\MimeTypes::getTypeByExt($this->getExt());
    }

    /**
     * Getting file path
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getPath()
    {
        return $this->sFilePath;
    }

    /**
     * Getting file path
     *
     * @access   public
     * @param    string $sPath
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setPath($sPath)
    {
        $this->sFilePath = $sPath;

        return $this;
    }

    /**
     * Getting full file path
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFullPath()
    {
        return $this->sFilePath.'/'.$this->sFileName.'.'.$this->sFileExt;
    }

    /**
     * Get file name and extension as one string imploded with dot.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getNameWithExt()
    {
        return $this->getName().'.'.$this->getExt();
    }

    /**
     * Uploading file to particular path.
     *
     * @access   public
     * @param    string   $sPath path to directory, where the file must be uploaded
     * @param    bool|int $iOperationOnExisting
     * @param    integer  $iChmod
     * @return   $this
     * @throws   Exception
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function upload($sPath, $iOperationOnExisting = self::UPLOAD_SAVE_BOTH, $iChmod = 0755)
    {
        if(!is_dir($sPath)) {
            throw new Exception('Ścieżka "'.$sPath.'" nie istnieje!');
        }

        $aFilePath = $sPath.DIRECTORY_SEPARATOR.$this->getName().'.'.$this->sFileExt;
        $this->setPath($sPath);

        if(file_exists($aFilePath)) {
            switch($iOperationOnExisting) {
                case static::UPLOAD_SAVE_BOTH:
                    while(file_exists($aFilePath)) {
                        $this->setName($this->getName().'_'.uniqid());

                        $aFilePath = $sPath.DIRECTORY_SEPARATOR.$this->getName().'.'.$this->sFileExt;
                    }
                    break;
                case static::UPLOAD_REMOVE_OLD:
                    unlink($aFilePath);
                    break;
                case static::UPLOAD_ERROR_ON_EXISTING:
                    throw new Exception('File with particular name already exists.');
            }
        }

        move_uploaded_file($this->aFile['tmp_name'], $aFilePath);
        chmod($sPath, $iChmod);

        return $this;
    }

    /**
     * Delete public files.
     *
     * @static
     * @access   public
     * @param    string $sPath
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function delete($sPath)
    {
        if(!empty($sPath)) {
            $sWholePath = PATH_PUBLIC.$sPath;

            if(file_exists($sWholePath)) {
                return unlink($sWholePath);
            }
        }

        return FALSE;
    }

    /**
     * Copy a file to another location.
     *
     * @access   public
     * @param    string  $sPath path to directory, where the file must be copied
     * @param    boolean $bDeleteExisting
     * @param    int     $iChmod
     * @return   $this
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function copy($sPath, $bDeleteExisting = TRUE, $iChmod = 0755)
    {
        if(!is_dir($sPath)) {
            throw new Exception('Ścieżka "'.$sPath.'" nie istnieje!');
        }

        if(empty($this->sFilePath)) {
            throw new Exception('This files path must be established! Use setPath() method firstly.');
        }

        $sFileName = $this->sFileName.'.'.$this->sFileExt;
        $sFilePath = $sPath.DIRECTORY_SEPARATOR.$sFileName;

        if(!file_exists($this->sFilePath.DIRECTORY_SEPARATOR.$sFileName)) {
            throw new Exception('File "'.$this->sFilePath.DIRECTORY_SEPARATOR.$sFileName.'" doesn\'t exist.');
        }

        if($bDeleteExisting && file_exists($sFilePath)) {
            unlink($sFilePath);
        }

        copy($this->sFilePath.DIRECTORY_SEPARATOR.$sFileName, $sFilePath);
        chmod($sPath, $iChmod);

        $this->setPath($sPath);

        return $this;
    }

    /**
     * Copy a file to another location.
     *
     * @access     public
     * @param      string  $sPath path to directory, where the file must be copied
     * @param      integer $iChmod
     * @param      boolean $bOverwrite
     * @return     $this
     * @throws     Exception
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function move($sPath, $iChmod = 0755, $bOverwrite = FALSE)
    {
        if(!is_dir($sPath)) {
            throw new Exception('Ścieżka "'.$sPath.'" nie istnieje!');
        }

        if(empty($this->sFilePath)) {
            throw new Exception('This files path must be established! Use setPath() method firstly.');
        }

        $sFileName    = $this->sFileName.'.'.$this->sFileExt;
        $sFileNameOld = $sFileName;
        $sFilePath    = $sPath.DS.$sFileName;

        if(!file_exists($this->sFilePath.DS.$sFileName)) {
            throw new Exception('File "'.$this->sFilePath.DS.$sFileName.'" doesn\'t exist.');
        }

        if(file_exists($sFilePath) && !$bOverwrite) {
            while(file_exists($sFilePath)) {
                $this->setName($this->sFileName.'_'.uniqid());

                $sFileName = $this->sFileName.'.'.$this->sFileExt;
                $sFilePath = $sPath.DS.$sFileName;
            }
        }

        rename($this->sFilePath.DS.$sFileNameOld, $sFilePath);
        chmod($sPath, $iChmod);

        $this->setPath($sPath);

        return $this;
    }

    /**
     * Rename a file.
     *
     * @access   public
     * @param    string $sName
     * @return   $this
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function rename($sName)
    {
        $sNewPath = $this->getPath().DS.$sName.'.'.$this->getExt();

        if(file_exists($sNewPath)) {
            throw new Exception('Plik w tym miejscu i o takiej nazwie ("'.$sName.'") już istnieje!');
        }

        rename($this->getFullPath(), $sNewPath);

        $this->setName($sName);

        return $this;
    }

    /**
     * Tworzenie drzewa katalogów (jeśli nie istnieje)
     *
     * @param    string $path
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function prepareDirPath($path)
    {
        return str_replace(['\\', '/'], [DS, DS], $path);
    }

    /**
     * Create directory tree (if not exists).
     *
     * @param    string  $path
     * @param    boolean $withFile
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function prepareDir($path, $withFile = FALSE)
    {
        $sPathTmp = static::prepareDirPath($path);

        if(file_exists($sPathTmp)) {
            return TRUE;
        }

        $sActualPath = '';

        if(strpos($sPathTmp, PATH_ROOT) !== FALSE) {
            $sActualPath .= rtrim(PATH_ROOT, DS);
            $sPathTmp = str_replace(PATH_ROOT, '', $sPathTmp);
        }

        $aPath = explode(DS, $sPathTmp);

        if($withFile) {
            array_pop($aPath);
        }

        foreach($aPath as $sDir) {
            if(!empty($sDir)) {
                if($sActualPath === '') {
                    $sActualPath = $sDir;
                } else {
                    $sActualPath .= DS.$sDir;
                }

                if(!file_exists($sActualPath)) {
                    mkdir($sActualPath);
                }
            }
        }

        if($sActualPath != '') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This method cleans temporary files after passing an appropriate amount of
     * time from the date of their creation.
     *
     * @static
     * @access   public
     * @param    string $path
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function clearTemp($path = NULL)
    {
        $sMainPath = $path === NULL ? PATH_TEMP : $path.DS;
        $aTempDir  = scandir($sMainPath);

        foreach($aTempDir as $sFile) {
            if(!in_array($sFile, ['.', '..'])) {
                $sFilePath = $sMainPath.$sFile;

                if(is_dir($sFilePath)) {
                    static::clearTemp($sFilePath);
                } else {
                    $iFileTime = filemtime($sFilePath);

                    if(time() - $iFileTime > static::$iTempTime) {
                        unlink($sFilePath);
                    }
                }
            }
        }

        return TRUE;
    }

    /**
     * Scan directory and return array of files and directores.
     *
     * @static
     * @access   public
     * @param    string $path
     * @param    int    $deepness
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function scanDir($path, $deepness = 0)
    {
        $output = [];

        foreach(scandir($path) as $file) {
            if(!in_array($file, ['.', '..'])) {
                if(is_dir($file)) {
                    if($deepness === -1 || $deepness > 0) {
                        $output[$file] = static::scanDir($path.DS.$file, $deepness > 0 ? $deepness-- : -1);
                    }
                } else {
                    $output[$file] = $file;
                }
            }
        }

        return $output;
    }
}