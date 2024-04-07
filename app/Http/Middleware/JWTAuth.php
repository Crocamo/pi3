<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{

    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     * @return User
     */
    private function getJWTAuthUser($request)
    {
        //HEADERS
        $headers = $request->getHeaders();
       
        //TOKEN PURO EM JWT 
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        //CHAVE PUBLICA JWT
        $key = getenv('JWT_KEY');

        try {
            //DECODE
            $decode = (array)JWT::decode($jwt, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception("Token inválido", 403);
        }

        //EMAIL
        $email = $decode['email'] ?? '';

        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($email);
        
        //RETORNA O USUÁRIO
        return $obUser instanceof User ? $obUser : false;
    }

    /**
     * Método responsável por validar o acesso via JWT
     * @param Request $request
     */
    private function Auth($request)
    {
        //VERIFICA O USUÁRIO RECEBIDO
        if ($obUser = $this->getJWTAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }
        //EMITE O ERRO DE SENHA INVÁLIDA
        throw new \Exception("Acesso negado", 403);
    }

    /**
     * Método responsável por executar o middlewares
     * @param Request $request
     * @param Clousure next
     * @return Response
     */
    public function handle($request, $next)
    {
        //REALIZA A VALIDAÇÃO DO ACESSO JWT
        $this->Auth($request);

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}
