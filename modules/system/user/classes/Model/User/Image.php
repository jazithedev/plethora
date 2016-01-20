<?php

namespace Model\User;

use Plethora\ModelCore;
use Model\User;


/**
 * @Entity
 * @Table(name="users_images")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          user
 * @subpackage       Model\User
 * @since            2.0.0-dev
 * @version          2.0.0-dev
 */
class Image extends ModelCore\FileBroker
{
    /**
     * A parent to which this file is corresponding.
     *
     * @ManyToOne(targetEntity="\Model\User", inversedBy="image")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @access    protected
     * @var       User
     * @since     2.0.0-dev
     */
    protected $parent;

    /**
     * Set parent to which this file is corresponding.
     *
     * @access   public
     * @param    User $parent
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setParent($parent)
    {
        return parent::setParent($parent);
    }

    /**
     * Get parent to which this file is corresponding.
     *
     * @access   public
     * @return   User
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getParent()
    {
        return parent::getParent();
    }


}