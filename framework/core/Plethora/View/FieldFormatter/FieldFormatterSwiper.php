<?php

namespace Plethora\View\FieldFormatter;

use Plethora\View;

/**
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterSwiper extends View\FieldFormatter implements View\ViewFieldFormatterInterface
{
    /**
     * Flag which tells whether to show pagination on this Swiper object.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showPagination = FALSE;

    /**
     * Flag which tells whether to show "next" and "previous" buttons on this
     * Swiper object.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showButtons = FALSE;

    /**
     * Flag which tells whether to show scrollbar on this Swiper object.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showScrollbar = FALSE;

    /**
     * Factory config.
     *
     * @access   public
     * @return   FieldFormatterSwiper
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new FieldFormatterSwiper();
    }

    /**
     * Format value.
     *
     * @access   public
     * @param    string $value
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function format($value)
    {
        return $this->sPrefix.$value.$this->sSuffix;
    }

    /**
     * Make formatting on an array value.
     *
     * @access   public
     * @param    array $values
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function formatArray(array $values)
    {
        $aFormattedValues = parent::formatArray($values);

        $sOutput = View::factory('base/view/field_formatter/swiper')
            ->bind('aValuesList', $aFormattedValues)
            ->bind('bButtons', $this->showButtons)
            ->bind('bPagination', $this->showPagination)
            ->bind('bScrollbar', $this->showScrollbar)
            ->render();

        return $sOutput;
    }

    /**
     * Show pagination in this Swiper.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function showPagination()
    {
        $this->showPagination = TRUE;

        return $this;
    }

    /**
     * Show "previous" and "next" buttons in this Swiper.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function showButtons()
    {
        $this->showButtons = TRUE;

        return $this;
    }

    /**
     * Show scrollbar in this Swiper.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function showScrollbar()
    {
        $this->showScrollbar = TRUE;

        return $this;
    }

}
