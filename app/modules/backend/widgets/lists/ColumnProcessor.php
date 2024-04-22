<?php namespace Backend\Widgets\Lists;

use BackendAuth;

/**
 * ColumnProcessor concern
 */
trait ColumnProcessor
{
    /**
     * processColumnTypeModifiers
     */
    protected function processColumnTypeModifiers(array &$columns)
    {
        foreach ($columns as $column) {
            if ($column->type === 'linkage') {
                $column->clickable(false);
            }
        }
    }

    /**
     * processPermissionCheck check if user has permissions to show the column
     * and removes it if permission is denied
     */
    protected function processPermissionCheck(array $columns): void
    {
        foreach ($columns as $columnName => $column) {
            if (
                $column->permissions &&
                !BackendAuth::userHasAccess($column->permissions, false)
            ) {
                $this->removeColumn($columnName);
            }
        }
    }

    /**
     * processAutoOrder applies a default sort order to all columns
     */
    protected function processAutoOrder(array &$columns)
    {
        // Build before and after map
        $beforeMap = $afterMap = [];
        foreach ($columns as $column) {
            if ($column->after && isset($columns[$column->after])) {
                $afterMap[$column->columnName] = $columns[$column->after]->columnName;
            }
            elseif ($column->before && isset($columns[$column->before])) {
                $beforeMap[$column->columnName] = $columns[$column->before]->columnName;
            }
        }

        // Apply incremental default orders
        $orderCount = 0;
        foreach ($columns as $column) {
            if (
                $column->order !== -1 ||
                isset($afterMap[$column->columnName]) ||
                isset($beforeMap[$column->columnName])
            ) {
                continue;
            }
            $column->order = ($orderCount += 100);
        }

        // Apply before and after
        foreach ($beforeMap as $from => $to) {
            $columns[$from]->order = $columns[$to]->order - 1;
        }
        foreach ($afterMap as $from => $to) {
            $columns[$from]->order = $columns[$to]->order + 1;
        }

        // Sort columns
        uasort($columns, static function ($a, $b) {
            return $a->order - $b->order;
        });
    }

    /**
     * processHiddenColumns purges hidden columns
     */
    protected function processHiddenColumns(array $columns)
    {
        foreach ($columns as $key => $column) {
            if ($column->hidden) {
                $this->removeColumn($key);
            }
        }
    }

    /**
     * processUserColumnOrders applies a supplied column order from a user preference
     */
    protected function processUserColumnOrders(array &$columns, $userPreference)
    {
        if ($userPreference) {
            $orderedDefinitions = [];
            foreach ($userPreference as $column) {
                if (isset($columns[$column])) {
                    $orderedDefinitions[$column] = $columns[$column];
                }
            }

            $columns = array_merge($orderedDefinitions, $columns);
        }
    }
}
