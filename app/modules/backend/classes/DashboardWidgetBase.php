<?php namespace Backend\Classes;

use Carbon\Carbon;
use SystemException;

/**
 * DashboardWidgetBase is a base class for Vue-based dashboard widgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class DashboardWidgetBase extends VueComponentBase
{
    /**
     * getData
     */
    abstract public function getData(
        array $widgetConfig,
        ?Carbon $dateStart,
        ?Carbon $dateEnd,
        ?int $startTimestamp,
        ?Carbon $compareDateStart,
        ?Carbon $compareDateEnd,
        ?string $aggregationInterval,
        array $extraData
    ): mixed;

    /**
     * runHandler
     */
    public function runHandler(array $widgetConfig, string $handlerName, array $extraData): mixed
    {
        if (!preg_match('/^on[a-z0-9_]+/i', $handlerName)) {
            throw new SystemException('Invalid handler name');
        }

        if (!method_exists($this, $handlerName)) {
            throw new SystemException('Handler does not exist');
        }

        return $this->{$handlerName}($widgetConfig, $extraData);
    }
}
