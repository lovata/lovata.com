<?php namespace System\Classes\PresetManager;

use DateTime as PhpDateTime;
use DateTimeZone;

/**
 * Dates is a resource file with minimal dependencies
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Dates
{
    /**
     * timezones collection
     */
    public static function timezones(): array
    {
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();
        $utcTime = new PhpDateTime('now', new DateTimeZone('UTC'));

        $tempTimezones = [];
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = [
                'offset' => (int) $currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            ];
        }

        // Sort the array by offset, identifier ascending
        usort($tempTimezones, function ($a, $b) {
            return $a['offset'] === $b['offset']
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = [];
        foreach ($tempTimezones as $tz) {
            $sign = $tz['offset'] == 0 ? '' : ($tz['offset'] > 0 ? '+' : '-');
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = '(' . $sign . $offset . ') ' . str_replace('_', ' ', $tz['identifier']);
        }

        return $timezoneList;
    }
}
