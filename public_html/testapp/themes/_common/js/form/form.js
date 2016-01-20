function strpos(haystack, needle, offset) {
    // Finds position of first occurrence of a string within another
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/strpos
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Onno Marsman
    // +   bugfixed by: Daniel Esteban
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    var i = (haystack + '').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

$(function() {
    // Tooltip
    $('span.form_tooltip').tooltip({
        html: true,
        title: function() {
            var _this    = $(this);
            var id       = _this.attr('id').replace('tooltip-', '');
            var sContent = $('div#tooltip-content-' + id).html();

            return sContent;
        }
    });

    // Remove files
    $('form div.form-group button.remove_file').click(function() {
        var $this = $(this);

        $this.siblings('input.old_file').remove();
        $this.siblings('p').remove();
        $this.remove();
    });

    // Form clear
    $('form input[type=reset]').click(function() {
        var $form = $(this).closest('form');

        $form.find('input:text, input:password, input:file').val('').attr('value', '');
        $form.find('select option').removeAttr('selected', '');
        $form.find('textarea').val('');
        $form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    });

    // Form language checker
    var sDefaultLanguage = null;
    var $LangInput       = $('#form-language-checker-single-num-0').find('input');

    if($LangInput.length > 0) {
        sDefaultLanguage = $LangInput.attr('id').replace('form-language-checker-single-', '');
    }

    function checkLang($container, changeCheckbox) {
        var $input = $container.find('input');
        var sLang  = $input.attr('id').replace('form-language-checker-single-', '');
        var sId    = parseInt($container.attr('id').replace('form-language-checker-single-num-', ''));

        if(sId === 0) {
            return false;
        }

        if(changeCheckbox) {
            $input.prop("checked", !$input.prop("checked"));
        }

        if($container.hasClass('disabled')) {
            $container.removeClass('disabled');
            $container.addClass('active');
            $('img.multilang_button_' + sLang).show();
        } else {
            $container.removeClass('active');
            $container.addClass('disabled');
            $('img.multilang_button_' + sLang).hide();
            $('div.form-field-content div.form-field-lang-' + sLang).hide();
        }

        $('div.form-field-langbuttons img.multilang_button_' + sDefaultLanguage).click();
    }

    $('div#form-language-checker div.form-language-checker-single').each(function() {
        var $this  = $(this);
        var $input = $this.find('input');
        var sLang  = $input.attr('id').replace('form-language-checker-single-', '');

        if($input.is(':checked') === false) {
            $('img.multilang_button_' + sLang).hide();
        }

        $this.find('a').click(function() {
            checkLang($this, true);
        });

        $this.find('input').click(function() {
            checkLang($this, false);
        });
    });

    // TinyMCE
    if(typeof tinyMCE !== 'undefined') {
        tinyMCE.init({
            relative_urls: false,
            browser_spellcheck: true,
            language: 'pl',
            selector: '.tinymce_editor',
            height: 600,
            content_css: '/themes/jazipl/css/tinymce.css',
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect | link unlink anchor | image media | forecolor backcolor | code ",
            toolbar2: "fontsizeselect ",
            image_advtab: true,
            external_filemanager_path: "/filemanager/",
            filemanager_title: "Responsive Filemanager",
            external_plugins: {"filemanager": "/filemanager/plugin.min.js"},
            fontsize_formats: '8px 10px 12px 14px 18px 24px 36px',
            image_class_list: [
                {title: 'None', value: ''},
                {title: 'colorbox', value: 'colorbox'}
            ],
            link_class_list: [
                {title: 'None', value: ''},
                {title: 'colorbox', value: 'colorbox'}
            ],
            font_size_style_values: ["12px,13px,14px,16px,18px,20px"],
            formats: {
                clearfix: {selector: 'h1,h2,h3,h4,h5,h6,div,p', classes: 'clearfix'}
            },
            style_formats: [
                {
                    title: "Headers", items: [
                    {title: "Header 1", format: "h1"},
                    {title: "Header 2", format: "h2"},
                    {title: "Header 3", format: "h3"},
                    {title: "Header 4", format: "h4"},
                    {title: "Header 5", format: "h5"},
                    {title: "Header 6", format: "h6"}
                ]
                },
                {
                    title: "Inline", items: [
                    {title: "Bold", icon: "bold", format: "bold"},
                    {title: "Italic", icon: "italic", format: "italic"},
                    {title: "Underline", icon: "underline", format: "underline"},
                    {title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
                    {title: "Superscript", icon: "superscript", format: "superscript"},
                    {title: "Subscript", icon: "subscript", format: "subscript"},
                    {title: "Code", icon: "code", format: "code"}
                ]
                },
                {
                    title: "Blocks", items: [
                    {title: "Paragraph", format: "p"},
                    {title: "Blockquote", format: "blockquote"},
                    {title: "Div", format: "div"},
                    {title: "Pre", format: "pre"}
                ]
                },
                {
                    title: "Alignment", items: [
                    {title: "Left", icon: "alignleft", format: "alignleft"},
                    {title: "Center", icon: "aligncenter", format: "aligncenter"},
                    {title: "Right", icon: "alignright", format: "alignright"},
                    {title: "Justify", icon: "alignjustify", format: "alignjustify"},
                    {title: "Clearfix", format: "clearfix"}
                ]
                }
            ]
        });
    }
});