<?php
/**
 * @author         Krzysztof Trzos
 * @package        views
 * @subpackage     view\field_formatter
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $aList array */ ?>

<?php if(!empty($aList)): ?>
    <ul>
        <?php foreach($aList as $sLiContent): ?>
            <li><?= $sLiContent ?></li>
        <?php endforeach ?>
    </ul>
<?php endif; ?>