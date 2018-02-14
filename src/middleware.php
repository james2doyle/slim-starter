<?php

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \Slim\Middleware\JwtAuthentication([
    'secure' => getenv('APP_ENV') !== 'testing',
    'relaxed' => ['localhost', getenv('APP_URL')],
    'path' => '/api',
    'secret' => getenv('JWT_SECRET'),
    'callback' => function ($request, $response, $arguments) use ($container) {
        // try to find a user with this token...
        $users = new \Application\Models\User();
        $user = $users->findByEmail($arguments['decoded']->sub)->run();

        // if we dont find a user, just bail
        if (empty($user[0])) {
            throw new \Exception('No user found for the given token.', 401);
        }

        // assign the user to the container
        $container['user'] = empty($user[0]) ? null : $user[0];
    },
    'error' => function ($request, $response, $arguments) {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode([
                'status' => 'error',
                'message' => $arguments['message'],
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));
