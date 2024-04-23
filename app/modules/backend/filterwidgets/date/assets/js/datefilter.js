/*
 * DateFilter plugin
 *
 * Data attributes:
 * - data-control="datefilter" - enables the plugin on an element
 * - data-data-locker="input#locker" - Input element to store and restore the chosen color
 *
 * JavaScript API:
 * $('div#someElement').dateFilter({ dataLocker: 'input#locker' })
 *
 * Dependencies:
 * - Some other plugin (filename.js)
 */

+function ($) { "use strict";
    var Base = $.oc.foundation.base,
        BaseProto = Base.prototype;

    var DateFilter = function(element, options) {
        this.options = options;
        this.$el = $(element);
        this.$pickers = $('[data-datepicker]', this.$el);

        $.oc.foundation.controlUtils.markDisposable(element);
        Base.call(this);
        this.init();
    }

    DateFilter.prototype = Object.create(BaseProto);
    DateFilter.prototype.constructor = DateFilter;

    DateFilter.DEFAULTS = {

    }

    DateFilter.prototype.init = function() {
        this.dbDateTimeFormat = 'YYYY-MM-DD HH:mm:ss';
        this.dbDateFormat = 'YYYY-MM-DD';

        this.initRegion();
        this.initDatePickers();
    }

    DateFilter.prototype.initDatePickers = function() {
        var self = this,
            scopeData = this.$el.data('scope-data');

        this.$pickers.each(function(index, datepicker) {
            var $datepicker = $(datepicker),
                defaultValue = self.getDatePickerValue($datepicker);

            var pikadayOptions = {
                minDate: new Date(scopeData.minDate),
                maxDate: new Date(scopeData.maxDate),
                firstDay: scopeData.firstDay,
                yearRange: scopeData.yearRange,
                showWeekNumber: scopeData.showWeekNumber,
                setDefaultDate: defaultValue !== '' ? defaultValue.toDate() : '',
                format: self.getDateFormat(),
                i18n: self.getLang('datepicker'),
                onSelect: function() {
                    self.onSelectDatePicker.call(self, this, $datepicker);
                }
            }

            if (defaultValue !== '') {
                $datepicker.val(defaultValue.format(self.getDateFormat()));
            }

            $datepicker.pikaday(pikadayOptions)
        });
    }

    DateFilter.prototype.onSelectDatePicker = function(datepicker, $input) {
        var pickerMoment = datepicker.getMoment(),
            pickerValue = pickerMoment.format(this.dbDateTimeFormat);

        // Convert from user preference to UTC
        var momentObj = moment
            .tz(pickerValue, this.dbDateTimeFormat, this.timezone)
            .tz(this.appTimezone);

        var lockerValue = momentObj.format(this.dbDateTimeFormat);

        this.setDataLocker($input, lockerValue);
    }

    DateFilter.prototype.getDatePickerValue = function($datepicker) {
        var rawValue = $datepicker.val();
        if (rawValue !== '') {
            rawValue = moment(rawValue, this.getDateFormat());
        }

        // Look at the locker for the default value
        if (!rawValue) {
            rawValue = this.getDataLocker($datepicker)
            if (rawValue !== '') {
                rawValue = moment(rawValue, this.dbDateFormat);
            }
        }

        return rawValue;
    }

    DateFilter.prototype.getDataLocker = function(picker) {
        var $picker = $(picker),
            $locker = $('#' + $picker.data('datepicker-target'));

        return $locker.val();
    }

    DateFilter.prototype.setDataLocker = function(picker, value) {
        var $picker = $(picker),
            $locker = $('#' + $picker.data('datepicker-target'));

        $locker.val(value);
    }

    DateFilter.prototype.initRegion = function() {
        this.locale = $('meta[name="backend-locale"]').attr('content');
        this.timezone = $('meta[name="backend-timezone"]').attr('content');
        this.appTimezone = $('meta[name="app-timezone"]').attr('content');

        if (!this.appTimezone) {
            this.appTimezone = 'UTC';
        }

        if (!this.timezone) {
            this.timezone = 'UTC';
        }

        // Set both timezones to UTC to disable converting between them
        var scopeData = this.$el.data('scope-data');
        if (!scopeData.useTimezone) {
            this.appTimezone = 'UTC';
            this.timezone = 'UTC';
        }
    }

    DateFilter.prototype.dispose = function() {
        this.$el.off('dispose-control', this.proxy(this.dispose));
        this.$el.removeData('oc.datefilter');

        this.$el = null;
        this.options = null;

        BaseProto.dispose.call(this);
    }

    DateFilter.prototype.getDateFormat = function () {
        if (this.locale) {
            return moment()
                .locale(this.locale)
                .localeData()
                .longDateFormat('l');
        }

        return this.dbDateFormat;
    }

    DateFilter.prototype.getLang = function(name, defaultValue) {
        if ($.oc === undefined || $.oc.lang === undefined) {
            return defaultValue;
        }

        return $.oc.lang.get(name, defaultValue);
    }

    // DATEFILTER PLUGIN DEFINITION
    // ============================

    var old = $.fn.dateFilter;

    $.fn.dateFilter = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), result
        this.each(function () {
            var $this = $(this);
            var data = $this.data('oc.datefilter');
            var options = $.extend({}, DateFilter.DEFAULTS, $this.data(), typeof option == 'object' && option);
            if (!data) $this.data('oc.datefilter', (data = new DateFilter(this, options)));
            if (typeof option == 'string') result = data[option].apply(data, args);
            if (typeof result != 'undefined') return false;
        });

        return result ? result : this;
    }

    $.fn.dateFilter.Constructor = DateFilter;

    // DATEFILTER NO CONFLICT
    // =================

    $.fn.dateFilter.noConflict = function () {
        $.fn.dateFilter = old;
        return this;
    }

    // DATEFILTER DATA-API
    // ===============

    $(document).render(function() {
        $('[data-control="datefilter"]').dateFilter();
    });

}(window.jQuery);
