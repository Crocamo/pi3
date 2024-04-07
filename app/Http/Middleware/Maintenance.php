<?php

namespace App\Http\Middleware;

class Maintenance{

    /**
     * Método responsável por executar o middlewares
     * @param Request $request
     * @param Clousure next
     * @return Response
     */
    public function handle($request, $next){
       if(getenv('MAINTENANCE')== 'true'){
        throw new \Exception("Página em manutenção, Tente novamente mais tarde", 200);
       }
        
       //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}