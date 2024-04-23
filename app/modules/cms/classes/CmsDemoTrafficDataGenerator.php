<?php namespace Cms\Classes;

use Str;
use App;
use Carbon\Carbon;
use Cms\Models\TrafficStatisticsPageview;

/**
 * CmsDemoTrafficDataGenerator generates CMS demo traffic data
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class CmsDemoTrafficDataGenerator
{
    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('cms.demos.traffic');
    }

    /**
     * generate the demo traffic
     */
    public function generate()
    {
        for ($i = 0; $i < 40; $i++) {
            $date = Carbon::now()->subDays($i);

            $sineWaveValue = sin((($i % 7) / 7) * 2 * M_PI)*0.3 + rand(10, 20) / 100;
            $maxPageViews = (int) round((($sineWaveValue + 1) / 2) * 100);

            $randomFactor = rand(95, 105) / 100;
            $maxPageViews = (int) round($maxPageViews * $randomFactor);

            $recordsExists = TrafficStatisticsPageview::where('ev_date', $date->format('Y-m-d'))->count() > 0;
            if ($recordsExists) {
                continue;
            }

            for ($j = 0; $j < $maxPageViews; $j++) {
                $pageview = new TrafficStatisticsPageview();
                $pageview->ev_datetime = $date->format('Y-m-d H:i:s');
                $pageview->ev_date = $date->toDateString();
                $pageview->ev_year_month_day = $date->toDateString();
                $pageview->ev_year = $date->copy()->startOfYear()->toDateString();
                $pageview->ev_year_quarter  = $date->copy()->startOfQuarter()->toDateString();
                $pageview->ev_year_month  = $date->copy()->startOfMonth()->toDateString();
                $pageview->ev_year_week  = $date->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
                $pageview->ev_timestamp = $date->format('Y-m-d H:i:s');
                $pageview->user_authenticated = $this->randomBoolean();
                $pageview->client_id = $this->getClientId();
                $pageview->first_time_visit = $this->randomBoolean();
                $pageview->user_agent = $this->randomUserAgent();
                $pageview->page_path = $this->getPagePath();
                $pageview->ip = '192.168.1.' . rand(1, 255);
                list($pageview->country, $pageview->city) = $this->getCountryAndCity();
                $pageview->referral_domain = $this->getReferralDomain();
                $pageview->save();
            }
        }
    }

    private function randomBoolean()
    {
        return rand(0, 1) === 1;
    }

    private function randomUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 13.5; rv:109.0) Gecko/20100101 Firefox/116.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36 Edg/115.0.1901.188'
        ];

        return $userAgents[array_rand($userAgents)];
    }

    private function getPagePath()
    {
        $paths = ["/", "blog", "products/phone", "about", "contact"];

        return $this->getWeightedRandom($paths);
    }

    private function getClientId()
    {
        $fixedClientIds = array_map(function ($i) {
            return 'fixed-client-id' . $i;
        }, range(1, 20));

        if (rand(1, 100) <= 80) {
            // 20% of the time, return a pseudo-unique ID
            return Str::random(64);
        }

        // 80% of the time, return one of the fixed client IDs
        return $fixedClientIds[array_rand($fixedClientIds)];
    }

    private function getCountryAndCity()
    {
        $countriesAndCities = [
            'France' => ['Paris', 'Lyon', 'Marseille'],
            'Germany' => ['Berlin', 'Munich', 'Hamburg'],
            'Italy' => ['Rome', 'Milan', 'Naples'],
            'Spain' => ['Madrid', 'Barcelona', 'Valencia'],
            'Netherlands' => ['Amsterdam', 'Rotterdam', 'The Hague'],
            'Sweden' => ['Stockholm', 'Gothenburg', 'Malmo'],
            'Poland' => ['Warsaw', 'Krakow', 'Lodz'],
        ];

        $country = $this->getWeightedRandom(array_keys($countriesAndCities));
        $city = $this->getWeightedRandom($countriesAndCities[$country]);

        return [$country, $city];
    }

    private function getReferralDomain()
    {
        if (!$this->randomBoolean()) {
            return null;
        }

        $domains = [
            'facebook.com', 'twitter.com', 'linkedin.com',
            'instagram.com', 'nytimes.com', 'bbc.co.uk'
        ];

        return $domains[array_rand($domains)];
    }

    private function getWeightedRandom($array)
    {
        $weights = [];
        $totalWeight = 0;
        foreach ($array as $index => $item) {
            $weight = count($array) - $index; // More weight for earlier entries
            $weights[$index] = $weight;
            $totalWeight += $weight;
        }

        $randomWeight = mt_rand(1, $totalWeight);
        $selectedKey = null;
        foreach ($weights as $key => $weight) {
            $randomWeight -= $weight;
            if ($randomWeight <= 0) {
                $selectedKey = $key;
                break;
            }
        }

        return $array[$selectedKey];
    }
}
