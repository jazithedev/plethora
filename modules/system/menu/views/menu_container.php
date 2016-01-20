<?php
/**
 * One of main views used to generate container of the menu.
 * 
 * @author		Krzysztof Trzos
 * @package		menu
 * @subpackage	views
 * @since		1.0.0-dev, 2015-03-30
 * @version		1.0.3-dev, 2015-06-01
 */
?>

<?php /* @var $sMenuHeader string */ ?>
<?php /* @var $sMenuMachineName string */ ?>
<?php /* @var $oContent \Plethora\View */ ?>

<div class="block_menu menu_name_<?php echo $sMenuMachineName ?>">
	<?php if(!empty($sMenuHeader)): ?>
		<h2><?php echo $sMenuHeader ?></h2>
	<?php endif ?>
	<?php echo $oContent->render() ?>
</div>