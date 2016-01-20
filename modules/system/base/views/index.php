<?php
/**
 * Main View of a page.
 *
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oHead \Plethora\View */ ?>
<?php /* @var $oBody \Plethora\View */ ?>
<?php /* @var $sBodyClasses string */ ?>

<!DOCTYPE html>
<html lang="<?php echo \Plethora\Router::getLang() ?>">
<head>
    <?php echo $oHead->render() ?>
</head>
<body class="<?php echo trim($sBodyClasses) ?>">
<?php echo $oBody->render() ?>
</body>
</html>