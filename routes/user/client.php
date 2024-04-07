<?php

use \App\Http\Response;
use \App\Controller\user;

//ROTA DE ADMINISTRAÇÃO DE CLIENTES
$obRouter->get('/user/client', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Client::getClient($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO CLIENTE 
$obRouter->get('/user/client/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Client::getNewClient($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO CLIENTE (POST) 
$obRouter->post('/user/client/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Client::setNewClient($request));
    }
]);

//ROTA DE EDIÇÃO DE UM CLIENTE 
$obRouter->get('/user/client/{id}/edit', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Client::getEditClient($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM CLIENTE 
$obRouter->post('/user/client/{id}/edit', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Client::setEditClient($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM CLIENTE 
$obRouter->get('/user/client/{id}/delete', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Client::getDeleteClient($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM CLIENTE (POST)
$obRouter->post('/user/client/{id}/delete', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Client::setDeleteClient($request, $id));
    }
]);

// //ROTA DE EXCLUSÃO DE UM CLIENTE 
// $obRouter->get('/user/client/{id}/delete', [
//     'middlewares' => [
//         'required-user-login'
//     ],
//     function ($request, $id) {
//         return new Response(200, User\Client::getDeleteClient($request, $id));
//     }
// ]);

// //ROTA DE EXCLUSÃO DE UM CLIENTE (POST)
// $obRouter->post('/user/client/{id}/delete', [
//     'middlewares' => [
//         'required-user-login'
//     ],
//     function ($request, $id) {
//         return new Response(200, User\Client::setDeleteClient($request, $id));
//     }
// ]);