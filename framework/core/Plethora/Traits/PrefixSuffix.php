<?php

namespace Plethora\Traits;

/**
 * Class PrefixSuffix
 *
 * @package        Trait
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
trait PrefixSuffix
{
    /**
     * Variable with content of the prefix.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $prefix = '';

    /**
     * Variable with content of the suffix.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $suffix = '';

    /**
     * Set form prefix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function setPrefix($value)
    {
        $this->prefix = $value;

        return $this;
    }

    /**
     * Add new content to form prefix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function addToPrefix($value)
    {
        $this->prefix .= $value;

        return $this;
    }

    /**
     * Get form prefix.
     *
     * @access  public
     * @return  string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set suffix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function setSuffix($value)
    {
        $this->suffix = $value;

        return $this;
    }

    /**
     * Add new content to suffix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function addToSuffix($value)
    {
        $this->suffix .= $value;

        return $this;
    }

    /**
     * Get suffix.
     *
     * @access  public
     * @return  string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function getSuffix()
    {
        return $this->suffix;
    }
}