<?php namespace Tailor\Console;

use Db;
use Schema;
use Illuminate\Console\Command;
use Tailor\Classes\BlueprintIndexer;

/**
 * TailorRefresh refreshes tailor content.
 *
 * This destroys all database tables for tailor, then builds them up again.
 * It is a great way for developers to debug and develop with tailor.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class TailorRefresh extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string signature of console command
     */
    protected $signature = 'tailor:refresh {--f|force : Force the operation to run.}
        {--r|rollback : Rollback to the beginning.}
        {--blueprint= : Handle name to refresh a single blueprint.}';

    /**
     * @var string description of the console command
     */
    protected $description = 'Rollback and migrate database tables for tailor.';

    /**
     * @var BlueprintIndexer indexer for blueprints
     */
    protected $indexer;

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->indexer = BlueprintIndexer::instance()->setNotesCommand($this);

        $message = "This will DESTROY all content records for Tailor. Please make sure you have a backup first.";
        if ($handle = $this->option('blueprint')) {
            $message = "This will DESTROY content records for the [{$handle}] handle.";
        }

        if (!$this->confirmToProceed($message)) {
            return;
        }

        if ($handle = $this->option('blueprint')) {
            return $this->handleForBlueprint($handle);
        }

        $this->handleRollback();
        if ($this->option('rollback')) {
            return;
        }

        $this->indexer->migrate();
    }

    /**
     * handleForBlueprint
     */
    public function handleForBlueprint($handle)
    {
        $blueprint = $this->indexer->findSectionByHandle($handle);
        if (!$blueprint) {
            $this->error("Blueprint for handle '{$handle}' not found");
        }

        $this->handleRollbackBlueprint($blueprint);

        if ($this->option('rollback')) {
            return;
        }

        $this->indexer->migrateBlueprint($blueprint);
    }

    /**
     * handleRollbackBlueprint performs a database rollback for a single blueprint
     */
    protected function handleRollbackBlueprint($blueprint)
    {
        $contentTable = $blueprint->getContentTableName();
        $this->line('<info>Dropped table</info> ' . $contentTable);
        Schema::dropIfExists($contentTable);

        $joinTable = $blueprint->getJoinTableName();
        $this->line('<info>Dropped table</info> ' . $joinTable);
        Schema::dropIfExists($joinTable);

        $repeaterTable = $blueprint->getRepeaterTableName();
        $this->line('<info>Dropped table</info> ' . $repeaterTable);
        Schema::dropIfExists($repeaterTable);
    }

    /**
     * handleRollback performs a database rollback
     */
    protected function handleRollback()
    {
        Db::table('tailor_content_schema')->orderBy('id')->chunkById(100, function($tables) {
            foreach ($tables as $table) {
                $tablePrefix = substr($table->table_name, 0, -1);

                $contentTable = $tablePrefix.'c';
                $this->line('<info>Dropped table</info> ' . $contentTable);
                Schema::dropIfExists($contentTable);

                $joinTable = $tablePrefix.'j';
                $this->line('<info>Dropped table</info> ' . $joinTable);
                Schema::dropIfExists($joinTable);

                $repeaterTable = $tablePrefix.'r';
                $this->line('<info>Dropped table</info> ' . $repeaterTable);
                Schema::dropIfExists($repeaterTable);
            }
        });

        $truncateTables = [
            'tailor_globals',
            'tailor_global_joins',
            'tailor_global_repeaters',
            'tailor_content_schema',
            'tailor_preview_tokens',
        ];

        foreach ($truncateTables as $table) {
            $this->line('<info>Emptied table</info> ' . $table);
            Db::table($table)->truncate();
        }
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
