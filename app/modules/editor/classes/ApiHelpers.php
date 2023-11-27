<?php namespace Editor\Classes;

use SystemException;

/**
 * ApiHelpers basic functions for the Editor
 *
 * @package october\editor
 * @author Alexey Bobkov, Samuel Georges
 */
class ApiHelpers
{
    /**
     * assertGetKey
     */
    public static function assertGetKey($array, $key)
    {
        if (!array_key_exists($key, $array)) {
            throw new SystemException(sprintf('Key %s not found in the array', $key));
        }

        return $array[$key];
    }

    /**
     * assertIsArray
     */
    public static function assertIsArray($value)
    {
        if (!is_array($value)) {
            throw new SystemException('Value is not array');
        }

        return $value;
    }
}
