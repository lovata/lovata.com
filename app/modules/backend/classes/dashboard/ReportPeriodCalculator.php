<?php namespace Backend\Classes\Dashboard;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * ReportPeriodCalculator is a utility class for calculating previous time periods based on given start and end dates.
 * The class can handle periods defined as multiples of full years, full quarters,
 * full months, full weeks, and full days. It calculates the start and end dates of
 * the corresponding previous period.
 */
class ReportPeriodCalculator
{
    /**
     * Get the start and end dates for the previous period based on the given period.
     *
     * This method identifies if the current period represents a multiple of full years,
     * full quarters, full months, full weeks, or full days. It then calculates the
     * corresponding previous period, taking into account the same multiple.
     *
     * @param Carbon $startDate Start date of the current period.
     * @param Carbon $endDate End date of the current period.
     * @return ?CarbonPeriod Returns a CarbonPeriod object representing the previous period.
     * Returns null if the previous period cannot be calculated.
     */
    public function getPreviousPeriod(Carbon $startDate, Carbon $endDate): ?CarbonPeriod
    {
        $startDate = clone $startDate;
        $endDate = clone $endDate;

        $years = $startDate->diffInYears($endDate) + 1;
        $quarters = $startDate->diffInQuarters($endDate) + 1;
        $months = $startDate->diffInMonths($endDate) + 1;
        $weeks = $startDate->diffInWeeks($endDate) + 1;
        $days = $startDate->diffInDays($endDate) + 1;

        $prevPeriodEndDate = $startDate->copy()->subDay();

        if ($years >= 1 && $startDate->day === 1 &&
            $startDate->month === 1 && $endDate->day === 31 && $endDate->month === 12) {
            return new CarbonPeriod($startDate->copy()->subYears($years), $prevPeriodEndDate);
        }
        elseif ($this->isFullQuarter($startDate, $endDate)) {
            return new CarbonPeriod($startDate->copy()->subQuarters($quarters), $prevPeriodEndDate);
        }
        elseif ($this->isFullMonth($startDate, $endDate)) {
            return new CarbonPeriod($startDate->copy()->subMonths($months), $prevPeriodEndDate);
        }
        elseif ($this->isFullWeek($startDate, $endDate)) {
            return new CarbonPeriod($startDate->copy()->subWeeks($weeks), $prevPeriodEndDate);
        }
        elseif ($days >= 1) {
            return new CarbonPeriod($startDate->copy()->subDays($days), $prevPeriodEndDate);
        }

        return null;
    }

    /**
     * Get the start and end dates for the previous period of the last year based on the given period.
     *
     * This method identifies if the current period represents a multiple of full years,
     * full quarters, full months, full weeks, or full days. It then calculates the
     * corresponding previous period for the last year, taking into account the same multiple.
     *
     * @param Carbon $startDate Start date of the current period.
     * @param Carbon $endDate End date of the current period.
     * @return ?CarbonPeriod Returns a CarbonPeriod object representing the previous period for the last year.
     * Returns null if the previous period cannot be calculated.
     */
    public function getPreviousPeriodLastYear(Carbon $startDate, Carbon $endDate): ?CarbonPeriod
    {
        $startDate = clone $startDate;
        $endDate = clone $endDate;

        $years = $startDate->diffInYears($endDate) + 1;
        $weeks = $startDate->diffInWeeks($endDate) + 1;
        $days = $startDate->diffInDays($endDate) + 1;

        if ($this->isFullYear($startDate, $endDate)) {
            $prevEndDate = $startDate->copy()->subDay();
            return new CarbonPeriod($startDate->copy()->subYears($years), $prevEndDate);
        }
        elseif ($this->isFullQuarter($startDate, $endDate)) {
            $prevStartDate = $startDate->copy()->subYears($years);
            $prevEndDate = $endDate->copy()->subYears($years);

            return new CarbonPeriod($prevStartDate, $prevEndDate);
        }
        elseif ($this->isFullMonth($startDate, $endDate)) {
            $prevStartDate = $startDate->copy()->subYears($years);
            $prevEndDate = Carbon::create($endDate->year - $years, $endDate->month, 1)->endOfMonth();
            return new CarbonPeriod($prevStartDate, $prevEndDate);
        }
        elseif ($this->isFullWeek($startDate, $endDate)) {
            $startWeek = $startDate->weekOfYear;
            $prevStartDate = Carbon::create($startDate->year - $years, 1, 1)->addWeeks($startWeek)->startOfWeek();
            $prevEndDate = $prevStartDate->copy()->addWeeks($weeks - 1)->endOfWeek();

            return new CarbonPeriod($prevStartDate, $prevEndDate);
        }
        elseif ($days >= 1) {
            $prevStartDate = $startDate->copy()->subYears($years);
            $prevEndDate = $endDate->copy()->subYears($years);

            return new CarbonPeriod($prevStartDate, $prevEndDate);
        }

        return null;
    }

    private function isFullYear(Carbon $startDate, Carbon $endDate): bool
    {
        return $startDate->day === 1 && $startDate->month === 1 && $endDate->day === 31 && $endDate->month === 12;
    }

    private function isFullQuarter(Carbon $startDate, Carbon $endDate): bool
    {
        return $startDate->day === 1 &&
               in_array($startDate->month, [1, 4, 7, 10]) &&
               in_array($endDate->month, [3, 6, 9, 12]) &&
               $endDate->day === $endDate->endOfMonth()->day;
    }

    private function isFullMonth(Carbon $startDate, Carbon $endDate): bool
    {
        return $startDate->day === 1 && $endDate->day === $endDate->endOfMonth()->day;
    }

    private function isFullWeek(Carbon $startDate, Carbon $endDate): bool
    {
        return $startDate->dayOfWeekIso === 1 && $endDate->dayOfWeekIso === 7;
    }

    private function isFullDay(Carbon $startDate, Carbon $endDate): bool
    {
        return $startDate->diffInDays($endDate) >= 0;
    }
}
