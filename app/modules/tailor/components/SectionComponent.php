<?php namespace Tailor\Components;

use Event;
use Tailor\Classes\ComponentVariable;
use Tailor\Classes\BlueprintIndexer;
use Tailor\Models\EntryRecord;
use Tailor\Models\PreviewToken;
use Cms\Classes\ComponentModuleBase;

/**
 * SectionComponent displays a list of records.
 */
class SectionComponent extends ComponentModuleBase
{
    /**
     * @var array otherSiteCache
     */
    protected $otherSiteCache;

    /**
     * @var bool multisiteCache
     */
    protected $multisiteCache;

    /**
     * componentDetails
     */
    public function componentDetails()
    {
        return [
            'name' => 'Section',
            'description' => 'Defines a website section with a supporting entry.'
        ];
    }

    /**
     * defineProperties
     */
    public function defineProperties()
    {
        return [
            'handle' => [
                'title' => 'Handle',
                'type' => 'dropdown',
                'showExternalParam' => false
            ],
            'identifier' => [
                'title' => 'Lookup Column',
                'type' => 'dropdown',
                'description' => 'Column name (index) to match with the primary record.',
                'options' => [
                    'slug' => 'Slug',
                    'fullslug' => 'Full Slug',
                    'id' => 'ID (Primary Key)',
                ]
            ],
            'value' => [
                'title' => 'Lookup Value',
                'type' => 'string',
                'description' => 'URL code (slug) used to find the primary record.'
            ],
            'isDefault' => [
                'title' => 'Default View',
                'type' => 'checkbox',
                'description' => 'Used as default entry point when previewing the record.',
                'showExternalParam' => false
            ],
        ];
    }

    /**
     * makePrimaryAccessor returns the PHP object variable for the Twig view layer.
     */
    public function makePrimaryAccessor()
    {
        return new ComponentVariable($this);
    }

    /**
     * init
     */
    public function init()
    {
        Event::listen('cms.sitePicker.overrideParams', function($page, $params, $currentSite, $proposedSite) {
            return $this->handleMultisiteParams($proposedSite, $params);
        });
    }

    /**
     * onRun
     */
    public function onRun()
    {
        if ($token = get('_preview_token')) {
            PreviewToken::checkTokenForCurrentUrl($token);
        }
    }

    /**
     * getHandleOptions
     */
    public function getHandleOptions()
    {
        $blueprints = BlueprintIndexer::instance()->listSections();

        $result = [];
        foreach ($blueprints as $bp) {
            $result[$bp->handle] = $bp->name . ' ('.$bp->handle.')';
        }

        return $result;
    }

    /**
     * getPrimaryRecord
     */
    public function getPrimaryRecord()
    {
        $query = $this->getPrimaryRecordQuery();

        // Previewing
        if ($model = $this->getPreviewModel($query)) {
            return $model;
        }

        // Using single section without identifier value
        if ($query->getModel()->isEntrySingle()) {
            return $query->first();
        }

        // Using entry point identifier value
        if ($model = $this->getEntryPointModel($query)) {
            return $model;
        }

        return null;
    }

    /**
     * getPrimaryRecordQuery
     */
    public function getPrimaryRecordQuery()
    {
        $handle = $this->property('handle');

        $model = EntryRecord::inSection($handle)->applyVisibleFrontend();

        return $model;
    }

    /**
     * getPreviewModel
     */
    protected function getPreviewModel($query)
    {
        if (
            ($token = PreviewToken::getEnabledToken()) &&
            ($previewId = $token->getRouteParam('id'))
        ) {
            return $query->withoutGlobalScopes()->find($previewId);
        }
    }

    /**
     * getEntryPointModel
     */
    protected function getEntryPointModel($query)
    {
        $slug = $this->getEntryPointIdentifierValue();
        if (!$slug) {
            return null;
        }

        $columnName = $this->getEntryPointIdentifierKey();
        return $query->where($columnName, $slug)->first();
    }

    /**
     * getEntryPointIdentifierKey returns the lookup column name
     */
    protected function getEntryPointIdentifierKey(): string
    {
        $validColumns = ['id', 'slug', 'fullslug'];
        $columnName = $this->property('identifier');

        return in_array($columnName, $validColumns) ? $columnName : 'slug';
    }

    /**
     * getEntryPointIdentifierValue
     */
    protected function getEntryPointIdentifierValue()
    {
        if ($value = $this->property('value')) {
            return $value;
        }

        return $this->param($this->getEntryPointIdentifierKey());
    }

    /**
     * getEntryPointIdentifierParamName
     */
    protected function getEntryPointIdentifierParamName()
    {
        if ($param = $this->paramName('value')) {
            return $param;
        }

        return $this->getEntryPointIdentifierKey();
    }

    /**
     * handleMultisiteParams is for multisite
     */
    protected function handleMultisiteParams($site, $params)
    {
        if (!$this->isMultisiteEnabled()) {
            return;
        }

        $otherRecord = $this->findOtherSiteRecords()->where('site_id', $site->id)->first();
        if (!$otherRecord) {
            return;
        }

        $params['id'] = $otherRecord->id;
        $params['slug'] = $otherRecord->slug;
        $params['fullslug'] = $otherRecord->fullslug;

        if ($paramName = $this->getEntryPointIdentifierParamName()) {
            $columnName = $this->getEntryPointIdentifierKey();
            $params[$paramName] = $otherRecord->$columnName;
        }

        return $params;
    }

    /**
     * findOtherSiteRecords is for multisite
     */
    protected function findOtherSiteRecords()
    {
        if ($this->otherSiteCache !== null) {
            return $this->otherSiteCache;
        }

        $primaryRecord = $this->getPrimaryRecord();
        $otherRecords = $primaryRecord->newOtherSiteQuery()->get();

        return $this->otherSiteCache = $otherRecords;
    }

    /**
     * isMultisiteEnabled
     */
    protected function isMultisiteEnabled()
    {
        if ($this->multisiteCache !== null) {
            return $this->multisiteCache;
        }

        $primaryRecord = $this->getPrimaryRecord();

        return $this->multisiteCache = $primaryRecord && $primaryRecord->isMultisiteEnabled();
    }

    /**
     * validateProperties is used to replace older property definitions with new ones
     */
    public function validateProperties(array $properties)
    {
        $properties = parent::validateProperties($properties);

        // @deprecated in v3.2
        if (isset($properties['fullSlug'])) {
            $properties['identifier'] = 'fullslug';
        }

        // @deprecated in v3.3
        if (isset($properties['entryColumn'])) {
            $properties['identifier'] = $properties['entryColumn'];
        }

        if (isset($properties['entrySlug'])) {
            $properties['value'] = $properties['entrySlug'];
        }

        if (isset($properties['entryDefault'])) {
            $properties['isDefault'] = $properties['entryDefault'];
        }

        return $properties;
    }
}
