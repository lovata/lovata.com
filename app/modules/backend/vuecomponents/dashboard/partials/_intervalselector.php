<div
    class="dashboard-button-set manage-dashboard-controls"
    data-lang-range-today="<?= e(trans('backend::lang.dashboard.range_today')) ?>"
    data-lang-range-yesterday="<?= e(trans('backend::lang.dashboard.range_yesterday')) ?>"
    data-lang-range-last-7-days="<?= e(trans('backend::lang.dashboard.range_last_7_days')) ?>"
    data-lang-range-last-30-days="<?= e(trans('backend::lang.dashboard.range_last_30_days')) ?>"
    data-lang-range-this-month="<?= e(trans('backend::lang.dashboard.range_this_month')) ?>"
    data-lang-range-last-month="<?= e(trans('backend::lang.dashboard.range_last_month')) ?>"
    data-lang-range-this-quarter="<?= e(trans('backend::lang.dashboard.range_this_quarter')) ?>"
    data-lang-range-this-year="<?= e(trans('backend::lang.dashboard.range_this_year')) ?>"
    data-lang-range-this-week="<?= e(trans('backend::lang.dashboard.range_this_week')) ?>"
    data-lang-interval-day="<?= e(trans('backend::lang.dashboard.interval_day')) ?>"
    data-lang-interval-week="<?= e(trans('backend::lang.dashboard.interval_week')) ?>"
    data-lang-interval-month="<?= e(trans('backend::lang.dashboard.interval_month')) ?>"
    data-lang-interval-quarter="<?= e(trans('backend::lang.dashboard.interval_quarter')) ?>"
    data-lang-interval-year="<?= e(trans('backend::lang.dashboard.interval_year')) ?>"
    data-lang-compare-prev-period="<?= e(trans('backend::lang.dashboard.compare_prev_period')) ?>"
    data-lang-compare-prev-year="<?= e(trans('backend::lang.dashboard.compare_prev_year')) ?>"
    data-lang-compare-none="<?= e(trans('backend::lang.dashboard.compare_none')) ?>"
>
    <button @click.stop.prevent="onSelectIntervalClick" class="dashboard-toolbar-button stack">
        <div><?= e(trans('backend::lang.dashboard.group')) ?></div>
        <span v-text="groupingIntervalName"></span>
    </button>
    <button @click.stop.prevent="onSelectCompareClick" class="dashboard-toolbar-button stack">
        <div><?= e(trans('backend::lang.dashboard.compare_totals')) ?></div>
        <span v-text="compareOptionName"></span>
    </button>
    <button @click.stop.prevent class="dashboard-toolbar-button stack" ref="calendarControl">
        <div><?= e(trans('backend::lang.dashboard.interval')) ?></div>
        <span v-text="intervalName"></span>
    </button>

    <backend-component-dropdownmenu
        :items="intervalMenuItems"
        ref="intervalMenu"
        @command="onIntervalMenuItemCommand"
    ></backend-component-dropdownmenu>

    <backend-component-dropdownmenu
        :items="compareMenuItems"
        ref="compareMenu"
        @command="onCompareMenuItemCommand"
    ></backend-component-dropdownmenu>
</div>