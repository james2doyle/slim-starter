<?php

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \Slim\HttpCache\Cache('public', 86400));

$app->add(new \Slim\Middleware\JwtAuthentication([
    'secure' => getenv('APP_ENV') !== 'testing',
    'relaxed' => ['localhost', getenv('APP_URL')],
    'path' => '/api',
    'secret' => getenv('JWT_SECRET'),
    'callback' => function ($request, $response, $arguments) use ($container) {
        $email = $arguments['decoded']->sub;
        $cache_key = 'au-' . crc32($email);
        // this function obeys the cache default TTL
        $user = $container['cache']->get($cache_key, function () use ($container, $email) {
            // try to find a user with this token...
            $users = new \Application\Models\User();
            $user = $users->findByEmail($email)->run();
            // if we dont find a user, just bail
            if (empty($user[0])) {
                throw new \Exception('No user found for the given token.', 401);
            }
            $container['logger']->info(getenv('APP_ENV') . ' fetched fresh user from db');
            return $user[0];
        });

        // assign the user to the container
        $container['user'] = $user;
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
