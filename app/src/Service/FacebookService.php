<?php

namespace App\Service;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
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
    public function allEventsPublished(): bool
    {
        $facebookEvents = $this->getPageEvents();
        foreach ($facebookEvents as $event) {
            $foundEvent = $this->entityManager->getRepository(Event::class)->findOneBy(['facebookEventId' => $event['id']]);
            if ($foundEvent === null) {
                return false;
            }
        }

        return true;
    }
}
