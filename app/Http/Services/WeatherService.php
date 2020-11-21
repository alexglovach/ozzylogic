<?php


namespace App\Http\Services;


use Cmfcmf\OpenWeatherMap;
use DateTime;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Factory\Guzzle\RequestFactory;

class WeatherService
{

    /**
     * @param string $city
     * @param string $date
     * @return string|void
     */
    public function getWeather(string $city, string $date)
    {
        $httpRequestFactory = new RequestFactory();
        $httpClient = GuzzleAdapter::createWithConfig([]);
        $weatherRequest = new OpenWeatherMap(env('OPEN_WEATHER_MAP_API_KEY',''), $httpClient, $httpRequestFactory);
        $days = $this->dateChecker($date);
        try {
            if ($days == 0) {
                return $this->getWeatherToday($weatherRequest, $city);
            } elseif ($days >= 1) {
                return $this->getWeatherForecasts($weatherRequest, $city, $date, $days);
            } else {
                return "Date is old to get forecasts.";
            }
        } catch (OWMException $e) {
            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            return;
        } catch (\Exception $e) {
            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            return;
        }
    }

    private function dateChecker(string $date): int
    {
        if ($this->validDate($date)) {
            $d = DateTime::createFromFormat('d.m.y', $date);
        } else {
            throw new \Exception('Wrong date format. Enter date in format day.month.year');
        }
        $dateStr = $d->getTimestamp();
        $currentDateStr = strtotime(date('d.m.y'));
        if ($dateStr <= $currentDateStr) return 0;
        return ceil(($dateStr - $currentDateStr) / (60 * 60 * 24));
    }

    private function validDate($date): bool
    {
        $d = DateTime::createFromFormat('d.m.y', $date);
        return $d && $d->format('d.m.y') === $date;
    }


    private function getWeatherToday(OpenWeatherMap $weatherRequest, string $city): string
    {
        $weather = $weatherRequest->getWeather($city, 'metric', 'en');
        return json_encode([
            "temp" => $weather->temperature->getValue(),
            "pressure" => $weather->pressure->getValue(),
            "humidity" => $weather->humidity->getValue()
        ]);
    }

    private function getWeatherForecasts(OpenWeatherMap $weatherRequest, string $city, string $date, int $days): string
    {
        $forecasts = $weatherRequest->getWeatherForecast($city, 'metric', 'en', '', $days);
        foreach ($forecasts as $forecast) {
            if ($forecast->time->day->format('d.m.y') == $date) {
                return json_encode([
                    "temp" => $forecast->temperature->getValue(),
                    "pressure" => $forecast->pressure->getValue(),
                    "humidity" => $forecast->humidity->getValue()
                ]);
            }
        }
    }
}
