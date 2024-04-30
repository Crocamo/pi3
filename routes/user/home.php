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


//ROTA DE EDIÇÃO DE UM ORÇAMENTO 
$obRouter->get('/user/{id}/imprimir' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Home::getPrintBudget($request,$id));
    }
]);

//ROTA DE EDIÇÃO DE UM ORÇAMENTO 
$obRouter->post('/user/{id}/imprimir' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Home::setPrintBudget($request,$id));
    }
]);