<?php
/**
 * Main View for "Swiper" entity fields formatter.
 * 
 * @author		Krzysztof Trzos
 * @package		views
 * @subpackage	view\field_formatter
 * @since		1.0.0-alpha
 * @version		1.0.0-alpha
 */
?>

<?php /* @var $aValuesList array */ ?>
<?php /* @var $bButtons boolean */ ?>
<?php /* @var $bPagination boolean */ ?>
<?php /* @var $bScrollbar boolean */ ?>

<?php ?>

<?php if(!empty($aValuesList)): ?>
	<div class="swiper-formatter">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<?php foreach($aValuesList as $i => $sSingleValue): ?>
					<div class="swiper-slide"><?= $sSingleValue ?></div>
				<?php endforeach ?>
			</div>
			<?php if($bButtons): ?>
				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
			<?php endif ?>
			<?php if($bScrollbar): ?>
				<div class="swiper-scrollbar"></div>
			<?php endif ?>
		</div>
		<?php if($bPagination): ?>
			<div class="swiper-pagination-wrapper">
				<div class="swiper-pagination"></div>
			</div>
		<?php endif ?>
	</div>
<?php endif; ?>