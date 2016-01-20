var PlethoraAfterAdminLteLoad = {
    /**
     * Function changes content min-height when dev-toolbar is on.
     * 
     * @returns {boolean}
     */
    fixContentWhenToolbar: function() {
        if($('#dev_toolbar').length === 0) {
            return false;
        }

        var contentWrapper = $('div.content-wrapper');
        var value          = parseInt(contentWrapper.css('min-height'));

        contentWrapper.css('min-height', value - 30);

        $(window).resize(function() {
            var contentWrapper = $('div.content-wrapper');
            var value          = parseInt(contentWrapper.css('min-height'));

            contentWrapper.css('min-height', value - 30);
        });

        return true;
    }
};

$(function() {
    PlethoraAfterAdminLteLoad.fixContentWhenToolbar();
});