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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

    #[Route('/proxy', name: 'proxy')]
    public function __invoke(Request $symfonyRequest): Response
    {
        /** @var null|string $uri */
        $uri = $symfonyRequest->query->get('uri');
        if ($uri === null) {
            throw new BadRequestHttpException('Missing query param "uri"');
        }

        $psrRequest = $this->requestFactory->createRequest($symfonyRequest->getMethod(), $uri);
        $psrResponse = $this->httpClient->sendRequest($psrRequest);

        $symfonyResponse = new Response(
            $psrResponse->getBody()->getContents(),
            $psrResponse->getStatusCode(), [
                'Content-Type' => $psrResponse->getHeaderLine('Content-Type'),
            ]
        );
        $symfonyResponse
            ->setPublic()
            ->setTtl(600);

        return $symfonyResponse;
    }
}
