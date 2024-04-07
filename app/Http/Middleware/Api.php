<?php

namespace App\Http\Middleware;

class Api{

    /**
     * Método responsável por executar o middlewares
     * @param Request $request
     * @param Clousure next
     * @return Response
     */
    public function handle($request, $next){
        //ALTERA O CONTENT TYPE PARA JSON
        $request->getRouter()->setContentType('application/json');
        
        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}