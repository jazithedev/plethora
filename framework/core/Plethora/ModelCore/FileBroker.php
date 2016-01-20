<?php

namespace Plethora\ModelCore;

use Model\User;
use Plethora\ModelCore;
use Plethora\Form;
use Model\File;

/**
 * Main class used on file brokers.
 *
 * @abstract
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
abstract class FileBroker extends ModelCore {

    /**
     * Database row identifier.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     *
     * @access  protected
     * @var     integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Model\File", fetch="EAGER")
     *
     * @access  protected
     * @var     User
     * @since   1.0.0-alpha
     */
    protected $file;

    /**
     * @Column(type="smallint", options={"unsigned"=true, "default"=0})
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sort_order = 0;

    /**
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $aTempFileData = [];

    /**
     * A parent to which this file is corresponding.
     *
     * @access  protected
     * @var     ModelCore
     * @since   1.0.0-alpha
     */
    protected $parent;

    /**
     * Generate Config.
     *
     * @static
     * @access   public
     * @return   MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function generateConfig() {
        // get Config from parent
        $oConfig = parent::generateConfig();

        // BACKEND
        $oConfig
            ->addField(Form\Field\Hidden::singleton('id')
                ->setLabel(__('ID'))
                ->setDisabled()
            );

        // return Config
        return $oConfig;
    }

    /**
     * Get ID of this entity.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get file.
     *
     * @access   public
     * @return   File
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set file.
     *
     * @access   public
     * @param    File $oFile
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFile(File $oFile) {
        $this->file = $oFile;

        return $this;
    }

    /**
     *
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getOrder() {
        return $this->sort_order;
    }

    /**
     *
     * @access     public
     * @param    integer $iOrder
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setOrder($iOrder) {
        $this->sort_order = (int)$iOrder;

        return $this;
    }

    /**
     * Remove all data of this Model from database.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function remove() {
        $oFile = $this->file;
        /* @var $oFile File */

        parent::remove();

        $oFile->decreaseUses();
    }

    /**
     *
     * @access     public
     * @param    string $aTempFileData
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setTempData($aTempFileData) {
        $this->aTempFileData = $aTempFileData;

        return $this;
    }

    /**
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getTempData() {
        return $this->aTempFileData;
    }

    /**
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function clearTempData() {
        $this->aTempFileData = [];

        return $this;
    }

    /**
     * Get parent to which this file is corresponding.
     *
     * @access   public
     * @return   ModelCore
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Set parent to which this file is corresponding.
     *
     * @access   public
     * @param    ModelCore $parent
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setParent($parent) {
        $this->parent = $parent;

        return $this;
    }
}
