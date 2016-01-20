<?php

namespace Model;

use Plethora\DB;
use Plethora\Exception;
use Plethora\ImageStyles;
use Plethora\ModelCore;
use Plethora\Form;
use Plethora\Helper;
use Plethora\Router;

/**
 * Main model class of File entity.
 *
 * @Entity
 * @Table(name="files")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          base
 * @subpackage       Model
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class File extends ModelCore implements ModelCore\ModelInterface
{
    const FILE_STATUS_TEMPORARY = 0;
    const FILE_STATUS_PERMANENT = 1;

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     *
     * @access    protected
     * @var        integer
     * @since     1.0.0-alpha
     */
    protected $id;

    /**
     * ID of User which uploaded particular file.
     *
     * @ManyToOne(targetEntity="\Model\User", inversedBy="files")
     *
     * @access    protected
     * @var        integer
     * @since     1.0.0-alpha
     */
    protected $author;

    /**
     * Name of the particular file.
     *
     * @Column(type="string", length=255)
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $name;

    /**
     * Path to file.
     *
     * @Column(type="string", length=255)
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $file_path;

    /**
     * Extension of the particular file.
     *
     * @Column(type="string", length=16)
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $ext;

    /**
     * MIME type of the particular file.
     *
     * @Column(type="string", length=100)
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $mime;

    /**
     * Size of the file in bytes.
     *
     * @Column(type="integer")
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $size;

    /**
     * Amount of uses of this file in entire application.
     *
     * @Column(type="integer", options={"default":1})
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $uses = 1;

    /**
     * Status of the file (0 - temporary, 1 - permanent)
     *
     * @Column(type="smallint")
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $status;

    /**
     * Date of upload.
     *
     * @Column(type="datetime")
     *
     * @access  protected
     * @var     \DateTime
     * @since   1.0.0-alpha
     */
    protected $add_date;

    /**
     * @Column(type="datetime", nullable=TRUE)
     *
     * @access  protected
     * @var     \DateTime
     * @since   1.0.0-alpha
     */
    protected $modification_date;

    /**
     * @OneToMany(targetEntity="\Model\File\Locales", mappedBy="parent")
     *
     * @access  protected
     * @var     \Model\File\Locales
     * @since   1.0.0-alpha
     */
    protected $locales;

    /**
     * Class constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        parent::__construct();

        $this->add_date = new \DateTime();
        $this->status   = static::FILE_STATUS_TEMPORARY;
    }

    /**
     * Fields config for backend.
     *
     * @access   public
     * @return   ModelCore\MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected static function generateConfig()
    {
        $oConfig            = parent::generateConfig();
        $currentRouteName   = Router::getCurrentRouteName();
        $currentRouteParams = Router::getParams();


        $oConfig->addField(Form\Field\Hidden::singleton('id')
            ->setLabel(__('ID'))
            ->setDisabled());

        // BACKEND
        if($currentRouteName === 'backend' && in_array($currentRouteParams['action'], ['edit', 'list'])) {
            $oConfig->addField(Form\Field\Text::singleton('name')
                ->setLabel(__('File name'))
                ->setDisabled());

            $oConfig->addField(Form\Field\Text::singleton('ext')
                ->setLabel(__('File extension'))
                ->setDisabled());

            $oConfig->addField(Form\Field\Text::singleton('file_path')
                ->setLabel(__('File path'))
                ->setDisabled());

            $oConfig->addField(Form\Field\Text::singleton('mime')
                ->setLabel(__('File MIME type'))
                ->setDisabled());

            $oConfig->addField(Form\Field\Text::singleton('size')
                ->setLabel(__('File size'))
                ->setTip(__('File size in KB.'))
                ->setDisabled());

            $oConfig->addField(Form\Field\Select::singleton('status')
                ->setOptions([0 => __('Temporary'), 1 => __('Permanent')])
                ->setLabel(__('Status'))
                ->setDisabled());
        }

        return $oConfig;
    }

    /**
     * Get title of particular entity.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getEntityTitle()
    {
        return $this->getFullPath();
    }


    /**
     * Remove file by ID.
     *
     * @static
     * @access   public
     * @param    integer $iFileID
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function deleteFile($iFileID)
    {
        // get file object and its path
        $oFile = DB::find('\Model\File', $iFileID);
        /* @var $oFile \Model\File */
        $sPath = $oFile->getPath().DS.$oFile->getNameWithExt();

        // if it's an image, remove all its styles
        if(in_array($oFile->getExt(), ['jpg', 'jpeg', 'gif', 'png', 'tiff'])) {
            ImageStyles::removeStyledImgCache($sPath);
        }

        // remove file
        \FileManager::delete($sPath);

        // remove from database
        $oFile->remove();

