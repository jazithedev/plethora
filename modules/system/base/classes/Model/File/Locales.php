<?php

namespace Model\File;

use Plethora\Form;
use Plethora\ModelCore;

/**
 * File locales model.
 *
 * @Entity
 * @Table(name="files_locales")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          base
 * @subpackage       Model\File
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Locales extends ModelCore\Locales
{

    /**
     * @ManyToOne(targetEntity="\Model\File", inversedBy="locales")
     *
     * @access  protected
     * @var     \Model\File
     * @since   1.0.0-alpha
     */
    protected $parent;

    /**
     * @Column(type="string", length=100, nullable=true)
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $title;

    /**
     * @Column(type="string", length=300, nullable=true)
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $description;

    /**
     * Generate Config.
     *
     * @static
     * @access   public
     * @return   ModelCore\MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function generateConfig()
    {
        // get Config from parent
        $config = parent::generateConfig();

        // BACKEND
        $config->addField(Form\Field\Hidden::singleton('id')
            ->setLabel(__('ID'))
            ->setDisabled()
        );

        $config->addField(Form\Field\Text::singleton('title')
            ->setLabel(__('Title'))
        );

        $config->addField(Form\Field\Textarea::singleton('description')
            ->setLabel(__('Description'))
        );

        // return Config
        return $config;
    }

    /**
     * Set new value of title.
     *
     * @access   public
     * @param    string $value
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * Get title.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @access   public
     * @param    string $sDescription
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setDescription($sDescription)
    {
        $this->description = $sDescription;
    }

    /**
     * Get description.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getDescription()
    {
        return stripslashes($this->description);
    }

}