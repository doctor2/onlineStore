<?php

namespace App\Bundle\CoreBundle\Controller\Api;

use App\Bundle\CoreBundle\Repository\CityRepository;
use App\Bundle\CoreBundle\Service\IpLocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CityController extends AbstractController
{
    #[Route('/search-city', name: 'search_city')]
    public function findCity(Request $request, IpLocationService $ipLocation): Response
    {
        $location = $ipLocation->getLocationByIp($request->getClientIp());

        return $this->json([
            'city' => $location['city'] ?? null,
            'region' => $location['region'] ?? null
        ]);
    }

    #[Route('/get-city', name: 'get_city')]
    public function getCity(Request $request): Response
    {
        return $this->json([
            'city' => $request->getSession()->get('city'),
            'region' => $request->getSession()->get('region'),
        ]);
    }

    #[Route('/get-cities', name: 'get_cities')]
    public function getCities(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();

        return $this->json($cities);
    }

    #[Route('/set-city', name: 'set_city', methods: ['POST'])]
    public function setCity(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $request->getSession()->set('city', $data['city']);
        $request->getSession()->set('region', $data['region']);

        return $this->json(['status' => 'ok']);
    }
}
