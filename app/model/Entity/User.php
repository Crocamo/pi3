<?php

namespace App\model\Entity;

use \App\Db\Database;

class User{

    /**
     * ID do usuário
     * @var interger
     */
    public $id;

    /**
     * Contato do usuário
     * @var interger
     */
    public $id_cli;

    /**
     * Login do usuário
     * @var String
     */
    public $login;

    /**
     * Senha do usuário
     * @var String
     */
    public $senha;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('tbl_user'))->insert([
            'id_cli'    => $this->id_cli,
            'login'     => $this->login,            
            'senha'     => $this->senha
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbl_user'))->update('id = '.$this->id,[
            'id_cli'      => $this->id_cli,
            'login'     => $this->login,            
            'senha'     => $this->senha
        ]);
    }

    /**
     * Método responsável por excluir um usuario do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbl_user'))->delete('id = '.$this->id);
    }

     /**
     * Método responsável por retornar um usuário com base em seu e-mail
     * @param interger $id
     * @return User
     */
    public static function getUserById($id){
        return self::getUsers('id ='.$id)->fetchObject(self::class);
    }

     /**
     * Método responsável por retornar um usuário com base em seu Login
     * @param string $login
     * @return User
     */
    public static function getUserByLogin($login){
        return self::getUsers('login ="'.$login.'"')->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar usuários
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbl_user'))->select($where,$order,$limit,$fields);
    }
}