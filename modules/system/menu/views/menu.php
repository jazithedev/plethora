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

use Plethora\Helper\Attributes;
use Plethora\View;

?>

<?php /* @var $attributes Attributes */ ?>
<?php /* @var $entries array */ ?>

<ul <?= $attributes->renderAttributes() ?>>
    <?php foreach($entries as $entry): /* @var $entry View */ ?>
        <?php echo $entry->render() ?>
    <?php endforeach ?>
</ul>