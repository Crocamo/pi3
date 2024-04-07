<?php

require __DIR__.'/includes/app.php';

use \App\Http\Router;

// INICIA O ROUTER
$obRouter = new Router(URL);

//INCLUE AS ROTAS DO PAINEL.
include __DIR__.'/routes/user.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()
         ->sendResponse();

/*
   echo '<pre>';
   print_r($);
   echo '</pre>'; exit;


   tipo de conta, perfil user, 
prefiro nao dizer perfil user,
regex, perfil user
mascara, gerenciar serviço/ cadastrar serviço/editar serviço
botão remover do editar trocar para reativar serviço descartado

botão agendar agendar
corrigir tabela Agendar
rotas e controler api 
 */
