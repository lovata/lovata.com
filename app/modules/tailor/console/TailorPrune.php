<?php namespace Tailor\Console;

use Illuminate\Console\Command;
use Tailor\Classes\SchemaPruner;
use Tailor\Classes\BlueprintIndexer;

/**
 * TailorPrune removes unused content.
 *
 * As a general rule Tailor will never drop table columns and delete content. This command
 * drops columns that have been removed or renamed and tables that are not used anymore.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class TailorPrune extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string signature of console command
     */
    protected $signature = 'tailor:prune {--f|force : Force the operation to run.}
        {--tables : Only prune unused tables.}
        {--fields : Only prune unused fields.}
        {--blueprint= : Handle name to prune a single blueprint.}';

    /**
     * @var string description of the console command
     */
    protected $description = 'Drops unused tables and columns from the database.';

    /**
     * @var BlueprintIndexer indexer for blueprints
     */
    protected $indexer;

    /**
     * @var \Tailor\Classes\Blueprint|null blueprintObj reference for a single blueprint
     */
    protected $blueprintObj;

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->indexer = BlueprintIndexer::instance()->setNotesCommand($this);

        $message = "This will DESTROY unused content tables and columns for Tailor. Please make sure you have a backup first.";

        if (!$this->confirmToProceed($message)) {
            return;
        }

        if (($handle = $this->option('blueprint')) && !$this->lookupBlueprint($handle)) {
            return;
        }

        if ($this->shouldPruneFields()) {
            $this->line('Pruning Content Fields');
            $this->handlePruneFields();
        }

        if ($this->shouldPruneTables()) {
            $this->line('Pruning Content Tables');
            $this->handlePruneTables();
        }
    }

    /**
     * handlePruneFields
     */
    public function handlePruneFields()
    {
        $pruner = $this->blueprintObj
            ? SchemaPruner::pruneBlueprint($this->blueprintObj)
            : SchemaPruner::pruneAll();

        $result = $pruner->getPrunedFields();
        if (!$result) {
            $this->line('<info>Nothing to prune.</info>');
        }

        foreach ($result as $table => $fields) {
            $fieldStr = join(', ', $fields);
            $this->line("<info>- Fields:</info> {$table} [$fieldStr]");
        }
    }

    /**
     * handlePruneTables
     */
    public function handlePruneTables()
    {
        if ($this->blueprintObj) {
            $this->error("Cannot specify a blueprint when pruning only tables");
            return;
        }

        $result = SchemaPruner::pruneTables()->getPrunedTables();
        if (!$result) {
            $this->line('<info>Nothing to prune.</info>');
        }

        foreach ($result as $tableName) {
            $this->line('<info>- Table:</info> ' . $tableName);
        }
    }

    /**
     * shouldPruneFields
     */
    public function shouldPruneFields(): bool
    {
        if ($this->option('fields')) {
            return true;
        }

        if ($this->option('tables')) {
            return false;
        }

        return true;
    }

    /**
     * shouldPruneTables
     */
    public function shouldPruneTables(): bool
    {
        if ($this->option('tables')) {
            return true;
        }

        if ($this->option('fields')) {
            return false;
        }

        if ($this->blueprintObj) {
            return false;
        }

        return true;
    }

    /**
     * lookupBlueprint
     */
    public function lookupBlueprint($handle): bool
    {
        $blueprint = $this->indexer->findSectionByHandle($handle);
        if (!$blueprint) {
            $this->error("Blueprint for handle '{$handle}' not found");
            return false;
        }

        $this->blueprintObj = $blueprint;
        return true;
    }

    /**
     * getDefaultConfirmCallback specifies the default confirmation callback
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return true;
        };
    }
}
