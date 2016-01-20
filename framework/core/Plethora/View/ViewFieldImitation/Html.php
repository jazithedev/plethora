<?php

namespace Plethora\View\ViewFieldImitation;

use Plethora\View;

/**
 * Class used to create field imitations in Entities View.
 *
 * @package        Plethora
 * @subpackage     View\ViewFieldImitation
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Html extends View\ViewFieldImitation {

    /**
     * Name of the field from Model which value will be used as value of the
     * anchor.
     *
     * @access    private
     * @var        string
     * @sine      1.0.0-alpha
     */
    private $sValue = NULL;

    /**
     * Path to the view of this field imitation.
     *
     * @access    private
     * @var        string
     * @sine      1.0.0-alpha
     */
    protected $sViewPath = 'base/view/imitation/basic';

    /**
     * Factory method.
     *
     * @static
     * @access     public
     * @return    Html
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new Html();
    }

    /**
     * Set value of the anchor.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setValue($sValue) {
        $this->sValue = $sValue;

        return $this;
    }

    /**
     * Get View with Imitation content.
     *
     * @access     public
     * @return    View
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getContentView() {
        $oContent = parent::getContentView();
        $oContent->bind('sContent', $this->sValue);

        return $oContent;
    }

}
