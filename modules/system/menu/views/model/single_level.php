<?php
/**
 * This view is used to generate one level of the menu.
 *
 * @author      Krzysztof Trzos
 * @package     menu
 * @subpackage  views
 * @since       1.2.0-dev
 * @version     1.3.0-dev
 */

use Plethora\Helper;
use Plethora\View;

?>

<?php /* @var $sRouteTitle string */ ?>
<?php /* @var $sRouteName string */ ?>
<?php /* @var $sPath string */ ?>
<?php /* @var $aRouteParams array */ ?>
<?php /* @var $oSiblings View */ ?>
<?php /* @var $aParameters array */ ?>

<li <?= Helper\Attributes::factory()->renderAttributes($aParameters) ?>>
    <?= Helper\Html::a($sPath, $sRouteTitle) ?>
    <?php if($oSiblings instanceof View): ?>
        <?= $oSiblings->render() ?>
    <?php endif ?>
</li>