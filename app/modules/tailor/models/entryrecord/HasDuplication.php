<?php namespace Tailor\Models\EntryRecord;

/**
 * HasDuplication
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasDuplication
{
    /**
     * duplicateRecord
     */
    public function duplicateRecord()
    {
        $copy = $this->duplicateWithRelations();
        $copy->is_enabled = false;

        if ($this->title) {
            $copy->title = sprintf("%s (%s)", $this->title, __('Copy'));
        }

        if ($this->slug) {
            $copy->slug = $this->slug . '-copy';
        }

        return $copy;
    }
}
