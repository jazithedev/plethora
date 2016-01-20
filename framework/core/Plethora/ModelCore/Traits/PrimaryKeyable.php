<?php

namespace Plethora\ModelCore\Traits;

use Plethora\Exception\Fatal;

/**
 * This trait is used to add primary key to the Model.
 *
 * @package        Plethora
 * @subpackage     Model\Traits
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
trait PrimaryKeyable {
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $id;

    /**
     * Get identifier of particular entity.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getId() {
        return $this->id;
    }
}