<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $defaultGraphVersion,
        private string $pageAccessToken,
        private string $cobaltoPageId
    ) {}

    public function getPageEvents(): array
    {
        $url = sprintf('https://graph.facebook.com/%s/%s/events', $this->defaultGraphVersion, $this->cobaltoPageId);
        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'access_token' => $this->pageAccessToken,
            ]
        ]);

        return $response->toArray()['data'];
    }
}
