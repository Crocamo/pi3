<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE ADMINISTRAÇÃO DE ORÇAMENTO
$obRouter->get('/user/budget', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Budget::getBudget($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO ORÇAMENTO (POST) 
$obRouter->post('/user/budget' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Budget::setBudget($request));
    }
]);

//ROTA DE EDIÇÃO DE UM ORÇAMENTO 
$obRouter->get('/user/budget/{id}/edit' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Budget::getEditBudget($request,$id));
    }
]);

//ROTA DE EDIÇÃO DE UM ORÇAMENTO 
$obRouter->post('/user/budget/{id}/edit' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Budget::setEditBudget($request,$id));
    }
]);

//ROTA DE CONCLUSÃO DE UM ORÇAMENTO 
$obRouter->get('/user/budget/{idBludServ}/deleteBlutServ' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$idBludServ) {
        return new Response(200,User\Budget::getdeleteBlutServ($request,$idBludServ));
    }
]);

//ROTA DE CONCLUSAO DE UM ORÇAMENTO 
$obRouter->get('/user/budget/{idServ}/{idblut}/add' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$idServ,$idblut) {
        return new Response(200,User\Budget::getAddBlutServ($request,$idServ,$idblut));
    }
]);

//ROTA DE CONCLUSÃO DE UM ORÇAMENTO 
$obRouter->get('/user/budget/{id}/concluir' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Budget::getConcluirBudget($request,$id));
    }
]);

//ROTA DE CONCLUSAO DE UM ORÇAMENTO 
$obRouter->post('/user/budget/{id}/concluir' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Budget::setConcluirBudget($request,$id));
    }
]);

//ROTA PARA CANCELAR UM ORÇAMENTO 
$obRouter->get('/user/budget/{id}/cancel' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Budget::getCancelBudget($request,$id));
    }
]);

//ROTA PARA CANCELAR UM ORÇAMENTO 
$obRouter->post('/user/budget/{id}/cancel' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Budget::setCancelBudget($request,$id));
    }
]);
