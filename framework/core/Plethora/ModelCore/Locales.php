<?php

namespace Plethora\ModelCore;

use Plethora\Config;
use Plethora\Exception;
use Plethora\ModelCore;

/**
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Locales extends ModelCore
{

    /**
     * Main identifier of an entity.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $id;

    /**
     * @Column(type="string", length=3)
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $language;

    /**
     * @access  protected
     * @var     ModelCore
     * @since   1.0.0-alpha
     */
    protected $parent;

    /**
     * Get entity language.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language for particular entity locales.
     *
     * @access   public
     * @param    string $sLang
     * @return   $this
     * @throws   Exception\Model
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLanguage($sLang)
    {
        if(!in_array($sLang, Config::get('base.languages'))) {
            throw new Exception\Model('Wrong language.');
        }

        $this->language = $sLang;

        return $this;
    }

    /**
     * Set parent.
     *
     * @access   public
     * @param    ModelCore $oValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setParent(ModelCore $oValue)
    {
        $this->parent = $oValue;

        return $this;
    }

    /**
     * Get parent.
     *
     * @access   public
     * @return   ModelCore
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getParent()
    {
        return $this->parent;
    }
}