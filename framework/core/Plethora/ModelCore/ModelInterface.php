<?php

namespace Plethora\ModelCore;

/**
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
interface ModelInterface {
    /**
     * Get identifier of particular model entity.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getId();

    /**
     * Get array of fields for frontend list.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getListFields();

    /**
     * Get entity URL.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function url();

    /**
     * Get title of particular entity.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getEntityTitle();
}