<?php
/**
 * This view generates select input (and its JavaScript code) which values indicated on amount of values in the list.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/list
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<div class="results_amount_container" style="text-align: right;">
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="get">
        <label for="results_amount"><?= __('Results amount:') ?></label>
        <select name="results" id="results_amount" class="form-control input-sm"
                style="width: auto; display: inline-block;">
            <?php
            $sQueryParam = filter_input(INPUT_GET, 'results', FILTER_VALIDATE_INT);
            $aValues     = [15, 30, 50, 100];

            foreach($aValues as $iValue) {
                echo '<option '.($iValue === $sQueryParam ? 'selected="selected"' : NULL).' value="'.$iValue.'">'.$iValue.'</option>';
            }

            ?>
        </select>
    </form>
</div>

<script type="text/javascript">
    $(function() {
        $('#results_amount').change(function() {
            var $this   = $(this);
            var oParams = oFramework.getQueryStringParams();

            oParams.results = $this.val();

            window.location.href = window.location.pathname + '?' + $.param(oParams);
        });
    });
</script>