<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oContent \Plethora\View */ ?>
<?php /* @var $oRight \Plethora\View */ ?>

<div class='left_content'>
    <?php echo ($oContent instanceof \Plethora\View) ? $oContent->render() : '' ?>
</div>
<div class='right_content'>
    <?php echo $oRight->render() ?>
</div>
<p class='stretch'></p>