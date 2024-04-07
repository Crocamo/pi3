<?php

namespace App\model\Entity;

use \App\Db\Database;

class Service{
    /**
     * ID do Serviço
     * @var interger
     */
    public $id_servico;

    /**
     * Tipo do Serviço
     * @var String
     */
    public $tipo;

    /**
     * Serviço
     * @var String
     */
    public $servico;

    /**
     * area do serviço
     * @var String
     */
    public $area_servico;

    /**
     * Preço por area
     * @var Float
     */
    public $preco;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->id_servico = (new Database('tbl_serv'))->insert([
            'tipo'          => $this->tipo,
            'servico'       => $this->servico,
            'area_servico'  => $this->area_servico,
            'preco'         => $this->preco
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbl_serv'))->update('id_servico = '.$this->id_servico,[
            'tipo'          => $this->tipo,
            'servico'       => $this->servico,
            'area_servico'  => $this->area_servico,
            'preco'         => $this->preco
        ]);
    }

    /**
     * Método responsável por excluir um Serviços do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbl_serv'))->delete('id_servico = '.$this->id_servico);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getServiceById($id_servico){
        return self::getservices('id_servico ='.$id_servico)->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar Serviços
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getservices($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbl_serv'))->select($where,$order,$limit,$fields);
    }
}