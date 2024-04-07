<?php

namespace App\Http\Middleware;

use \App\Session\User\Login as SessionUserLogin;

class RequireUserLogin{

    /**
     * Método responsável por executar o middlewares
     * @param Request $request
     * @param Clousure next
     * @return Response
     */
    public function handle($request, $next){
        //VERIFICA SE O USUÁRIO ESTA LOGADO
        if(!SessionUserLogin::isLogged()){
           $request->getRouter()->redirect('/');
        }
       
        //CONTINUA A EXECUÇÃO
        return $next($request); 
    }
}