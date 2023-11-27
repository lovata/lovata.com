<?php namespace Backend\Widgets\Lists;

/**
 * HasSorting concern
 *
 * @mixin \Backend\Traits\SessionMaker
 */
trait HasSorting
{
    /**
     * @var array All sortable columns.
     */
    protected $sortableColumns;

    /**
     * @var string Sets the list sorting column.
     */
    protected $sortColumn;

    /**
     * @var string sortDirection sets the list sorting direction (asc, desc)
     */
    protected $sortDirection;

    /**
     * @var string sortStep allows for indeterminate sorting
     */
    protected $sortStep;

    /**
     * onSort AJAX handler for sorting the list.
     */
    public function onSort()
    {
        $column = post('sortColumn');
        if (!$column) {
            return;
        }

        $sortOptions = [
            'column' => $this->getSortColumn(),
            'direction' => $this->getSortDirection(),
            'step' => $this->getSortStep()
        ];

        $isSameColumn = $column === $sortOptions['column'];
        $otherDirection = $sortOptions['direction'] !== 'asc' ? 'asc' : 'desc';

        // Sorting the same column
        if ($isSameColumn) {
            $sortOptions['column'] = $column;
            $sortOptions['direction'] = $otherDirection;
            $sortOptions['step']++;
        }
        // Sorting a new column
        else {
            $sortOptions['column'] = $column;
            $sortOptions['direction'] = 'desc';
            $sortOptions['step'] = 1;
        }

        // Finish up
        $this->sortDirection = $sortOptions['direction'];
        $this->sortColumn = $sortOptions['column'];
        $this->sortStep = $sortOptions['step'];

        // Persist the page number
        $this->currentPageNumber = post('page');

        // Attempt a refresh with the new sortOptions and update the
        // the user session only if the query succeeded.
        $result = $this->onRefresh();

        $this->putSession('sort', $sortOptions);

        return $result;
    }

    /**
     * getSortColumn returns the current sorting column, saved in a session or cached.
     */
    public function getSortColumn()
    {
        if (!$this->isSortable() || !$this->useSorting()) {
            return false;
        }

        if ($this->sortColumn !== null) {
            return $this->sortColumn;
        }

        // Defaults
        $this->sortStep = 0;
        $this->sortDirection = 'asc';

        // User preference
        if ($this->showSorting && ($sortOptions = $this->getSession('sort'))) {
            $this->sortColumn = $sortOptions['column'] ?? '';
            $this->sortDirection = $sortOptions['direction'] ?? $this->sortDirection;
            $this->sortStep = $sortOptions['step'] ?? $this->sortStep;
        }
        // Supplied default
        else {
            if (is_string($this->defaultSort)) {
                $this->sortColumn = $this->defaultSort;
            }
            elseif (is_array($this->defaultSort) && isset($this->defaultSort['column'])) {
                $this->sortColumn = $this->defaultSort['column'];
                $this->sortDirection = $this->defaultSort['direction'] ?? $this->sortDirection;
            }
            else {
                foreach ($this->getColumns() as $column) {
                    if (!$column->sortableDefault) {
                        continue;
                    }
                    $this->sortColumn = $column->columnName;
                    if (is_string($column->sortableDefault)) {
                        $this->sortDirection = $column->sortableDefault;
                    }
                    break;
                }
            }
        }

        // If an invalid column is found, reset to the first available column
        if ($this->sortColumn && !$this->isSortable($this->sortColumn)) {
            $columns = $this->visibleColumns ?: $this->getVisibleColumns();
            $columns = array_filter($columns, function ($column) {
                return $column->sortable;
            });
            $this->sortColumn = key($columns);
        }

        return $this->sortColumn;
    }

    /**
     * isUserSorting returns true if the user has requested a sort column
     */
    public function isUserSorting(): bool
    {
        return (bool) $this->getSession('sort', false);
    }

    /**
     * getSortStep returns the current indeterminate step
     */
    public function getSortStep(): int
    {
        return (int) ($this->sortStep ?? 0);
    }

    /**
     * getSortDirection returns the current sort direction or default of 'asc'
     */
    public function getSortDirection()
    {
        return $this->sortDirection ?? 'asc';
    }

    /**
     * useSorting
     */
    protected function useSorting(): bool
    {
        return true;
    }

    /**
     * isSortable returns true if the column can be sorted.
     */
    protected function isSortable($column = null)
    {
        if ($column === null) {
            return (count($this->getSortableColumns()) > 0);
        }

        return array_key_exists($column, $this->getSortableColumns());
    }

    /**
     * getSortableColumns returns a collection of columns which are sortable.
     */
    protected function getSortableColumns()
    {
        if ($this->sortableColumns !== null) {
            return $this->sortableColumns;
        }

        $columns = $this->getColumns();
        $sortable = array_filter($columns, function ($column) {
            return $column->sortable;
        });

        return $this->sortableColumns = $sortable;
    }
}
