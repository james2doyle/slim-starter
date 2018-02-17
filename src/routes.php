<?php

// Routes
$app->post('/auth/login', Application\Controller\LoginController::class);
$app->get('/api/users', Application\Controller\UserApiController::class . ':index');
$app->get('/stream', Application\Controller\StreamFileController::class)->setOutputBuffering(false);
$app->get('/about', Application\Controller\HomeController::class . ':about');
$app->get('/[{name}]', Application\Controller\HomeController::class . ':index');
