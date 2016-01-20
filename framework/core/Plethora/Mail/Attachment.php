<?php

namespace Plethora\Mail;

/**
 * Mail attachment class.
 *
 * @package        Plethora
 * @subpackage     Mail
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Attachment {

    /**
     * File content
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sContent;

    /**
     * File name
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sFilename;

    /**
     * Randomed file id
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $id;

    /**
     * File content type
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sContentType;

    /**
     * Constructor
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct() {
        $this->id = md5(time() + rand(0, 10));
    }

    /**
     * Create new attachment.
     *
     * @static
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Attachment();
    }

    /**
     * Get file content from path.
     *
     * @access   public
     * @param    string $sPath
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function fromPath($sPath) {
        $this->setContent(file_get_contents($sPath));

        return $this;
    }

    /**
     * Set file content type.
     *
     * @access   public
     * @param    string $sContentType
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setContentType($sContentType) {
        $this->sContentType = $sContentType;

        return $this;
    }

    /**
     * Get file content type.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getContentType() {
        return $this->sContentType;
    }

    /**
     * Set file content.
     *
     * @access   public
     * @param    string $sBody
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setContent($sBody) {
        $this->sContent = $sBody;

        return $this;
    }

    /**
     * Get content.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getContent() {
        return $this->sContent;
    }

    /**
     * Set name of the attachments.
     *
     * @access   public
     * @param    string
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFilename($name) {
        $this->sFilename = $name;

        return $this;
    }

    /**
     * Get name of the file.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFilename() {
        return $this->sFilename;
    }

    /**
     * Get base64 encoded body
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getBodyBase64() {
        return chunk_split(base64_encode($this->sContent));
    }

    /**
     * Get file id
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get path to file in mail content.  Used in html images etc.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getPath() {
        return 'cid:'.$this->id;
    }

}
