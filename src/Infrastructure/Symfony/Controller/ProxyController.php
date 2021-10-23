<?php

namespace App\Infrastructure\Symfony\Controller;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProxyController extends AbstractController
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;

    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }
    
    /**
     * @Route("/proxy/{uri}", name="proxy", requirements={"uri": ".+"})
     */
    public function __invoke(Request $currentRequest, string $uri): Response
    {
        $request = $this->requestFactory->createRequest($currentRequest->getMethod(), $uri);
        $response = $this->httpClient->sendRequest($request);
        dump($response);
        
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProxyController.php',
        ]);
    }
}
