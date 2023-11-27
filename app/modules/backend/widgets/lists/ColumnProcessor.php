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
     * processAutoOrder applies a default sort order to all columns
     */
    protected function processAutoOrder(array &$columns)
    {
        // Apply incremental default orders
        $orderCount = 0;
        foreach ($columns as $column) {
            if ($column->order !== -1) {
                continue;
            }
            $column->order = ($orderCount += 100);
        }

        // Sort columns
        uasort($columns, static function ($a, $b) {
            return $a->order - $b->order;
        });
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
