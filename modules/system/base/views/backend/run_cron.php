<?php
/**
 * Here you can run Cron jobs manually.
 *
 * @author     Krzysztof Trzos
 * @package    base
 * @subpackage views
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
?>

<div class="run_cron_container">
	<p><?= __('Click above button to run all cron jobs manually.') ?></p>

	<p>
		<button class="btn btn-warning" type="button" id="run_cron"><?= __('run Cron jobs') ?></button>
	</p>
	<div id="cron_result"></div>
</div>

<script type="text/javascript">
	$(function() {
		$('#run_cron').click(function() {
			var $this = $(this);
			var oldLabel = $this.text();

			$('div#cron_result').html('');
			$this.text('<?= __('loading...') ?>');

			$.ajax({
				url: '<?php echo \Plethora\Route::factory('cron')->url(['token' => \Plethora\Config::get('base.cron_token')]) ?>'
			}).done(function(output) {
				$this.text(oldLabel);

				console.log(output);

				if(output === '') {
					output = 'All cron jobs have been executed.';
				}

				$('div#cron_result').html(output);
			});
		});
	});
</script>