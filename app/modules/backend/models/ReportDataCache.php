<?php

namespace Backend\Models;

use Db;
use Model;
use Carbon\Carbon;

/**
 * ReportDataCache manages the back-end report data database cache
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ReportDataCache extends Model
{
    /**
     * @var string table associated with the model
     */
    public $table = 'backend_report_data_cache';

    /**
     * getRanges
     */
    public function getRanges(string $key, Carbon $dateStart, Carbon $dateEnd): array
    {
        self::deleteOldItems();

        $values = self::select('value', 'value_date')
            ->where('value_date', '>=', $dateStart->toDateString())
            ->where('value_date', '<=', $dateEnd->toDateString())
            ->where('key', $key)
            ->orderBy('value_date')
            ->get();

        $result = [];
        foreach ($values as $obj) {
            $result[$obj->value_date] = json_decode($obj->value, false);
        }

        return $result;
    }

    /**
     * putRange
     */
    public function putRange(string $key, array $dataPoints, bool $excludeTodayDate = true)
    {
        Db::transaction(function () use ($key, $dataPoints, $excludeTodayDate) {
            $todayDate = Carbon::now()->toDateString();

            foreach ($dataPoints as $dataPoint) {
                $dateString = $dataPoint->oc_dimension;
                $value = $dataPoint;

                if ($excludeTodayDate && $todayDate == $dateString) {
                    continue;
                }

                $existing = self::where('key', $key)
                    ->where('value_date', $dateString)
                    ->first();

                if ($existing) {
                    $existing->delete();
                }

                $obj = new self();
                $obj->key = $key;
                $obj->value = json_encode($value);
                $obj->value_date = $dateString;
                $obj->save();
            }
        });
    }

    /**
     * deleteOldItems
     */
    public static function deleteOldItems()
    {
        $latestDate = new Carbon();
        $latestDate->subDays(30);

        self::where('created_at', '<', $latestDate->toDateTimeString())->delete();
    }
}
