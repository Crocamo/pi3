<?php

namespace App\model\Entity;

use \App\Db\Database;

class Client{
    /**
     * ID do cliente
     * @var interger
     */
    public $id_cli;

    /**
     * Nome do cliente
     * @var String
     */
    public $nome;

    /**
     * CPF do cliente
     * @var interger
     */
    public $cpf;

    /**
     * Telefone do cliente
     * @var interger
     */
    public $telefone;

    /**
     * Email do Clente
     * @var String
     */
    public $email;

    /**
     * Endereco do Clente
     * @var String
     */
    public $endereco;

    /**
     * CEP do cliente
     * @var interger
     */
    public $cep;

    /**
     * Cidade do Clente
     * @var String
     */
    public $cidade;

    /**
     * Estado do Clente
     * @var String
     */
    public $estado;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->id_cli = (new Database('tbl_cli'))->insert([
            'nome'      => $this->nome,
            'cpf'       => $this->cpf,
            'telefone'  => $this->telefone,
            'email'     => $this->email,
            'endereco'  => $this->endereco,
            'cep'       => $this->cep,
            'cidade'    => $this->cidade,
            'estado'    => $this->estado
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbl_cli'))->update('id_cli = '.$this->id_cli,[
            'nome'      => $this->nome,
            'cpf'       => $this->cpf,
            'telefone'  => $this->telefone,
            'email'     => $this->email,
            'endereco'  => $this->endereco,
            'cep'       => $this->cep,
            'cidade'    => $this->cidade,
            'estado'    => $this->estado
        ]);
    }

    /**
     * Método responsável por excluir um Serviços do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbl_cli'))->delete('id_cli = '.$this->id_cli);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getClientById($id_cli){
        return self::getClients('id_cli ='.$id_cli)->fetchObject(self::class);
    }


    /**
     * Método responsavel por retornar Serviços
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getClients($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbl_cli'))->select($where,$order,$limit,$fields);
    }
}