<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// Reset endpoint
$router->post('/reset', 'BankController@reset');

// Balance endpoint
$router->get('/balance', 'BankController@getBalance');

// Event endpoint
$router->post('/event', 'BankController@processEvent');
