/*
 * DatePicker plugin
 *
 * - Documentation: ../docs/datepicker.md
 *
 * Dependencies:
 * - Pikaday plugin (pikaday.js)
 * - Pikaday jQuery addon (pikaday.jquery.js)
 * - Clockpicker plugin (jquery-clockpicker.js)
 * - Moment library (moment.js)
 * - Moment Timezone library (moment.timezone.js)
 */

+function ($) { "use strict";

    var Base = $.oc.foundation.base,
        BaseProto = Base.prototype

    var DatePicker = function (element, options) {
        this.$el = $(element)
        this.options = options || {}

        $.oc.foundation.controlUtils.markDisposable(element);
        Base.call(this);
        this.init();
    }

    DatePicker.prototype = Object.create(BaseProto);
    DatePicker.prototype.constructor = DatePicker;

    DatePicker.prototype.init = function() {
        if (oc.changeMonitor) {
            oc.changeMonitor.disable();
        }

        this.dbDateTimeFormat = 'YYYY-MM-DD HH:mm:ss';
        this.dbDateFormat = 'YYYY-MM-DD';
        this.dbTimeFormat = 'HH:mm:ss';

        this.$dataLocker = $('[data-datetime-value]', this.$el);
        this.$datePicker = $('[data-datepicker]', this.$el);
        this.$timePicker = $('[data-timepicker]', this.$el);
        this.hasDate = !!this.$datePicker.length;
        this.hasTime = !!this.$timePicker.length;
        this.ignoreTimezone = !!this.$el.get(0).hasAttribute('data-ignore-timezone');
        this.defaultTimeMidnight = !!this.$el.get(0).hasAttribute('data-default-time-midnight');
        this.disabledDays = this.options.disableDays || [];

        this.initRegion();

        if (this.hasDate) {
            this.initDatePicker();
        }

        if (this.hasTime) {
            this.initTimePicker();
        }

        if (oc.changeMonitor) {
            oc.changeMonitor.enable();
        }

        this.$timePicker.on('change.oc.datepicker', this.proxy(this.onChangePicker));
        this.$datePicker.on('change.oc.datepicker', this.proxy(this.onChangePicker));
        oc.Events.on(this.$el.get(0), 'trigger:disable', this.proxy(this.checkPickerStates));

        this.checkPickerStates();

        this.$el.one('dispose-control', this.proxy(this.dispose));
    }

    DatePicker.prototype.dispose = function() {
        this.$timePicker.off('change.oc.datepicker', this.proxy(this.onChangePicker));
        this.$datePicker.off('change.oc.datepicker', this.proxy(this.onChangePicker));
        oc.Events.off(this.$el.get(0), 'trigger:disable', this.proxy(this.checkPickerStates));

        this.$el.off('dispose-control', this.proxy(this.dispose));
        this.$el.removeData('oc.datePicker');

        this.$el = null;
        this.options = null;

        BaseProto.dispose.call(this);
    }

    DatePicker.prototype.onChangePicker = function(event) {
        if (!$.trim($(event.target).val())) {
            this.emptyValues();
        }
        else {
            this.onSetLockerValue();
        }
    }

    DatePicker.prototype.checkPickerStates = function() {
        this.dateIsEnabled = !this.$datePicker.attr('readonly') && !this.$datePicker.attr('disabled');
        this.timeIsEnabled = !this.$timePicker.attr('readonly') && !this.$timePicker.attr('disabled');
        var dateBound = !!this.$datePicker.data('pickerBound'),
            timeBound = !!this.$timePicker.data('pickerBound');

        if (!this.dateIsEnabled && dateBound) {
            this.$datePicker.pikaday('destroy');
            this.$datePicker.data('pickerBound', false);
        }
        else if (this.dateIsEnabled && !dateBound) {
            this.initDatePicker();
        }

        if (!this.timeIsEnabled && timeBound) {
            this.$timePicker.clockpicker('remove');
            this.$timePicker.data('pickerBound', false);
        }
        else if (this.timeIsEnabled && !timeBound) {
            this.initTimePicker();
        }
    }

    DatePicker.prototype.onSetLockerValue = function() {
        var dateValue = this.getDatePickerValue();

        var timeValue = this.getTimePickerValue();

        var momentObj = moment
            .tz(dateValue + ' ' + timeValue, this.dbDateTimeFormat, this.timezone)
            .tz(this.appTimezone);

        var lockerValue = momentObj.format(this.dbDateTimeFormat);

        this.$dataLocker.val(lockerValue);
    }

    //
    // Datepicker
    //

    DatePicker.prototype.initDatePicker = function() {
        var dateFormat = this.getDateFormat(),
            now = moment().tz(this.timezone).format(dateFormat);

        var pikadayOptions = {
            yearRange: this.options.yearRange,
            firstDay: this.options.firstDay,
            showWeekNumber: this.options.showWeekNumber,
            format: dateFormat,
            setDefaultDate: now,
            keyboardInput: false,
            disableDayFn: (date) => {
                return this.isDateDisabled(date);
            },
            onOpen: function() {
                var $field = $(this._o.trigger)

                $(this.el).css({
                    left: 'auto',
                    right: $(window).width() - $field.offset().left - $field.outerWidth()
                })
            }
        }

        var lang = this.getLang('datepicker', false);
        if (lang) {
            pikadayOptions.i18n = lang;
        }

        this.$datePicker.val(this.getDataLockerValue(dateFormat));

        if (this.options.minDate) {
            pikadayOptions.minDate = new Date(this.options.minDate);
        }

        if (this.options.maxDate) {
            pikadayOptions.maxDate = new Date(this.options.maxDate);
        }

        this.$datePicker.pikaday(pikadayOptions);
        this.$datePicker.data('pickerBound', true);
    }

    DatePicker.prototype.isDateDisabled = function(date) {
        if (!Array.isArray(this.disabledDays)) {
            return false;
        }

        const parsedDate = moment(date, this.getDateFormat());
        const compareDate = parsedDate.format('YYYY-MM-DD');

        for (const disabledDay of this.disabledDays) {
            if (parsedDate.day() === disabledDay) {
                return true;
            }

            if (disabledDay === compareDate) {
                return true;
            }
        }

        return false;
    }

    // Returns in user preference timezone
    DatePicker.prototype.getDatePickerValue = function() {
        var value = this.$datePicker.val();

        if (!this.hasDate || !value) {
            return moment.tz(this.appTimezone)
                .tz(this.timezone)
                .format(this.dbDateFormat);
        }

        return moment(value, this.getDateFormat()).format(this.dbDateFormat);
    }

    DatePicker.prototype.getDateFormat = function() {
        var format = 'YYYY-MM-DD';

        if (this.options.format) {
            format = this.options.format;
        }
        else if (this.locale) {
            format = moment()
                .locale(this.locale)
                .localeData()
                .longDateFormat('l');
        }

        return format;
    }

    //
    // Timepicker
    //

    DatePicker.prototype.initTimePicker = function() {
        this.$timePicker.clockpicker({
            autoclose: 'true',
            placement: 'auto',
            align: 'right',
            twelvehour: this.isTimeTwelveHour(),
            afterDone: this.proxy(this.onChangeTimePicker)
        });

        this.$timePicker.val(this.getDataLockerValue(this.getTimeFormat()));
        this.$timePicker.data('pickerBound', true);
    }

    // Trigger a change event when the time is changed,
    // to allow dependent fields to refresh
    DatePicker.prototype.onChangeTimePicker = function() {
        this.$timePicker.trigger('change');
    }

    // Returns in user preference timezone
    DatePicker.prototype.getTimePickerValue = function() {
        var value = this.$timePicker.val();

        if (!this.hasTime || !value) {
            if (this.defaultTimeMidnight) {
                return '00:00:00';
            }

            return moment.tz(this.appTimezone)
                .tz(this.timezone)
                .format(this.dbTimeFormat);
        }

        return moment(value, this.getTimeFormat()).format(this.dbTimeFormat);
    }

    DatePicker.prototype.getTimeFormat = function() {
        return this.isTimeTwelveHour()
            ? 'hh:mm A'
            : 'HH:mm';
    }

    DatePicker.prototype.isTimeTwelveHour = function() {
        return this.options.twelveHour;
    }

    //
    // Utilities
    //

    DatePicker.prototype.emptyValues = function() {
        this.$dataLocker.val('');
        this.$datePicker.val('');
        this.$timePicker.val('');
    }

    DatePicker.prototype.getDataLockerValue = function(format) {
        var value = this.$dataLocker.val();

        return value
            ? this.getMomentLoadValue(value, format)
            : null;
    }

    DatePicker.prototype.getMomentLoadValue = function(value, format) {
        var momentObj = moment.tz(value, this.appTimezone);

        if (this.locale) {
            momentObj = momentObj.locale(this.locale);
        }

        momentObj = momentObj.tz(this.timezone);

        return momentObj.format(format);
    }

    DatePicker.prototype.initRegion = function() {
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
        if (this.ignoreTimezone) {
            this.appTimezone = 'UTC';
            this.timezone = 'UTC';
        }
    }

    DatePicker.prototype.getLang = function(name, defaultValue) {
        if ($.oc === undefined || $.oc.lang === undefined) {
            return defaultValue;
        }

        return $.oc.lang.get(name, defaultValue);
    }

    DatePicker.DEFAULTS = {
        minDate: null,
        maxDate: null,
        format: null,
        yearRange: 10,
        disableDays: null,
        firstDay: 0,
        twelveHour: false,
        showWeekNumber: false
    }

    // PLUGIN DEFINITION
    // ============================

    var old = $.fn.datePicker;

    $.fn.datePicker = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), items, result;

        items = this.each(function () {
            var $this   = $(this);
            var data    = $this.data('oc.datePicker');
            var options = $.extend({}, DatePicker.DEFAULTS, $this.data(), typeof option == 'object' && option);
            if (!data) $this.data('oc.datePicker', (data = new DatePicker(this, options)));
            if (typeof option == 'string') result = data[option].apply(data, args);
            if (typeof result != 'undefined') return false;
        })

        return result ? result : items;
    }

    $.fn.datePicker.Constructor = DatePicker;

    $.fn.datePicker.noConflict = function () {
        $.fn.datePicker = old;
        return this;
    }

    $(document).on('render', function() {
        $('[data-control="datepicker"]').datePicker();
    });

}(window.jQuery);
