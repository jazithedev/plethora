<?php

namespace Model;

use Plethora\Form;
use Plethora\ModelCore;
use Plethora\Validator;
use Doctrine;

/**
 * @Entity
 * @Table(name="pages")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2013, Krzysztof Trzos
 * @package          pages
 * @subpackage       classes
 * @version          1.2.0-dev
 */
class Page extends ModelCore
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Model\User", inversedBy="pages")
     *
     * @access    protected
     * @var        User
     * @since     2015-02-09
     */
    protected $author;

    /**
     * @Column(type="string", length=100)
     */
    protected $title;

    /**
     * Simple page image.
     *
     * @ManyToOne(targetEntity="\Model\Page\Image", inversedBy="parent")
     * @JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @access    protected
     * @var        \Model\Page\Image
     * @since     1.1.1-dev, 2015-07-29
     */
    protected $image;

    /**
     * @Column(type="string", length=40)
     */
    protected $rewrite;

    /**
     * @Column(type="string", length=600)
     */
    protected $description;

    /**
     * @Column(type="string", length=100)
     */
    protected $keywords;

    /**
     * @Column(type="text")
     */
    protected $content;

    /**
     * @Column(type="datetime")
     */
    protected $add_date;

    /**
     * @Column(type="datetime", nullable=TRUE)
     */
    protected $modification_date;

    /**
     * @Column(type="boolean")
     */
    protected $deleted = FALSE;

    /**
     * @Column(type="boolean")
     */
    protected $published = TRUE;

    /**
     * @Column(type="boolean")
     */
    protected $is_html = FALSE;

    /**
     * @Column(type="boolean")
     */
    protected $is_editable = TRUE;

    /**
     * @OneToMany(targetEntity="\Model\Page\File", mappedBy="parent")
     * @JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @access  protected
     * @var     Doctrine\Common\Collections\ArrayCollection
     * @since   1.1.0-dev, 2015-07-15
     */
    protected $files;

    /**
     * Constructor.
     *
     * @access     public
     * @version    2013-10-04
     */
    public function __construct()
    {
        parent::__construct();

        $this->add_date = new \DateTime();
        $this->files    = new Doctrine\Common\Collections\ArrayCollection();
        $this->image    = new Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Fields config for backend.
     *
     * @access   public
     * @return   ModelCore\MConfig
     * @since    1.0.0
     * @version  1.0.3-dev, 2015-04-29
     */
    protected static function generateConfig()
    {
        # create config
        $config = parent::generateConfig();

        # create fields
        $config->addField(Form\Field\Hidden::singleton('id')
            ->setLabel('ID')
            ->setDisabled()
        );

        $config->addField(
            Form\Field\Select::singleton('published')
                ->setOptions(['Nie', 'Tak'])
                ->setLabel(__('Published'))
                ->setRequired()
                ->addRulesSet(
                    Validator\RulesSetBuilder::factory()
                        ->addRule(['in_array', [':value', [1, 0]]])
                )
        );

        $config->addField(
            Form\Field\Text::singleton('title')
                ->setLabel(__('Title'))
                ->setRequired()
        );

        $config->addField(
            Form\Field\Text::singleton('rewrite')
                ->setRequired()
                ->setLabel(__('URL alias'))
        );

        $config->addField(
            Form\Field\Text::singleton('keywords')
                ->setLabel(__('Keywords'))
        );

        $config->addField(
            Form\Field\ImageModel::singleton('image')
                ->setBrokerModel('\Model\Page\Image')
                ->setUploadPath('uploads/page')
                ->setRequired()
                ->setLabel(__('Image'))
                ->addRulesSet(
                    Validator\RulesSetBuilder\ImageModel::factory()
                        ->allowedExt(':value', ['jpg', 'gif', 'png', 'jpeg'])
                )
        );

        $config->addField(
            Form\Field\FileModel::singleton('files')
                ->setBrokerModel('\Model\Page\File')
                ->setUploadPath('uploads/page_files')
                ->setQuantity(0)
                ->setLabel(__('Attachments'))
                ->addRulesSet(
                    Validator\RulesSetBuilder\ImageModel::factory()
                        ->allowedExt(':value', ['txt', 'pdf', 'docx', 'doc', 'rtf', 'zip'])
                )
        );

        $config->addField(
            Form\Field\Textarea::singleton('description')
                ->setLabel(__('Description'))
        );

        $config->addField(
            Form\Field\Tinymce::singleton('content')
                ->setRequired()
                ->setLabel(__('Content'))
        );

        # return config
        return $config;
    }

    /**
     * Get page ID.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0
     * @version    1.0.0
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set author.
     *
     * @access   public
     * @param    User $oAuthor
     * @since    2015-02-09
     * @version  2015-02-09
     */
    public function setAuthor(User $oAuthor)
    {
        $this->author = $oAuthor;
    }

    /**
     * Get author.
     *
     * @access   public
     * @return   User
     * @since    2015-02-09
     * @version  2015-02-09
     */
    public function getAuthor()
    {
        return $this->author;
    }

    # TITLE methods

    public function setTitle($v)
    {
        $this->title = $v;
    }

    public function getTitle()
    {
        return $this->title;
    }

    # REWRITE methods

    public function setRewrite($v)
    {
        $this->rewrite = $v;
    }

    public function getRewrite()
    {
        return $this->rewrite;
    }

    # DESCRIPTION methods

    public function setDescription($v)
    {
        $this->description = $v;
    }

    public function getDescription()
    {
        return $this->description;
    }

    # KEYWORDS methods

    public function setKeywords($v)
    {
        $this->keywords = $v;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    # CONTENT methods

    public function setContent($val)
    {
        $this->content = $val;
    }

    public function getContent()
    {
        return $this->content;
    }

    # ADD DATE methods

    public function getAddDate()
    {
        return $this->add_date;
    }

    # MODIFICATION DATE methods

    public function getModificationDate()
    {
        return $this->modification_date;
    }

    public function setModificationDate()
    {
        return $this->modification_date;
    }

    /**
     * @access     public
     * @return    boolean
     * @since      2013-10-08
     * @version    2013-10-08
     */
    public function isHtml()
    {
        return $this->is_html;
    }

    /**
     * @access     public
     * @param    boolean $bValue
     * @since      2013-10-08
     * @version    2013-10-08
     */
    public function setIsHtml($bValue = TRUE)
    {
        $this->is_html = $bValue;
    }

    /**
     * @access     public
     * @return    boolean
     * @since      2013-10-08
     * @version    2013-10-08
     */
    public function isEditable()
    {
        return $this->is_editable;
    }

    /**
     * @access     public
     * @param    boolean $bValue
     * @since      2013-10-08
     * @version    2013-10-08
     */
    public function setIsEditable($bValue = TRUE)
    {
        $this->is_editable = $bValue;
    }

    /**
     * Get page image.
     *
     * @access     public
     * @return    File
     * @since      1.0.3-dev, 2015-04-29
     * @version    1.1.3-dev, 2015-08-01
     */
    public function getImage()
    {
        $oBroker = $this->image;

        /* @var $oBroker \Plethora\ModelCore\FileBroker */

        return $oBroker->getFile();
    }

    /**
     * Get whole path to the page image.
     *
     * @access     public
     * @return    FALSE|string
     * @since      1.0.3-dev, 2015-04-29
     * @version    1.1.3-dev, 2015-08-01
     */
    public function getImagePath()
    {
        $oImage = $this->getImage();
        /* @var $oImage File */

        if(!$oImage instanceof File) {
            return FALSE;
        }

        return $oImage->getFullPath();
    }

    /**
     * Set new value for page image.
     *
     * @access   public
     * @param    $sValue
     * @return   $this
     * @since    1.0.3-dev, 2015-04-29
     * @version  1.0.3-dev, 2015-04-29
     */
    public function setImage($sValue)
    {
        $this->image = $sValue;

        return $this;
    }

    /**
     * Update modification date.
     *
     * @access     public
     * @return    string
     * @since      1.0.0
     * @version    2.3.0, 2014-12-21
     */
    public function updateModificationDate()
    {
        $this->modification_date = new \DateTime();
    }

}
