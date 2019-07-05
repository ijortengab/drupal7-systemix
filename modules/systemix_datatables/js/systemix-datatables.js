(function ($) {

    Drupal.behaviors.systemixDatatables = {
        attach: function (context, settings) {
            $.each(settings.systemixDatatables, function (i, settings) {
                // Saat ajax trigger, Command insert pasti menjalankan
                // Drupal.attachBehaviors(). Oleh karena itu, untuk menghindari
                // redraw table karena command insert, kita cegah dengan
                // pengecekan instance.
                // Redraw table nanti akan dilakukan oleh command
                // `systemixDatatablesRedraw`
                if (!Drupal.systemixDatatables.instance.hasOwnProperty(i)) {
                    Drupal.systemixDatatables.instance[i] = new Drupal.systemixDatatables.dataTables(settings);
                }
            });
        }
    };

    Drupal.systemixDatatables = Drupal.systemixDatatables || {};
    Drupal.systemixDatatables.instance = Drupal.systemixDatatables.instance || {};

    // Class Object.
    Drupal.systemixDatatables.dataTables = function (settings) {
        this.settings = settings;
        var selector = '.view-dom-id-' + settings.dom_id + ' table.systemix-datatables';
        this.$table = $(selector);
        this.$table.once(jQuery.proxy(this.drawtable, this));
        this.destroyTable = function () {
            this.$dataTable.destroy();
        }
    };

    Drupal.systemixDatatables.dataTables.prototype.drawtable = function () {
        // Izinkan module untuk menambah options.
        if (this.settings.additionalOptions) {
            var that = this;
            $.each(this.settings.additionalOptions, function (key, value) {
                $.extend(true, that.settings.options, Drupal[value])
            });
        }
        // Beri event agar module dapat mengubah tampilan element table.
        this.$dataTable = this.$table.on('init.dt', function () {
            Drupal.attachBehaviors(this);
        }).DataTable(this.settings.options)
        this.$dataTable.on('draw.dt', function () {
            Drupal.attachBehaviors(this);
        });
    }

    // Custom Command.
    Drupal.ajax.prototype.commands.systemixDatatablesRedraw = function (ajax, response, status) {
        if (response.key && response.settings) {
            var key = response.key;
            // Save memory.
            Drupal.systemixDatatables.instance[key].destroyTable();
            // Recreate.
            Drupal.systemixDatatables.instance[key] = new Drupal.systemixDatatables.dataTables(response.settings);
        }
    };
})(jQuery);
