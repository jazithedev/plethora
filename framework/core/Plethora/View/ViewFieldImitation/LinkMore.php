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
class LinkMore extends View\ViewFieldImitation {

    /**
     * Name of the field from Model which value will be used as title of the
     * anchor.
     *
     * @access    private
     * @var        string
     * @sine      1.0.0-alpha
     */
    private $sModelFieldAsTitle = NULL;

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
    protected $sViewPath = 'base/view/imitation/link_more';

    /**
     * Set title prefix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sTitlePrefix = NULL;

    /**
     * Set title suffix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sTitleSuffix = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access     public
     * @return    LinkMore
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new LinkMore();
    }

    /**
     * Set field name which value will be used as title of the anchor.
     *
     * @access     public
     * @param    string $sTitleFromField
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setModelFieldAsTitle($sTitleFromField) {
        $this->sModelFieldAsTitle = $sTitleFromField;

        return $this;
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
     * Set title prefix.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setTitlePrefix($sValue) {
        $this->sTitlePrefix = $sValue;

        return $this;
    }

    /**
     * Set title suffix.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setTitleSuffix($sValue) {
        $this->sTitleSuffix = $sValue;

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
        $oModel   = $this->oEntity->getModel();

        $sURL   = $oModel->url();
        $mValue = $oModel->{$this->sModelFieldAsTitle};
        $sTitle = $this->sTitlePrefix.$mValue.$this->sTitleSuffix;

        $oContent->bind('sURL', $sURL);
        $oContent->bind('sTitle', $sTitle);
        $oContent->bind('sValue', $this->sValue);


        return $oContent;
    }

}
