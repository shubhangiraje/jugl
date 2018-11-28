(function ($, window, i) {

    'use strict';

    function ReStable(table, options) {
        var self = this;

        this.$table = $(table),
        this.$responsiveTable,
        this.resizeTimeout = false,
        this.options = $.extend({
            rowHeaders: true,
            maxWidth: 480
        }, options);

        this.windowResizeWrapper = function() {
            if (self.resizeTimeout)
                clearTimeout(self.resizeTimeout);
            self.resizeTimeout = setTimeout(function() {
                self.resizeTableHandler();
            }, 20);
        };

        this.windowResizeWrapper();

        $(window).on('resize.ReStable', this.windowResizeWrapper);
    }

    ReStable.prototype = {
        resizeTableHandler: function() {
            if ($(window).width() > parseInt(this.options.maxWidth, 10)) {
                this.$table.show();
                if (this.$responsiveTable) {
                    this.$responsiveTable.hide();
                }
            } else {
                this.$table.hide();
                if (this.$responsiveTable)
                    this.$responsiveTable.show();
                else
                    this.buildResponsiveTable();
            }
        },

        buildResponsiveTable: function() {
            var self = this;

            this.destroyResponsiveTable();

            // Build titles of lists
            var $headers = $('tr:first td, tr:first th', this.$table);
            if (this.options.rowHeaders)
                $headers = $headers.not(':first');

            var cols = [];
            $headers.each(function() {
                cols.push($(this).html());
            });

            var data = {};

            $('tr', this.$table).not(':first').each(function(rowIndex) {
                var $row = $(this);

                $.each(cols, function(columnIndex, value) {
                    if (self.options.rowHeaders) {
                        if (!data[value]) {
                            data[value] = {};
                        }
                        data[value][$('td:eq(0)', $row).html()] = $('td:eq(' + (columnIndex + 1) + ')', $row).html();
                    } else {
                        if (!data[rowIndex + 1]) {
                            data[rowIndex + 1] = {};
                        }
                        data[rowIndex + 1][value] = $('td:eq(' + columnIndex + ')', $row).html();
                    }

                });
            });

            var $list = $('<ul/>', {
                class: 'responsive-table' + (self.options.rowHeaders ? ' rh' : ' nrh')
            });

            $.each(data, function(rowIndex, row) {
                var $block = $('<li/>', {
                    html: (self.options.rowHeaders) ? '<span class="title">' + rowIndex + '</span>' : ''
                }).appendTo($list),
                $row = $('<ul/>').appendTo($block);

                $.each(row, function(columnIndex, column) {
                    $('<li/>', {
                        html: '<span class="row-header">' + columnIndex + '</span> <span class="row-data">' + column + '</span>'
                    }).appendTo($row);
                });
            });

            $list.insertBefore(this.$table);
            this.$responsiveTable = $list;
            this.$table.trigger('list-builded.ReStable');
        },

        destroyResponsiveTable: function() {
            if (this.$responsiveTable) {
                this.$responsiveTable.remove();
                this.$responsiveTable = null;
            }
        },

        destroy: function() {
            $(window).off('resize.ReStable', this.windowResizeWrapper);
            this.destroyResponsiveTable();
        }
    };

    $.fn.ReStable = function(options, args) {
        var dataKey = 'ReStable';
        return this.each(function () {
            var $table = $(this),
                instance = $table.data(dataKey);

            if (typeof options == 'string') {
                if (instance && typeof instance[options] == 'function')
                    instance[options](args);
            } else {
                if (instance && instance.destroy)
                    instance.destroy();
                instance = new ReStable(this, options);
                $table.data(dataKey, instance);
            }
        });
    };

    $.ReStable = function(options) {
        $('table').ReStable(options);
    };

})(jQuery, this, 0);
