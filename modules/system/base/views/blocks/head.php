<?php
/**
 * One of the primary Views of the project which is responsible for generating
 * HEAD tag of the page DOM.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\blocks
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $aCss array */ ?>
<?php /* @var $aJs array */ ?>
<?php /* @var $aMeta array */ ?>
<?php /* @var $sTitle string */ ?>

<?php foreach($aMeta as $aArray): ?>
    <?php
    $sTags = '';

    foreach($aArray as $sName => $sValue) {
        $sTags .= $sName.'="'.$sValue.'" ';
    }

    ?>
    <meta <?= trim($sTags) ?> />
<?php endforeach ?>

    <title><?= $sTitle ?></title>

<?php foreach($aCss as $sValue): ?>
    <link rel="stylesheet" href="<?= $sValue ?>" type="text/css" media="all"/>
<?php endforeach ?>

<?php foreach($aJs as $sValue): ?>
    <script type="text/javascript" src="<?= $sValue ?>"></script>
<?php endforeach ?>

<?php if(file_exists(PATH_PUBLIC.'favicon.ico')): ?>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<?php endif; ?>