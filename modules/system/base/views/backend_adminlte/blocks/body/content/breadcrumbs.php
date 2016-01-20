<?php
/**
 * Main View for breadcrumbs.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $aBreadcrumbs array */ ?>

<ol class="breadcrumb">
    <?php foreach($aBreadcrumbs as $i => $aBreadcrumb): ?>
        <?php if(isset($aBreadcrumb['url'])): ?>
            <li>
                <a
                    href="<?= $aBreadcrumb['url'] ?>"
                    title="<?= $aBreadcrumb['name'] ?>"
                ><?= ($i == 0 ? '<i class="fa fa-dashboard"></i> ' : '') ?><?= $aBreadcrumb['name'] ?></a>
            </li>
        <?php else: ?>
            <li class="active"><?= $aBreadcrumb['name'] ?></li>
        <?php endif ?>
    <?php endforeach ?>
</ol>