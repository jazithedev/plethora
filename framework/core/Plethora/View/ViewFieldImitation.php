<?php

namespace Plethora\View;

use Plethora\View;

/**
 * Class used to create field imitations in Entities View.
 *
 * @package        Plethora
 * @subpackage     View
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ViewFieldImitation {

    /**
     * Path to field imitation container view.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sBaseViewPath = 'base/view/imitation';

    /**
     * Path to field imitation view.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sViewPath = 'base/view/imitation/basic';

    /**
     * Field View parameters.
     *
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $aParameters = [];

    /**
     * Field imitation label.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sLabel = NULL;

    /**
     * Entity View in which this particular field imitation will be created.
     *
     * @access  protected
     * @var     ViewEntity
     * @since   1.0.0-alpha
     */
    protected $oEntity = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   ViewFieldImitation
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new ViewFieldImitation();
    }

    /**
     * Set parameter to the View for this imitation field.
     *
     * @access   public
     * @param    string $sName
     * @param    string $mValue
     * @return   ViewFieldImitation
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setParameter($sName, $mValue) {
        $this->aParameters[$sName] = $mValue;

        return $this;
    }

    /**
     * Set label for this field imitation.
     *
     * @access   public
     * @param    string $sLabel
     * @return   ViewFieldImitation
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLabel($sLabel) {
        $this->sLabel = $sLabel;

        return $this;
    }

    /**
     * Set Entity View for this imitation.
     *
     * @access   public
     * @param    ViewEntity $oEntity
     * @return   ViewFieldImitation
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setEntity(ViewEntity $oEntity) {
        $this->oEntity = $oEntity;

        return $this;
    }

    /**
     * Get View with Imitation content.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getContentView() {
        $oContent = View::factory($this->sViewPath);

        foreach($this->aParameters as $sParamName => $mParamValue) {
            $oContent->set($sParamName, $mParamValue);
        }

        return $oContent;
    }

    /**
     * Return field imitation View.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getView() {
        $sParentClass = get_class();
        $sCalledClass = get_called_class();
        $sClass       = str_replace([$sParentClass, '\\'], ['', ''], $sCalledClass);

        return View::factory($this->sBaseViewPath)
            ->bind('sLabel', $this->sLabel)
            ->set('oContent', $this->getContentView())
            ->set('sClass', strtolower($sClass));
    }

}
