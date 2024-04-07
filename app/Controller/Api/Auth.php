<?php

namespace App\Controller\Api;

use \App\model\Entity\User;
use \Firebase\JWT\JWT;

class Auth extends Api{

    /**
     * Método responsável por gerar um token JWT
     * @param Request $request
     * @return array
     */
    public static function generateToken($request){
        //POST VARS
        $postVars = $request->getPostVars();

        if (!isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new \Exception("Os campos 'email e 'senha' são obrigatorios", 400);
        }

        //BUSCA O USUÁRIO PELO EMAIL
        $obUser= User::getUserByEmail($postVars['email']);
        if (!$obUser instanceof User) {
            throw new \Exception("O usuário ou senha são inválidos", 400);
        }

        //VALIDA A SENHA DO USUÁRIO
        if (!password_verify($postVars['senha'],$obUser->senha)) {
            throw new \Exception("O usuário ou senha são inválidos", 400);
        }

        //PLAYLOAD
        $payload= [
            'email' => $obUser->email
        ];

        return [
            'token' => JWT::encode($payload,getenv('JWT_KEY'),'HS256')
        ];
    }

}