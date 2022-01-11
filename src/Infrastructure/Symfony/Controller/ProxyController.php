<?php

namespace App\Infrastructure\Symfony\Controller;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProxyController extends AbstractController
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private HttpFoundationFactory $foundationFactory;

    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, HttpFoundationFactory $foundationFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->foundationFactory = $foundationFactory;
    }
    
    #[Route("/proxy/{uri}", name: "proxy", requirements: ['uri' => '.+'])]
    public function __invoke(Request $currentRequest, string $uri): Response
    {
        $request = $this->requestFactory->createRequest($currentRequest->getMethod(), $uri);
        $response = $this->httpClient->sendRequest($request);

        return new Response($response->getBody(), $response->getStatusCode(), [
            'Content-Type' => $response->getHeaderLine('Content-Type')
        ]);

        // TODO: for some reason, response gets truncated this way
        // ie. Content-Length says 43, actual length is 58
        return $this->foundationFactory->createResponse($response);
    }
}
