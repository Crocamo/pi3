<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;

class UserBasicAuth{

    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     * @return User
     */
    private function getBasicAuthUser(){
        //VERIFICA A EXISTêNCIA DOS DADOS DE ACESSO
        if(!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW'])){
            return false;
        }

        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser= User::getUserByEmail($_SERVER['PHP_AUTH_USER']);
        
        //VERIFICA A INSTANCIA
        if(!$obUser instanceof User){
            return false;
        }

        //VALIDA A SENHA E RETORNA O USUÁRIO
        return password_verify($_SERVER['PHP_AUTH_PW'],$obUser->senha) ? $obUser : False;
    }

    

    /**
     * Método responsável por validar o acesso via BASIC AUTH
     * @param Request $request
     */
    private function basicAuth($request){
        //VERIFICA O USUÁRIO RECEBIDO
        if($obUser=$this->getBasicAuthUser()){
            $request->user = $obUser;
            return true;
        }
        //EMITE O ERRO DE SENHA INVÁLIDA
        throw new \Exception("Usuário ou senha inválido", 403);
        
    }

    /**
     * Método responsável por executar o middlewares
     * @param Request $request
     * @param Clousure next
     * @return Response
     */
    public function handle($request, $next){
        //REALIZA A VALIDAÇÃO DO ACESSO VIA BASIC AUTH
        $this->basicAuth($request);

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}