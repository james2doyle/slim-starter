<?php

namespace Application\Controller;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class BaseController
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        if (method_exists($this, 'handle')) {
            return $this->handle($request, $response, $args);
        } else {
            throw new \Exception('Cannot invoke class without a handle method', 500);
        }
    }
}
