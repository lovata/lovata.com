<?php namespace Backend\Classes\Dashboard;

/**
 * ReportData defines common report metrics and helpers
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ReportData
{
    const INDICATOR_ICON_STATUS_INFO = 'info';
    const INDICATOR_ICON_STATUS_SUCCESS = 'success';
    const INDICATOR_ICON_STATUS_IMPORTANT = 'important';
    const INDICATOR_ICON_STATUS_DISABLED = 'disabled';
    const INDICATOR_ICON_STATUS_WARNING = 'warning';

    const METRIC_INDICATOR_ICON_STATUS = 'icon_status';
    const METRIC_INDICATOR_ICON_COMPLICATION = 'icon_complication';

    const METRIC_VALUE = 'value';
    const METRIC_LINK_ENABLED = 'link_enabled';
    const METRIC_LINK_HREF = 'link_href';

    /**
     * @var string INDICATOR_HREF_POPUP specifies a special type of the Indicator widget link which opens a popup.
     * The data for the popup is requested from the data source's onGetPopupData
     * method, which must return an array with the `title` and `content` elements.
     */
    const INDICATOR_HREF_POPUP = 'popup';

    const INDICATOR_ICON_COMPLICATION_UP = 'up';

    /**
     * addIndicatorMetrics adds common indicator widget metrics to a dimension.
     */
    public static function addIndicatorMetrics(ReportDimension $dimension)
    {
        $langPrefix = 'backend::lang.dashboard.';

        $dimension
            ->addCalculatedMetric(ReportData::METRIC_VALUE, $langPrefix . 'widget_metric_value')
            ->addCalculatedMetric(ReportData::METRIC_INDICATOR_ICON_STATUS, $langPrefix . 'widget_metric_icon_status')
            ->addCalculatedMetric(ReportData::METRIC_INDICATOR_ICON_COMPLICATION, $langPrefix . 'widget_metric_icon_complication')
            ->addCalculatedMetric(ReportData::METRIC_LINK_ENABLED, $langPrefix . 'widget_metric_link_enabled')
            ->addCalculatedMetric(ReportData::METRIC_LINK_HREF, $langPrefix . 'widget_metric_href');
    }
}
