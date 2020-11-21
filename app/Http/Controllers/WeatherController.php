<?php


namespace App\Http\Controllers;


use App\Http\Requests\WeatherRequest;
use Cmfcmf\OpenWeatherMap;
use DateTime;
use Illuminate\Http\Request;
use ipinfo\ipinfo\IPinfo;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class WeatherController
{
    public function index(Request $request)
    {
        $data = $request->all();
        $city = $data['city'] ?? $this->getCurrentLocation();
        $date = $data['date'] ?? date('d.m.y');
        echo $this->getWeather($city, $date);
    }

    private function getCurrentLocation(): string
    {
        $accessToken = '86d6b605f149b6';
        $client = new IPinfo($accessToken);
        $ipAddress = $this->getUserIp();
        $details = $client->getDetails($ipAddress);
        return $details->city;
    }

    private function getUserIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function dateChecker(string $date): int
    {
        if ($this->validDate($date)) {
            $d = DateTime::createFromFormat('d.m.y', $date);
        } else {
            echo "Wrond date format. Enter date in format day.month.year";
            die();
        }
        $dateStr = $d->getTimestamp();
        $currentDateStr = strtotime(date('d.m.y'));
        if ($dateStr <= $currentDateStr) return 0;
        return ceil(($dateStr - $currentDateStr) / (60 * 60 * 24));
    }

    private function validDate($date)
    {
        $d = DateTime::createFromFormat('d.m.y', $date);
        return $d && $d->format('d.m.y') === $date;
    }

    private function getWeather(string $city, string $date)
    {
        $apiKey = 'b8970f7a31c08f69b71b8ee124b7b26b';
        $httpRequestFactory = new RequestFactory();
        $httpClient = GuzzleAdapter::createWithConfig([]);
        $weatherRequest = new OpenWeatherMap($apiKey, $httpClient, $httpRequestFactory);
        $days = $this->dateChecker($date);
        try {
            if ($days == 0) {
                return $this->getWeatherToday($weatherRequest, $city);
            } elseif ($days >= 1) {
                return $this->getWeatherForecasts($weatherRequest, $city, $date,$days);
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
