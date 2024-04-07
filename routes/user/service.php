<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE ADMINISTRAÇÃO DE SERVIÇOS
$obRouter->get('/user/service', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Service::getService($request));
    }
]);

//ROTA DE CRIAÇÃO DE SERVIÇO
$obRouter->get('/user/service/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Service::getNewService($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO SERVIÇO (POST) 
$obRouter->post('/user/service/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Service::setNewService($request));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->get('/user/service/{id}/edit', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Service::getEditService($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->post('/user/service/{id}/edit', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Service::setEditService($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM SERVIÇO 
$obRouter->get('/user/service/{id}/delete', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Service::getDeleteService($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM SERVIÇO 
$obRouter->post('/user/service/{id}/delete', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request, $id) {
        return new Response(200, User\Service::setDeleteService($request, $id));
    }
]);
