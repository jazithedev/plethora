<?php
/**
 * Content of an e-mail.
 *
 * @author         Krzysztof Trzos
 * @package        basic
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Router;
use Plethora\Theme;

?>

<?php /* @var $sTitle string */ ?>
<?php /* @var $sContent string */ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <meta http-equiv='content-type' content='application/xhtml+xml; charset=utf-8' />
    <meta http-equiv='Content-Language' content='pl' />
    <meta name='language' content='polish' />
    <title><?php echo $sTitle ?></title>
    <style type="text/css">
        p { margin: 1em 0; }

        table.wrapper { width: 780px; height: auto; margin: 0 auto; border-spacing: 0; padding: 0; }

        table.wrapper div.footer { border-top: 1px solid #808080; padding-top: 5px; color: #808080; }
    </style>
</head>
<body style="font-family: Tahoma; font-size: 13px;">
<table class="wrapper" width="780" align="center">
    <tr>
        <td style="text-align: left;">
            <div style="text-align: center;">
                <img src="<?= Router::getBase().'/'.Theme::getThemePath() ?>/images/email/header.png" alt="<?php echo \Plethora\Core::getAppName() ?>" />
            </div>
            <div class="content">
                <?php echo $sContent ?>

                <div class="footer">
                    <span><?= __('This e-mail was generated automatically. Please, do not respond.') ?></span><br />
                    <span><?= __('All best,') ?></span><br />
                    <span class="appname"><?= \Plethora\Core::getAppName() ?></span>
                </div>
            </div>
        </td>
    </tr>
</table>
</body>
</html>