<?php namespace Backend\Widgets\Lists;

/**
 * HasListSetup concern
 */
trait HasListSetup
{
    /**
     * onLoadSetup handler to display the list set up.
     */
    public function onLoadSetup()
    {
        $this->vars['columns'] = $this->getSetupListColumns();
        $this->vars['perPageOptions'] = $this->getSetupPerPageOptions();
        $this->vars['recordsPerPage'] = $this->recordsPerPage;
        return $this->makePartial('setup_form');
    }

    /**
     * onApplySetup handler
     */
    public function onApplySetup()
    {
        if (($visibleColumns = post('visible_columns')) && is_array($visibleColumns)) {
            $this->columnOverride = $visibleColumns;
            $this->putUserPreference('visible', $this->columnOverride);
        }

        $this->recordsPerPage = post('records_per_page', $this->recordsPerPage);

        $this->putUserPreference('order', post('column_order'));

        $this->putUserPreference('per_page', $this->recordsPerPage);

        return $this->onRefresh();
    }

    /**
     * onResetSetup handler
     */
    public function onResetSetup()
    {
        $this->resetUserPreference('visible');

        $this->resetUserPreference('per_page');

        $this->resetUserPreference('order');

        $this->resetSession();

        $this->init();

        return $this->onRefresh();
    }

    /**
     * Returns an array of allowable records per page.
     */
    protected function getSetupPerPageOptions()
    {
        $perPageOptions = is_array($this->perPageOptions)
            ? $this->perPageOptions
            : [20, 40, 80, 100, 120];

        if (!in_array($this->recordsPerPage, $perPageOptions)) {
            $perPageOptions[] = $this->recordsPerPage;
        }

        sort($perPageOptions);

        return $perPageOptions;
    }

    /**
     * getSetupListColumns returns all the list columns used for list set up.
     */
    protected function getSetupListColumns()
    {
        // Force all columns invisible
        $columns = $this->defineListColumns();
        foreach ($columns as $column) {
            $column->invisible = true;
        }

        return array_merge($columns, $this->getVisibleColumns());
    }
}
