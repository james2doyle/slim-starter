<?php

namespace Application\Controller;

use Psr\Container\ContainerInterface;
use Application\Models\User;

class HomeController
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index($request, $response, $args)
    {
        $this->container->logger->info(getenv('APP_ENV') . " '/{name}' route", $args);
        // Render index view
        return $this->container->renderer->render($response, 'index.phtml', $args);
    }

    public function about($request, $response, $args)
    {
        $this->container->logger->info(getenv('APP_ENV') . " '/about' route", $args);

        $tagged_response = $this->container->httpCache->withEtag($response, crc32('about_' . json_encode($args)));
        return $this->container->renderer->render($tagged_response, 'about.phtml', $args);
    }
}
