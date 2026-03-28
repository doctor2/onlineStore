<?php

namespace App\Bundle\CoreBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpLocationService
{
    public function __construct(private HttpClientInterface $httpClient)
    {}

    public function getLocationByIp(string $ip): ?array
    {
        $url = "https://ipwho.is/{$ip}";

        $response = $this->httpClient->request('GET', $url, [
            'timeout' => 20,
        ])->getContent(false);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);

        if (!($data['success'] ?? false)) {
            return null;
        }

        return [
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null
        ];
    }

}
