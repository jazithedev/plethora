$(function() {
    /* form fields flag images */
    $('div.form form img.multilang_button').click(function() {
        var $this = $(this);
        var lang  = $this.attr('data-lang');
        var box   = $this.closest('div.form-group');

        box.find('img.multilang_checked').removeClass('multilang_checked');
        $this.addClass('multilang_checked');

        box.find('div.form-field-lang').css('display', 'none');
        box.find('div.form-field-lang-' + lang).css('display', 'block');
    });

    /* form field values increase */
    $('div.form form div.new_value button').click(function() {
        var $this            = $(this);
        var iMax             = parseInt($this.attr('data-max'));
        var sLang            = $this.closest('div.form-field-lang').data('lang');
        var sName            = $this.closest('div.form-field').data('fieldname');
        var $valuesContainer = $this.closest('div.form-field-lang').children('div.values');
        var $allValues       = $valuesContainer.children('div.form-field-value');
        var iValuesAmount    = $allValues.size();

        if(iMax === 0 || iValuesAmount < iMax) {
            var iLastValueID = 0;
            var sNewField    = $this.closest('div.form').find('div.field_patterns div.field-name-' + sName).html();

            if(iValuesAmount > 0) {
                iLastValueID = parseInt($allValues.last().attr('class').replace('form-field-value ', '').replace('form-field-value-no-', '')) + 1;
            }

            sNewField = sNewField.replace(new RegExp('LANGUAGE', 'g'), sLang);
            sNewField = sNewField.replace(new RegExp('NUMBER', 'g'), iLastValueID);

            $valuesContainer.append(sNewField);

            if(iMax > 0 && iMax === (iValuesAmount + 1)) {
                $this.addClass('hidden');
            }
        }
    });

    /* form field values increase */
    $('div.form form').on('click', 'div.form-field-delete button', function() {
        var $this   = $(this);
        var $addBtn = $this.closest('div.form-field-lang').find('div.new_value button');

        var $fieldValue = $(this).closest('div.form-field-value');
        var $prev       = $fieldValue.prev();

        if($prev.hasClass('form-field-errors')) {
            $prev.remove();
        }

        $fieldValue.remove();

        if($addBtn.hasClass('hidden')) {
            $addBtn.removeClass('hidden');
        }
    });

    // backend list - sortable / draggable
    $('div#sorted_list > ol').nestedSortable({
        handle: 'div span.move',
        items: 'li',
        toleranceElement: '> div',
        isTree: true
    });

    $('button#sort_save_conf').click(function(e) {
        e.preventDefault();

        var $this              = $(this);
        var sOldInputVal       = $this.text();
        var sSerializedObjects = $('div#sorted_list > ol').nestedSortable('serialize', {toArray: 0});
        var sModel             = $this.siblings('input#model_name').val();

        $this.text('Loading...');

        $.ajax({
            url: '/a/ajax/sortlist',
            dataType: 'json',
            data: {objects: sSerializedObjects, model: sModel},
            type: 'POST'
        }).done(function(response) {
            $this.text(sOldInputVal);
        });
    });
});

var AdminLTEOptions = {
    sidebarExpandOnHover: true,
    animationSpeed: 300
};