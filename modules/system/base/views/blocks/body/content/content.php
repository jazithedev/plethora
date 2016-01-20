<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oContent \Plethora\View */ ?>

<?php echo ($oContent instanceof \Plethora\View) ? $oContent->render() : '' ?>