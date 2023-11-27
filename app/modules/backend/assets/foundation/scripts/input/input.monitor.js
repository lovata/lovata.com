/*
 * The form change monitor API.
 *
 * - Documentation: ../docs/input-monitor.md
 */
+function ($) { "use strict";

    var Base = $.oc.foundation.base,
        BaseProto = Base.prototype;

    var ChangeMonitor = function (element, options) {
        this.$el = $(element);

        this.paused = false;
        this.options = options || {};

        $.oc.foundation.controlUtils.markDisposable(element);

        Base.call(this);

        this.init();
    }

    ChangeMonitor.prototype = Object.create(BaseProto);
    ChangeMonitor.prototype.constructor = ChangeMonitor;

    ChangeMonitor.prototype.init = function() {
        this.$el.on('change', this.proxy(this.change));
        this.$el.on('unchange.oc.changeMonitor', this.proxy(this.unchange));
        this.$el.on('click ajaxSuccess', '[data-change-monitor-commit]', this.proxy(this.unchange));
        this.$el.on('pause.oc.changeMonitor', this.proxy(this.pause));
        this.$el.on('resume.oc.changeMonitor', this.proxy(this.resume));
        this.$el.on('pauseUnloadListener.oc.changeMonitor', this.proxy(this.pauseUnloadListener));
        this.$el.on('resumeUnloadListener.oc.changeMonitor', this.proxy(this.resumeUnloadListener));
        this.$el.on('keyup input paste', 'input:not(.ace_search_field), textarea:not(.ace_text-input)', this.proxy(this.onInputChange));

        $('input:not([type=hidden]):not(.ace_search_field), textarea:not(.ace_text-input)', this.$el).each(function() {
            $(this).data('oldval.oc.changeMonitor', $(this).val());
        });

        $(window).on('beforeunload', this.proxy(this.onBeforeUnload));
        addEventListener('page:before-visit', this.proxy(this.onBeforeUnloadTurbo))

        this.$el.one('dispose-control', this.proxy(this.dispose));
        this.$el.trigger('ready.oc.changeMonitor');
    }

    ChangeMonitor.prototype.dispose = function() {
        if (this.$el === null) {
            return;
        }

        this.unregisterHandlers();

        this.$el.removeData('oc.changeMonitor');
        this.$el = null;
        this.options = null;

        BaseProto.dispose.call(this);
    }

    // static
    ChangeMonitor.globallyDisabled = false;

    ChangeMonitor.disable = function() {
        ChangeMonitor.globallyDisabled = true;
    }

    ChangeMonitor.enable = function() {
        ChangeMonitor.globallyDisabled = false;
    }

    ChangeMonitor.prototype.unregisterHandlers = function() {
        this.$el.off('change', this.proxy(this.change));
        this.$el.off('unchange.oc.changeMonitor', this.proxy(this.unchange));
        this.$el.off('click ajaxSuccess', '[data-change-monitor-commit]', this.proxy(this.unchange));
        this.$el.off('pause.oc.changeMonitor ', this.proxy(this.pause));
        this.$el.off('resume.oc.changeMonitor ', this.proxy(this.resume));
        this.$el.off('keyup input paste', 'input:not(.ace_search_field), textarea:not(.ace_text-input)', this.proxy(this.onInputChange));
        this.$el.off('dispose-control', this.proxy(this.dispose));

        $(window).off('beforeunload', this.proxy(this.onBeforeUnload))
        removeEventListener('page:before-visit', this.proxy(this.onBeforeUnloadTurbo))
    }

    ChangeMonitor.prototype.change = function(ev, inputChange) {
        if (this.paused || ChangeMonitor.globallyDisabled) {
            return;
        }

        if (ev.target.className === 'ace_search_field') {
            return;
        }

        if (!inputChange) {
            var type = $(ev.target).attr('type');
            if (type === 'text' || type === 'password') {
                return;
            }
        }

        if (!this.$el.hasClass('oc-data-changed')) {
            this.$el.trigger('changed.oc.changeMonitor');
            this.$el.addClass('oc-data-changed');
        }
    }

    ChangeMonitor.prototype.unchange = function() {
        if (this.paused || ChangeMonitor.globallyDisabled) {
            return;
        }

        if (this.$el.hasClass('oc-data-changed')) {
            this.$el.trigger('unchanged.oc.changeMonitor');
            this.$el.removeClass('oc-data-changed');
        }
    }

    ChangeMonitor.prototype.onInputChange = function(ev) {
        if (this.paused || ChangeMonitor.globallyDisabled) {
            return;
        }

        var $el = $(ev.target);
        if ($el.data('oldval.oc.changeMonitor') !== $el.val()) {
            $el.data('oldval.oc.changeMonitor', $el.val());
            this.change(ev, true);
        }
    }

    ChangeMonitor.prototype.pause = function() {
        this.paused = true;
    }

    ChangeMonitor.prototype.resume = function() {
        this.paused = false;
    }

    ChangeMonitor.prototype.pauseUnloadListener = function() {
        this.unloadListenerPaused = true;
    }

    ChangeMonitor.prototype.resumeUnloadListener = function() {
        this.unloadListenerPaused = false;
    }

    ChangeMonitor.prototype.shouldWarn = function() {
        return $.contains(document.documentElement, this.$el.get(0)) &&
            this.$el.hasClass('oc-data-changed') &&
            !this.unloadListenerPaused;
    }

    ChangeMonitor.prototype.onBeforeUnload = function(event) {
        if (this.shouldWarn()) {
            event.preventDefault();
            return event.returnValue = '';
        }
    }

    // Disable PJAX to fallback to the browser unload event
    ChangeMonitor.prototype.onBeforeUnloadTurbo = function(event) {
        const { url, action } = event.detail;
        if (this.shouldWarn() && action === 'advance') {
            event.preventDefault();
            location.assign(url);
        }
    }

    ChangeMonitor.DEFAULTS = {
    }

    // CHANGEMONITOR PLUGIN DEFINITION
    // ===============================

    var old = $.fn.changeMonitor;

    $.fn.changeMonitor = function (option) {
        return this.each(function () {
            var $this = $(this);
            var data  = $this.data('oc.changeMonitor');
            var options = $.extend({}, ChangeMonitor.DEFAULTS, $this.data(), typeof option === 'object' && option);

            if (!data) $this.data('oc.changeMonitor', (data = new ChangeMonitor(this, options)));
        });
    }

    $.fn.changeMonitor.Constructor = ChangeMonitor;

    // CHANGEMONITOR NO CONFLICT
    // ===============================

    $.fn.changeMonitor.noConflict = function () {
        $.fn.changeMonitor = old;
        return this;
    }

    // CHANGEMONITOR DATA-API
    // ===============================

    $(document).render(function() {
        $('[data-change-monitor]').changeMonitor();
    });

    // Used to globally disable change monitor by others
    oc.changeMonitor = ChangeMonitor;

}(window.jQuery);
