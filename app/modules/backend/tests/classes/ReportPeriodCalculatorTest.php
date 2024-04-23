<?php

use Carbon\Carbon;
use Backend\Classes\Dashboard\ReportPeriodCalculator;

class ReportPeriodCalculatorTest extends TestCase
{
    public function testFullYears()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2020, 1, 1);
        $endDate = Carbon::create(2022, 12, 31);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2017-01-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2019-12-31', $result->getEndDate()->toDateString());
    }

    public function testPartialYears()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2020, 2, 1);
        $endDate = Carbon::create(2022, 12, 31);

        // This must fall back to months
        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2017-03-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2020-01-31', $result->getEndDate()->toDateString());
    }

    public function testFullMonths()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2022, 1, 1);
        $endDate = Carbon::create(2022, 2, 28);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2021-11-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-12-31', $result->getEndDate()->toDateString());

        $startDate = Carbon::create(2022, 6, 1);
        $endDate = Carbon::create(2022, 6, 30);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2022-05-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-05-31', $result->getEndDate()->toDateString());
    }

    public function testPartialMonths()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2022, 2, 15);
        $endDate = Carbon::create(2022, 2, 25);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2022-02-04', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-02-14', $result->getEndDate()->toDateString());
    }

    public function testFullWeeks()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2022, 2, 14);
        $endDate = Carbon::create(2022, 2, 20);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2022-02-07', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-02-13', $result->getEndDate()->toDateString());
    }

    public function testPartialWeeks()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2022, 2, 13);
        $endDate = Carbon::create(2022, 2, 20);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2022-02-05', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-02-12', $result->getEndDate()->toDateString());
    }

    public function testFullQuarters()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2022, 1, 1);
        $endDate = Carbon::create(2022, 3, 31);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2021-10-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-12-31', $result->getEndDate()->toDateString());

        $startDate = Carbon::create(2022, 4, 1);
        $endDate = Carbon::create(2022, 6, 30);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2022-01-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-03-31', $result->getEndDate()->toDateString());
    }

    public function testDays()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2021, 12, 30);
        $endDate = Carbon::create(2022, 1, 5);

        $result = $calculator->getPreviousPeriod($startDate, $endDate);

        $this->assertEquals('2021-12-23', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-12-29', $result->getEndDate()->toDateString());
    }

    public function testFullYearsLastYear()
    {
        $calculator = new ReportPeriodCalculator();

        $startDate = Carbon::create(2020, 1, 1);
        $endDate = Carbon::create(2022, 12, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2017-01-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2019-12-31', $result->getEndDate()->toDateString());

        $startDate = Carbon::create(2022, 1, 1);
        $endDate = Carbon::create(2022, 12, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2021-01-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-12-31', $result->getEndDate()->toDateString());
    }

    public function testFullQuartersLastYear()
    {
        $calculator = new ReportPeriodCalculator();

        // One quarter
        $startDate = Carbon::create(2022, 4, 1);
        $endDate = Carbon::create(2022, 6, 30);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2021-04-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-06-30', $result->getEndDate()->toDateString());

        // Three quarters, same year
        $startDate = Carbon::create(2022, 4, 1);
        $endDate = Carbon::create(2022, 12, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2021-04-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-12-31', $result->getEndDate()->toDateString());

        // Two quarters, spanning two years
        $startDate = Carbon::create(2021, 10, 1);
        $endDate = Carbon::create(2022, 3, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2020-10-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-03-31', $result->getEndDate()->toDateString());

        // Five quarters, spanning two years
        $startDate = Carbon::create(2021, 1, 1);
        $endDate = Carbon::create(2022, 3, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2019-01-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2020-03-31', $result->getEndDate()->toDateString());
    }

    public function testFullMonthsLastYear()
    {
        $calculator = new ReportPeriodCalculator();

        // One month
        $startDate = Carbon::create(2022, 5, 1);
        $endDate = Carbon::create(2022, 5, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2021-05-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-05-31', $result->getEndDate()->toDateString());

        // Two months, leap year
        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2024, 2, 29);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2023-01-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2023-02-28', $result->getEndDate()->toDateString());

        // Two months, spanning two years
        $startDate = Carbon::create(2021, 12, 1);
        $endDate = Carbon::create(2022, 1, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2020-12-01', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-01-31', $result->getEndDate()->toDateString());
    }

    public function testFullWeeksLastYear()
    {
        $calculator = new ReportPeriodCalculator();

        // One week
        $startDate = Carbon::create(2022, 1, 3);
        $endDate = Carbon::create(2022, 1, 9);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2021-01-04', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-01-10', $result->getEndDate()->toDateString());

        // One week, spanning two years
        $startDate = Carbon::create(2021, 12, 27);
        $endDate = Carbon::create(2022, 1, 2);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2020-12-28', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-01-03', $result->getEndDate()->toDateString());

        // One week, last week of the year
        $startDate = Carbon::create(2023, 12, 25);
        $endDate = Carbon::create(2023, 12, 31);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2022-12-26', $result->getStartDate()->toDateString());
        $this->assertEquals('2023-01-01', $result->getEndDate()->toDateString());

        // Three weeks
        $startDate = Carbon::create(2022, 2, 7);
        $endDate = Carbon::create(2022, 2, 27);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);
        $this->assertEquals('2021-02-08', $result->getStartDate()->toDateString());
        $this->assertEquals('2021-02-28', $result->getEndDate()->toDateString());
    }

    public function testDaysLastYear()
    {
        $calculator = new ReportPeriodCalculator();

        // Days in the same year
        $startDate = Carbon::create(2023, 1, 10);
        $endDate = Carbon::create(2023, 1, 17);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2022-01-10', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-01-17', $result->getEndDate()->toDateString());

        // Days spanning two years
        $startDate = Carbon::create(2022, 12, 29);
        $endDate = Carbon::create(2023, 1, 3);

        $result = $calculator->getPreviousPeriodLastYear($startDate, $endDate);

        $this->assertEquals('2021-12-29', $result->getStartDate()->toDateString());
        $this->assertEquals('2022-01-03', $result->getEndDate()->toDateString());
    }
}