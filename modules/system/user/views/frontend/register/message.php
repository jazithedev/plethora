<?php
/**
 * @author     Krzysztof Trzos <k.trzos@jazi.pl>
 * @package    user
 * @subpackage views
 * @since      1.0.0
 * @version    2.1.0-dev
 */
?>

<?php /* @var $sActivationCode string */ ?>
<?php /* @var $sLogin string */ ?>

<p>Witaj <b><?= $sLogin ?></b>!</p>
<p>Na tego maila zarejestrowane zostało konto portalu <b><?= \Plethora\Core::getAppName() ?></b>. Nie jest jednak ono w pełni aktywne i, aby zakończyć rejestrację, należy kliknąć w poniższy link:</p>
<p style="text-align: center;">
	<a href="<?= \Plethora\Route::factory('account_activation')->url(['code' => $sActivationCode]) ?>" title="Link aktywacyjny konta">
		<?= \Plethora\Route::factory('account_activation')->url(['code' => $sActivationCode]) ?>
	</a>
</p>
<p style="border-top: 1px solid rgb(102, 102, 102); font-size: 12px; padding-top: 5px; text-align: center;">
	Jeżeli rejestracja konta nie została dokonania przez Ciebie, zgłoś to przez dział
	<a href="<?= \Plethora\Route::factory('contact')->url() ?>" title="<?= \Plethora\Core::getAppName() ?> - kontakt">KONTAKT</a>, a usuniemy Twój e-mail z naszej bazy danych.
</p>