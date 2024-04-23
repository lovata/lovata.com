oc.Modules.register('backend.vuecomponents.dashboard.calendar', function () {
    class DashboardCalendar { 
        constructor(element, locale, trans) {
            this.element = element;
            this.locale = locale;

            const start = moment().subtract(29, 'days');
            const end = moment();

            $(this.element).daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: moment(),
                opens: 'left',
                alwaysShowCalendars: true,
                ranges: {
                    [trans('range-today')]: [moment(), moment()],
                    [trans('range-this-week')]: [moment().isoWeekday(1), moment()],
                    // [trans('range-yesterday')]: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    // [trans('range-last-7-days')]: [moment().subtract(6, 'days'), moment()],
                    // [trans('range-last-30-days')]: [moment().subtract(29, 'days'), moment()],
                    [trans('range-this-month')]: [moment().startOf('month'), moment().endOf('month')],
                    // [trans('range-last-month')]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    [trans('range-this-quarter')]: [moment().startOf('quarter'), moment()],
                    [trans('range-this-year')]: [moment().startOf('year'), moment()],
                }
            })
        }
    }

    return DashboardCalendar;
});