//		\Plethora\DB::flush();
    }

    /**
     * Get ID.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author.
     *
     * @access     public
     * @param    User $oAuthor
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setAuthor(User $oAuthor)
    {
        $this->author = $oAuthor;
    }

    /**
     * Get author.
     *
     * @access     public
     * @return    User
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set name of the file.
     *
     * @access     public
     * @param    string $sName
     * @return    File
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setName($sName)
    {
        $this->name = $sName;

        return $this;
    }

    /**
     * Get name of the file.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set file extension.
     *
     * @access     public
     * @param    string $sValue
     * @return    File
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setExt($sValue)
    {
        $this->ext = $sValue;

        return $this;
    }

    /**
     * Get particular file extension.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Get file name and extension.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getNameWithExt()
    {
        return $this->getName().'.'.$this->getExt();
    }

    /**
     * Set path to the file.
     *
     * @access     public
     * @param    string $sPath
     * @return    File
     * @since      1.0.0-alpha
     * @version    2.36.4-dev, 2015-07-03
     */
    public function setPath($sPath)
    {
        $this->file_path = str_replace(DS, '/', $sPath);

        return $this;
    }

    /**
     * Get path to the file.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getPath()
    {
        return $this->file_path;
    }

    /**
     * Get path to the file.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFullPath()
    {
        return $this->file_path.'/'.$this->getNameWithExt();
    }

    /**
     * Set mime of the file.
     *
     * @access   public
     * @param    string $sMime
     * @return   File
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setMime($sMime)
    {
        $aMimeTypes = Helper\MimeTypes::getExtByType($sMime);

        if($aMimeTypes === FALSE) {
            throw new Exception\Fatal('Unknown file type ('.$sMime.'). File couldn\'t be uploaded.');
        }

        $this->mime = $sMime;

        return $this;
    }

    /**
     * Get mime of the file.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set size of the file.
     *
     * @access   public
     * @param    integer $iSize
     * @return   File
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setSize($iSize)
    {
        $this->size = $iSize;

        return $this;
    }

    /**
     * Get size of the file.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set status of the file.
     *
     * @access   public
     * @param    integer $iStatus
     * @return   File
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setStatus($iStatus)
    {
        if(!in_array($iStatus, [static::FILE_STATUS_TEMPORARY, static::FILE_STATUS_PERMANENT])) {
            throw new Exception\Fatal('Unknown file status ('.$iStatus.'). Should be '.static::FILE_STATUS_TEMPORARY.' (temporary) or '.static::FILE_STATUS_PERMANENT.' (permanent).');
        }

        $this->status = $iStatus;

        return $this;
    }

    /**
     * Get status of the file.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get add date.
     *
     * @access   public
     * @return   \DateTime
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAddDate()
    {
        return $this->add_date;
    }

    /**
     * Get modification date.
     *
     * @access   public
     * @return   \DateTime
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getModificationDate()
    {
        return $this->modification_date;
    }

    /**
     * Move file to the other path.
     *
     * @access     public
     * @param    string $sPath
     * @return    File
     * @since      2.36.4-dev, 2015-07-03
     * @version    2.37.0-dev, 2015-07-28
     */
    public function moveFile($sPath)
    {
        \FileManager::prepareDir($sPath);

        $oFileManager = new \FileManager;
        $oFileManager->prepareFileByPath($this->getPath().'/'.$this->getNameWithExt());
        $oFileManager->move($sPath);

        $this->setName($oFileManager->getName()); // if file name was changed in \FileManager class
        $this->setPath($oFileManager->getPath());

        return $this;
    }

    /**
     * Update modification date.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function updateModificationDate()
    {
        $this->modification_date = new \DateTime();
    }

    /**
     * Remove entity. If entity has been removed, delete related file.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function remove()
    {
        $bRemoved = parent::remove();

        if($bRemoved) {
            // set path to file
            $sPath = $this->getPath().DS.$this->getNameWithExt();

            // if it's an image, remove all its styles
            if(in_array($this->getExt(), ['jpg', 'jpeg', 'gif', 'png', 'tiff'])) {
                ImageStyles::removeStyledImgCache($sPath);
            }

            // remove file
            \FileManager::delete($sPath);
        }

        return $bRemoved;
    }

    /**
     * Get amount of file uses.
     *
     * @access     public
     * @return    integer
     * @since      2.36.10-dev, 2015-07-19
     * @version    2.36.10-dev, 2015-07-19
     */
    public function getUsesAmount()
    {
        return $this->uses;
    }

    /**
     * Increase amount of file uses by 1.
     *
     * @access     public
     * @return    \Model\File
     * @since      2.36.10-dev, 2015-07-19
     * @version    2.36.10-dev, 2015-07-19
     */
    public function increaseUses()
    {
        $this->uses++;

        return $this;
    }

    /**
     * Decrease amount of file uses by 1.
     *
     * @access   public
     * @return   \Model\File
     * @since    2.36.10-dev, 2015-07-19
     * @version  2.36.10-dev, 2015-07-19
     */
    public function decreaseUses()
    {
        $this->uses--;

        if($this->uses <= 0) {
            $this->remove();
        }

        return $this;
    }
}
