<?php
/**
 * @author     Krzysztof Trzos <k.trzos@jazi.pl>
 * @package    user
 * @subpackage views
 * @since      1.0.0
 * @version    2.1.0-dev
 */
?>

<?php /* @var $bActivated boolean */ ?>

<?php if($bActivated): ?>
	<p class="correct_activation">Gratulacje! Twoje konto zostało w pełni aktywowane!</p>
	<p class="act_txt">Możesz się teraz zalogować.</p>
<?php else: ?>
	<p class="incorrect_activation">Błąd!</p>
	<p class="act_txt">Konto nie zostało aktywowane. Powodem tego może być jeden z poniższych punktów:</p>
	<ul class="func_list">
		<li>konto zostało już wcześniej aktywowane;</li>
		<li>link aktywacyjny nie został w całości przekopiowany do pola adresu przeglądarki;</li>
	</ul>
	<p class="act_txt">Jeżeli żaden z powyższych powodów nie rozwiązuje problemu aktywacji konta, skontaktuj się z nami poprzez dział
		<a href="<?= \Plethora\Route::factory('contact')->url() ?>" title="Dział kontakt">KONTAKT</a>.
	</p>
<?php endif ?>