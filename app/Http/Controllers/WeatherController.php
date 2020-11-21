<?php


namespace App\Http\Controllers;


use App\Http\Requests\WeatherRequest;
use App\Http\Services\LocationService;
use App\Http\Services\WeatherService;
use Cmfcmf\OpenWeatherMap;
use DateTime;
use Illuminate\Http\Request;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class WeatherController
{
    public function index(Request $request)
    {
        $data = $request->all();
        if(isset($data['city'])){
            $city = $data['city'];
        }else{
            $location = new LocationService();
            $city = $location->getCurrentLocation();
        }
        $date = $data['date'] ?? date('d.m.y');
        $weather = new WeatherService();
        echo $weather->getWeather($city, $date);
    }
}
