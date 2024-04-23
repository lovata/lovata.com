<?php namespace Tailor\Models\EntryRecord;

use Date;
use Tailor\Classes\Scopes\DraftableScope;

/**
 * HasStatusScopes
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasStatusScopes
{
    /**
     * getStatusCodeAttribute
     */
    public function getStatusCodeAttribute()
    {
        if ($this->useDrafts() && $this->isDraftStatus()) {
            return 'draft';
        }

        $isEnabled = $this->is_enabled !== null
            ? $this->is_enabled
            : $this->isEntryEnabledByDefault();

        if (!$isEnabled) {
            return 'hidden';
        }

        if ($this->published_at || $this->expired_at) {
            $now = Date::now();

            if ($this->published_at && $now < $this->published_at) {
                return 'scheduled';
            }

            if ($this->expired_at && $now > $this->expired_at) {
                return 'expired';
            }
        }

        if ($this->trashed()) {
            return 'deleted';
        }

        return 'published';
    }

    /**
     * getStatusNameOptions
     */
    public function getStatusCodeOptions()
    {
        return [
            'published' => ['Published', 'var(--bs-green)'],
            'expired' => ['Expired', 'var(--bs-red)'],
            'scheduled' => ['Scheduled', 'var(--bs-indigo)'],
            'hidden' => ['Hidden', '#bdc3c7'],
            'draft' => ['Draft', 'var(--bs-orange)'],
            'deleted' => ['Deleted', '#536061']
        ];
    }

    /**
     * scopeApplyStatusFromFilter
     */
    public function scopeApplyStatusFromFilter($query, $scope)
    {
        if ($scope->value === 'published') {
            return $query->applyPublishedStatus();
        }

        if ($scope->value === 'expired') {
            return $query->applyExpiredStatus();
        }

        if ($scope->value === 'scheduled') {
            return $query->applyScheduledStatus();
        }

        if ($scope->value === 'draft') {
            return $query->applyDraftStatus();
        }

        if ($scope->value === 'hidden') {
            return $query->applyHiddenStatus();
        }

        if ($scope->value === 'deleted') {
            return $query->onlyTrashed();
        }

        return $query;
    }

    /**
     * scopeApplyPublished
     */
    public function scopeApplyPublishedStatus($query)
    {
        $now = Date::now();

        return $query->where('is_enabled', 1)
            ->where(function($q) use ($now) {
                $q->whereNull('published_at');
                $q->orWhere('published_at', '<', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('expired_at');
                $q->orWhere('expired_at', '>', $now);
            })
        ;
    }

    /**
     * scopeApplyPublished
     */
    public function scopeApplyScheduledStatus($query)
    {
        $now = Date::now();

        return $query->where('is_enabled', true)
            ->where('published_at', '>', $now)
        ;
    }

    /**
     * scopeApplyPublished
     */
    public function scopeApplyExpiredStatus($query)
    {
        $now = Date::now();

        return $query->where('is_enabled', true)
            ->where('expired_at', '<', $now)
        ;
    }

    /**
     * scopeApplyHiddenStatus
     */
    public function scopeApplyHiddenStatus($query)
    {
        return $query->where('is_enabled', '<>', true)
            ->where('draft_mode', DraftableScope::MODE_PUBLISHED);
    }

    /**
     * scopeApplyPublished
     */
    public function scopeApplyDraftStatus($query)
    {
        return $query->where('draft_mode', '<>', DraftableScope::MODE_PUBLISHED);
    }
}
