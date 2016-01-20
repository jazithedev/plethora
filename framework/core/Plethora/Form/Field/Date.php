<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Date field form field
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Date extends Form\Field {
    /**
     * Path to view for this type of form field.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/date';

    /**
     * Show days input?
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showDay = TRUE;

    /**
     * Show months input?
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showMonth = TRUE;

    /**
     * Show years input?
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showYear = TRUE;

    /**
     * Year interwal (from ... to)
     *
     * @access  private
     * @var     array    array(integer, integer)
     * @since   1.0.0-alpha
     */
    private $yearInterval = [1990, 2020];

    /**
     * Show months names?
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $showMonthsName = TRUE;

    /**
     * Separator which shows between inputs
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $separator = " ";

    /**
     * Array with month names
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $monthNames = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];

    /**
     * Default values of inputs
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $defaultValue = ['day' => NULL, 'month' => NULL, 'year' => NULL];

    /**
     * Constructor
     *
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form &$form) {
        parent::__construct($name, $form);

        $mValue = $this->getValue();

        if(empty($mValue)) {
            $this->setValue(['day' => '', 'month' => '', 'year' => '']);
        }
    }


    /**
     * Method which gets all Date field settings
     *
     * @access     public
     * @return    array
     * @version    1.0.0-alpha
     */
    public function getSettings() {
        return [
            'show_day'      => $this->showDay,
            'show_month'    => $this->showMonth,
            'show_year'     => $this->showYear,
            'year_interval' => $this->yearInterval];
    }

    /**
     * Hiding day field
     *
     * @access     public
     * @return    $this
     * @since    1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hideDay() {
        $this->showDay = FALSE;

        return $this;
    }

    /**
     * @access     public
     * @return    boolean
     * @since    1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isDayHidden() {
        return $this->showDay;
    }

    /**
     * Hiding month field
     *
     * @access     public
     * @return    $this
     * @since    1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hideMonth() {
        $this->showMonth = FALSE;

        return $this;
    }

    /**
     * @access     public
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isMonthHidden() {
        return $this->showMonth;
    }

    /**
     * Hiding year field
     *
     * @access     public
     * @return    $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function hideYear() {
        $this->showYear = FALSE;

        return $this;
    }

    /**
     * @access     public
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isYearHidden() {
        return $this->showYear;
    }

    /**
     * Show months in field as numbers (1 - 12)
     *
     * @access     public
     * @return    $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function hideMonthsName() {
        $this->showMonthsName = FALSE;

        return $this;
    }

    /**
     * @access     public
     * @return    boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isMonthNamesHidden() {
        return $this->showMonthsName;
    }

    /**
     * Setting year interval
     *
     * @access   public
     * @param    integer $iFrom
     * @param    integer $iTo
     * @return   Date
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setYearInterval($iFrom, $iTo) {
        if(is_int($iFrom) && is_int($iTo) && $iFrom < $iTo) {
            $this->yearInterval = [$iFrom, $iTo];
        }

        return $this;
    }

    /**
     * @access     public
     * @return    array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getYearInterval() {
        return $this->yearInterval;
    }

    /**
     * Setting month names (different than default, for ex. in other languages)
     *
     * @access     public
     * @param    array $aArrayOfNames
     * @return    Date
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setMonthNames($aArrayOfNames) {
        if(is_array($aArrayOfNames) && count($aArrayOfNames) == 12) {
            $this->monthNames = $aArrayOfNames;
        }

        return $this;
    }

    /**
     * @access     public
     * @return    array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMonthNames() {
        return $this->monthNames;
    }

    /**
     * Setting separator which separates date fields in HTML
     *
     * @access     public
     * @param    string $sSeparator
     * @return    Date
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setSeparator($sSeparator) {
        $this->separator = $sSeparator;

        return $this;
    }

    /**
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getSeparator() {
        return $this->separator;
    }
}
