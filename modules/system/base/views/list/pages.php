<?php
/**
 * This view generates pages for lists.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $oPager \Plethora\Helper\Pager */ ?>
<?php /* @var $sUrlName string */ ?>

<?php $aPages = $oPager->generatePages() ?>

<?php if(count($aPages) > 0): ?>
    <ul class="pager">
        <?php foreach($aPages as $aPage): ?>
            <?php
            $sPageLink = \Plethora\Router::currentUrlWithQueryParams(['page' => $aPage['id']]);

            switch($aPage['type']) {
                case 'previous':
                    ?>
                    <li class="previous">
                    <a href='<?php echo $sPageLink ?>'>&laquo; <?php echo __('previous', [], ['context' => 'page']) ?></a>
                    </li><?php
                    break;
                case 'next':
                    ?>
                    <li class="next">
                    <a href="<?php echo $sPageLink ?>"><?php echo __('next', [], ['context' => 'page']) ?> &raquo;</a>
                    </li><?php
                    break;
                case 'middle':
                    ?>
                    <li class="other">
                    <a class="stronnicowanie" href="<?php echo $sPageLink ?>"><?php echo $aPage['id'] ?></a>
                    </li><?php
                    break;
                case 'current':
                    ?>
                    <li class="current disabled">
                    <a class="stronnicowanie" href="<?php echo $sPageLink ?>"><?php echo $aPage['id'] ?></a>
                    </li><?php
                    break;
            }
            ?>
        <?php endforeach ?>
    </ul>
<?php endif ?>