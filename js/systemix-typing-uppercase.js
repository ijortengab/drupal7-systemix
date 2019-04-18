(function ($) {
Drupal.systemix = Drupal.systemix || {}
// Reference: 
// http://blog.vishalon.net/index.php/javascript-getting-and-setting-caret-position-in-textarea/
// https://stackoverflow.com/questions/5757101/change-input-to-upper-case
Drupal.systemix.typingUppercase = {
    getCaretPosition: function (ctrl){
        var CaretPos = 0;    // IE Support
        if (document.selection) {
            ctrl.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart('character', -ctrl.value.length);
            CaretPos = Sel.text.length;
        }
        // Firefox support
        else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
            CaretPos = ctrl.selectionStart;
        }
        return CaretPos;
    },
    setCaretPosition: function (ctrl, pos) {
        if (ctrl.setSelectionRange) {
            ctrl.focus();
            ctrl.setSelectionRange(pos,pos);
        }
        else if (ctrl.createTextRange) {
            var range = ctrl.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    }
}

Drupal.behaviors.systemixTypingUppercase = {
    attach: function (context, settings) {
        $('input.systemix-typing-uppercase', context).once('systemix-typing-uppercase', function () {
            $(this).on('keyup', function (e) {
                switch (e.keyCode) {
                    case 16: // Shift.
                    case 17: // Ctrl.
                    case 18: // Alt.
                    case 20: // Caps lock.
                    case 33: // Page up.
                    case 34: // Page down.
                    case 35: // End.
                    case 36: // Home.
                    case 37: // Left arrow.
                    case 38: // Up arrow.
                    case 39: // Right arrow.
                    case 40: // Down arrow.
                    case 9:  // Tab.
                    case 13: // Enter.
                    case 27: // Esc.
                      return true;
                }
                var caretPosition = Drupal.systemix.typingUppercase.getCaretPosition(this);
                this.value = this.value.toLocaleUpperCase()
                Drupal.systemix.typingUppercase.setCaretPosition(this, caretPosition);
            })
        })
    }
}

})(jQuery);
