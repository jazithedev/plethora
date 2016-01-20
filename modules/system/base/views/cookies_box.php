<?php
/**
 * @author  Krzysztof Trzos
 * @package base
 * @since   1.0.0-alpha
 * @version 1.0.0-alpha
 */
?>

<?php if(!isset($_COOKIE['cookieswarn'])): ?>
    <div class="cookies_box">
        <div>
            <p>
                Ta strona używa cookie. Dowiedz się więcej o celu ich używania z naszej
                <a href="<?php echo \Plethora\Route::factory('page')->url(['rewrite' => 'politykaprywatnosci']) ?>" title="polityka prywatności">polityki prywatności</a>.
                Korzystając ze strony wyrażasz zgodę na używanie cookie, zgodnie z aktualnymi ustawieniami przeglądarki.
            </p>
            <span class="close">x</span>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            $('div.cookies_box span.close').click(function() {
                $(this).closest('div.cookies_box').remove();

                var exdate = new Date();
                exdate.setDate(exdate.getDate() + 31);
                document.cookie = 'cookieswarn=yes; expires=' + exdate.toUTCString() + ';path=/';
            });
        });
    </script>
<?php endif ?>