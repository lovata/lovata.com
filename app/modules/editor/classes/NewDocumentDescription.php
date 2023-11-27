<?php namespace Editor\Classes;

/**
 * NewDocumentDescription contains information required for creating new documents in Editor.
 *
 * @package october\editor
 * @author Alexey Bobkov, Samuel Georges
 */
class NewDocumentDescription
{
    /**
     * @var string label
     */
    protected $label;

    /**
     * @var string icon
     */
    protected $icon;

    /**
     * @var mixed metadata
     */
    protected $metadata;

    /**
     * @var mixed documentData
     */
    protected $documentData;

    /**
     * __construct
     */
    public function __construct(string $label, array $metadata)
    {
        $this->label = $label;
        $this->metadata = $metadata;
    }

    /**
     * setIcon
     */
    public function setIcon(string $backgroundColor, string $iconClassName)
    {
        $this->icon = [
            'backgroundColor' => $backgroundColor,
            'cssClass' => $iconClassName
        ];

        return $this;
    }

    /**
     * setInitialDocumentData
     */
    public function setInitialDocumentData($documentData)
    {
        $this->documentData = $documentData;
    }

    /**
     * toArray
     */
    public function toArray()
    {
        $result = [
            'label' => $this->label,
            'metadata' => $this->metadata
        ];

        if ($this->icon) {
            $result['icon'] = $this->icon;
        }

        if ($this->documentData) {
            $result['document'] = $this->documentData;
        }

        return $result;
    }
}
