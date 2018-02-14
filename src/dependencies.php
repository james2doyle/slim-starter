<?php

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new \Slim\Views\PhpRenderer($settings['template_path']);
};

$container['jwt'] = function ($c) {
    return new \StdClass;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    if ($webhook = getenv('SLACK_WEBHOOK')) {
        $logger->pushHandler(new \Monolog\Handler\SlackWebhookHandler($webhook));
    }
    return $logger;
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        if (in_array('application/json', $request->getHeader('Accept'))) {
            $code = empty($exception->getCode()) ? 500 : $exception->getCode();
            return $c['response']
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ]);
        }

        return $c['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write((string)$exception);
    };
};
