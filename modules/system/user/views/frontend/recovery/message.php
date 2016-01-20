<?php
/**
 * Content of e-mail with password reset link.
 *
 * @author         Krzysztof Trzos
 * @package        user
 * @subpackage     views\frontend\recovery
 * @since          2015-02-17
 * @version        2.1.0-dev
 */

use Plethora\Core;
use Plethora\Helper;
use Plethora\Route;

$siteName = Core::getAppName();
$contactUrl = Helper\Html::a(
    Route::factory('contact')->url(),
    __('CONTACT')
);

?>

<?php /* @var $sRecoveryCode string */ ?>
<?php /* @var $sLogin string */ ?>

<?php
$passRecoveryLink = Route::factory('password_recovery_code')->url(['code' => $sRecoveryCode]);
?>

<p><?= __('Hello :login', ['login' => $sLogin]) ?>,</p>
<p><?= __(
        'We were asked to change password on your account '.
        'registered on :site_name site. To do this, click on '.
        'the link below:', ['site_name' => $siteName]) ?></p>
<p style="text-align: center;">
    <a href="<?= $passRecoveryLink ?>" title="<?= __('Password recovery link') ?>"><?= $passRecoveryLink ?></a>
</p>
<p style="text-align: center;">
    <?= __(
        'If the above operation is not made by you, report it '.
        'to us through the :contact section.', ['contact' => $contactUrl]) ?>
</p>