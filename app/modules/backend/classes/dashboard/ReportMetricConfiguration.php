<?php namespace Backend\Classes\Dashboard;

/**
 * Carries information about a report metric configuration.
 */
class ReportMetricConfiguration
{
    /**
     * @var bool Indicates whether the metric totals should be displayed in the report.
     */
    private $displayTotals;

    /**
     * @var bool Indicates whether a relative or proportional bar should be displayed.
     */
    private $displayRelativeBar;

    /**
     * Constructor to initialize the metric configuration.
     *
     * @param bool $displayTotals Specifies whether to display totals.
     * @param bool $displayRelativeBar Specifies whether to display a relative/proportional bar.
     */
    public function __construct(bool $displayTotals, bool $displayRelativeBar)
    {
        $this->displayTotals = $displayTotals;
        $this->displayRelativeBar = $displayRelativeBar;
    }

    /**
     * Retrieves the display setting for totals.
     * @return bool True if totals should be displayed, false otherwise.
     */
    public function getDisplayTotals(): bool
    {
        return $this->displayTotals;
    }

    /**
     * Retrieves the display setting for the relative or proportional bars.
     * @return bool True if the relative/proportional bars should be displayed, false otherwise.
     */
    public function getDisplayRelativeBar(): bool
    {
        return $this->displayRelativeBar;
    }
}
