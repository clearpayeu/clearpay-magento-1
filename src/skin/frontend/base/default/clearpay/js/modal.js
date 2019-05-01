(function($) {

    $(document).on('ready', function() {
        $('.clearpay-what-is-modal-trigger').fancybox({
            afterShow: function() {
                $('#clearpay-what-is-modal').find('.close-clearpay-button').on('click', function(event) {
                    event.preventDefault();
                    $.fancybox.close();
                })
            }
        })
    });

})(jQuery);
