<?php /* @version 1.0.1, 2014-11-27 */ ?>
<?php /* @var $oUser \Model\User */ ?>

<?php if(\Plethora\Router::getParam('id') == \Plethora\Session::get('uid')): ?>
    <p style="text-align: center;">
        <a href="<?php echo \Plethora\Route::factory('user_profile_edit')->url() ?>" title="<?= __('Edit profile') ?>">
            [ <?= __('Edit profile') ?> ]
        </a>
    </p>
<?php endif ?>

<div class="user_profile">
    <table>
        <tbody>
        <tr>
            <td><?php echo __('Firstname') ?>:</td>
            <td><?php echo \Plethora\Helper\String::placeholder($oUser->getFirstname(), '-') ?></td>
        </tr>
        <tr>
            <td><?php echo __('Lastname') ?>:</td>
            <td><?php echo \Plethora\Helper\String::placeholder($oUser->getLastname(), '-') ?></td>
        </tr>
        <tr>
            <td><?php echo __('Nickname') ?>:</td>
            <td><?php echo \Plethora\Helper\String::placeholder($oUser->getNickname(), '-') ?></td>
        </tr>
        <tr>
            <td><?php echo __('City') ?>:</td>
            <td><?php echo \Plethora\Helper\String::placeholder($oUser->getCity(), '-') ?></td>
        </tr>
        <tr>
            <td><?php echo __('Description') ?>:</td>
            <td><?php echo \Plethora\Helper\String::placeholder($oUser->getDescription(), '-') ?></td>
        </tr>
        </tbody>
    </table>
</div>