<?php namespace Backend\VueComponents\TreeView;

/**
 * SectionList encapsulates a list of Treeview sections.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class SectionList
{
    /**
     * @var array sections
     */
    protected $sections = [];

    /**
     * @var mixed childKeyPrefix
     */
    protected $childKeyPrefix;

    /**
     * addSection
     */
    public function addSection(string $label, string $key)
    {
        $section = new SectionDefinition($label, $key);
        $section->setChildKeyPrefix($this->childKeyPrefix);

        return $this->sections[] = $section;
    }

    /**
     * getSections
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * setChildKeyPrefix
     */
    public function setChildKeyPrefix($prefix)
    {
        $this->childKeyPrefix = $prefix;

        return $this;
    }

    /**
     * toArray
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->sections as $section) {
            $result[] = $section->toArray();
        }

        return $result;
    }
}
