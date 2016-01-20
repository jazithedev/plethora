<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oHeader \Plethora\View */ ?>
<?php /* @var $oContent \Plethora\View */ ?>
<?php /* @var $oFooter \Plethora\View */ ?>
<?php /* @var $menu \Plethora\View */ ?>

<?php
# get logged user
$user = Model\User::getLoggedUser();

# user anchor
$userAnchor = \Plethora\Helper\Link::factory()
    ->setTitle(__('User profile'))
    ->code($user->getName(), $user->getProfileURL());
?>

<div class="wrapper">
    <header class="main-header">
        <?php echo $oHeader->render() ?>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= $user->getImageStyled() ?>" class="img-circle" alt="<?= __('User Image') ?>">
                </div>
                <div class="pull-left info">
                    <p><?= $userAnchor ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <?= $menu->render() ?>
        </section>
    </aside>
    <?php echo $oContent->render() ?>
    <?php echo $oFooter->render() ?>
</div>