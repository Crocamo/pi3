<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA HOME
$obRouter->get('/', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200, User\Login::getLogin($request));
    }
]);

//ROTA LOGIN (POST)
$obRouter->post('/', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200, User\Login::setLogin($request));
    }
]);

//ROTA LOGOUT
$obRouter->get('/logout', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Login::setLogout($request));
    }
]);
