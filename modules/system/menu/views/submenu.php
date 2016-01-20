<?php
/**
 * Main view used to generate menu.
 *
 * @author      Krzysztof Trzos
 * @package     menu
 * @subpackage  views
 * @since       1.0.0-dev
 * @version     1.3.0-dev
 */

use Plethora\View;

?>

<?php /* @var $submenuClasses array */ ?>
<?php /* @var $entries array */ ?>
<?php /* @var $level integer */ ?>

<ul class="menu menu_level_<?php echo $level ?> <?= $submenuClasses ?>">
    <?php foreach($entries as $oEntry): /* @var $oEntry View */ ?>
        <?php echo $oEntry->render() ?>
    <?php endforeach ?>
</ul>