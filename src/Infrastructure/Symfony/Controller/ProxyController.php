<?php

declare(strict_types=1);

/*
 * This file is part of the Sigwin Pipes project.
 *
 * (c) sigwin.hr
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Infrastructure\Symfony\Controller;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProxyController extends AbstractController
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;

    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    #[Route('/proxy/{uri}', name: 'proxy', requirements: ['uri' => '.+'])]
    public function __invoke(Request $currentRequest, string $uri): Response
    {
        $request = $this->requestFactory->createRequest($currentRequest->getMethod(), $uri);
        $response = $this->httpClient->sendRequest($request);

        return new Response($response->getBody()->getContents(), $response->getStatusCode(), [
            'Content-Type' => $response->getHeaderLine('Content-Type'),
        ]);
    }
}
