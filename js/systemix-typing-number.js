(function ($) {
Drupal.behaviors.systemixTypingNumber = {
    attach: function (context, settings) {
        $('input.systemix-typing-number', context).once('systemix-typing-number', function () {
            $(this).inputFilter(function (value) {
                return /^\d*$/.test(value);
            });
        })
    }
}
})(jQuery);
