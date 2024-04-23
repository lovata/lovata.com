<?php namespace Tailor\Console;

use Site;
use Illuminate\Console\Command;
use Tailor\Models\EntryRecord;
use Tailor\Classes\BlueprintIndexer;

/**
 * TailorPropagate propagates multisite records for all tailor records.
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class TailorPropagate extends Command
{
    /**
     * @var string signature of console command
     */
    protected $signature = 'tailor:propagate
        {--site= : Site ID for finding source records.}
        {--blueprint= : Handle name to refresh one blueprint.}';

    /**
     * @var string description of the console command
     */
    protected $description = 'Propagates multisite records for tailor records.';

    /**
     * @var BlueprintIndexer indexer for blueprints
     */
    protected $indexer;

    /**
     * @var \Tailor\Classes\Blueprint|null blueprintObj reference for a single blueprint
     */
    protected $blueprintObj;

    /**
     * @var \System\Models\SiteDefinition|null siteObj reference for a specific site source
     */
    protected $siteObj;

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->indexer = BlueprintIndexer::instance()->setNotesCommand($this);

        $this->line('Propagating Blueprint Records');

        if (($handle = $this->option('blueprint')) && !$this->lookupBlueprint($handle)) {
            return;
        }

        if (($site = $this->option('site')) && !$this->lookupSite($site)) {
            return;
        }

        if ($this->blueprintObj) {
            $this->propagateBlueprint($this->blueprintObj);
        }
        else {
            foreach ($this->indexer->listSections() as $blueprint) {
                if ($blueprint->useMultisiteSync()) {
                    $this->propagateBlueprint($blueprint);
                }
            }
        }
    }

    /**
     * propagateBlueprint
    */
    public function propagateBlueprint($blueprint)
    {
        $otherSites = [];

        if ($this->siteObj) {
            [$records, $otherSites] = Site::withContext($this->siteObj->id, function() use ($blueprint) {
                $instance = EntryRecord::inSectionUuid($blueprint->uuid);
                return [$instance->get(), $instance->getMultisiteSyncSites()];
            });
        }
        else {
            $instance = EntryRecord::inSectionUuid($blueprint->uuid);
            $otherSites = $instance->getMultisiteSyncSites();
            $records = $instance->get();
        }

        $this->line('- <info>'.$blueprint->name.'</info>: '.$records->count() .' record(s)');

        foreach ($otherSites as $siteId) {
            Site::withContext($siteId, function() use ($records, $siteId) {
                $records->each(function($record) use ($siteId) {
                    $record->findOrCreateForSite($siteId);
                });
            });
        }
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

        if (!$blueprint->useMultisiteSync()) {
            $this->error("Blueprint is not using multisite sync mode.");
            return false;
        }

        $this->blueprintObj = $blueprint;
        return true;
    }

    /**
     * lookupSite
     */
    public function lookupSite($id): bool
    {
        $site = Site::getSiteFromId($id);
        if (!$site) {
            $this->error("Site with ID '{$id}' not found");
            return false;
        }

        $this->siteObj = $site;
        return true;
    }
}
