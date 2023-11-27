/*
 * SimpleList control.
 *
 * Data attributes:
 * - data-control="simplelist" - enables the simplelist plugin
 *
 * JavaScript API:
 * $('#simplelist').simplelist()
 *
 */
+function ($) { "use strict";

    var SimpleList = function (element, options) {

        var $el = this.$el = $(element);

        this.options = options || {};

        // Make each list inside sortable
        if ($el.hasClass('is-sortable')) {
            var sortableOptions = {
                distance: 10
            }

            if (this.options.sortableHandle) {
                sortableOptions.handle = this.options.sortableHandle;
            }

            if ($el.find('.drag-handle').length > 0) {
                sortableOptions.handle = '.drag-handle';
            }

            $el.find('> ul, > ol').each(function() {
                Sortable.create(this, sortableOptions);
            });
        }

        // Inject a scrollbar container
        if ($el.hasClass('is-scrollable')) {
            $el.wrapInner($('<div />').addClass('control-scrollbar'));
            var $scrollbar = $el.find('>.control-scrollbar:first');
            $scrollbar.scrollbar();
        }
    }

    SimpleList.DEFAULTS = {
        sortableHandle: null
    }

    // SIMPLE LIST PLUGIN DEFINITION
    // ============================

    var old = $.fn.simplelist

    $.fn.simplelist = function (option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('oc.simplelist')
            var options = $.extend({}, SimpleList.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.simplelist', (data = new SimpleList(this, options)))
        })
      }

    $.fn.simplelist.Constructor = SimpleList

    // SIMPLE LIST NO CONFLICT
    // =================

    $.fn.simplelist.noConflict = function () {
        $.fn.simplelist = old
        return this
    }

    // SIMPLE LIST DATA-API
    // ===============

    $(document).render(function(){
        $('[data-control="simplelist"]').simplelist()
    })

}(window.jQuery);
