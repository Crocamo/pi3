<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA PAGINA INICIAL (USER)
$obRouter->get('/user' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Home::getHome($request));
    }
]);
