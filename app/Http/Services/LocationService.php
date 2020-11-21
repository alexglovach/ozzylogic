<?php


namespace App\Http\Services;


use ipinfo\ipinfo\IPinfo;

class LocationService
{
    public function getCurrentLocation(): string
    {
        $client = new IPinfo(env('LOCATION_API_TOKEN',''));
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
}
