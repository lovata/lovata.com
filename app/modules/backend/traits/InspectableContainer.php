<?php namespace Backend\Traits;

use Lang;
use ApplicationException;

/**
 * InspectableContainer is an extension for controllers that can host
 * inspectable widgets (Components, etc.)
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait InspectableContainer
{
    /**
     * onInspectableGetOptions
     */
    public function onInspectableGetOptions()
    {
        // Disable asset broadcasting
        $this->flushAssets();

        $property = trim(post('inspectorProperty'));
        if (!$property) {
            throw new ApplicationException('The property name is not specified.');
        }

        $className = trim(post('inspectorClassName'));
        if (!$className || $className === 'undefined') {
            throw new ApplicationException('The inspectable class name is not specified.');
        }

        $traitFound = in_array(\System\Traits\PropertyContainer::class, class_uses_recursive($className));
        if (!$traitFound) {
            throw new ApplicationException('Dynamic Inspector control options cannot be loaded for the specified class.');
        }

        $obj = new $className(null);
        $obj->setProperties(post());

        // Nested properties have names like object.property.
        // Convert them to Object.Property.
        $propertyNameParts = explode('.', $property);
        $propertyMethodName = '';
        foreach ($propertyNameParts as $part) {
            $part = trim($part);

            if (!strlen($part)) {
                continue;
            }

            $propertyMethodName .= ucfirst($part);
        }

        // Find options method
        $propertyConfig = $obj->defineProperties()[$property] ?? [];
        $optionsMethod = $propertyConfig['optionsMethod'] ?? ($propertyConfig['options'] ?? null);
        $methodName = is_string($optionsMethod)
            ? $optionsMethod
            : 'get'.$propertyMethodName.'Options';

        if (method_exists($obj, $methodName)) {
            $options = $obj->$methodName();
        }
        else {
            $options = $obj->getPropertyOptions($property);
        }

        // Convert to array to retain the sort order in JavaScript
        $optionsArray = [];
        foreach ((array) $options as $value => $title) {
            $optionsArray[] = ['value' => $value, 'title' => Lang::get($title)];
        }

        return [
            'options' => $optionsArray
        ];
    }
}
