<?php

namespace Plethora;

/**
 * Response class - provide view content to the project.
 *
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Response {

    /**
     * Main variable responsible for storing all content of the page.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $content;

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct() {
        Log::insert('Response object initialized!');
    }

    /**
     * Parse this object instance to string.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __toString() {
        return $this->content;
    }

    /**
     * Set content of this response.
     *
     * @access   public
     * @param    string $content
     * @return   Response
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

}
