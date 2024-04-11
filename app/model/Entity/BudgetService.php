<?php

namespace App\model\Entity;

use \App\Db\Database;

class BudgetService{
    /**
     * ID do Serviço em orçamento
     * @var interger
     */
    public $id_serv_orc;

    /**
     * Tipo do Serviço em orçamento
     * @var String
     */
    public $tipo;

    /**
     * Serviço em orçamento
     * @var String
     */
    public $servico;

    /**
     * qtd do serviço em orçamento
     * @var Double
     */
    public $qtd_servico;

    /**
     * Preço por area em orçamento
     * @var String
     */
    public $preco;

    /**
     * ID de seu orçamento
     * @var interger
     */
    public $id_orcamento;

    /**
     * Campo de observação do serviço
     * @var String
     */
    public $observacao;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->id_serv_orc = (new Database('tbl_serv_orc'))->insert([
            'tipo'          => $this->tipo,
            'servico'       => $this->servico,
            'qtd_servico'   => $this->qtd_servico,
            'preco'         => $this->preco,
            'id_orcamento'  => $this->id_orcamento,
            'observacao'    => $this->observacao
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbl_serv_orc'))->update('id_serv_orc = '.$this->id_serv_orc,[
            'tipo'          => $this->tipo,
            'servico'       => $this->servico,
            'qtd_servico'   => $this->qtd_servico,
            'preco'         => $this->preco,
            'id_orcamento'  => $this->id_orcamento,
            'observacao'    => $this->observacao
        ]);
    }

    /**
     * Método responsável por excluir um Serviços do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbl_serv_orc'))->delete('id_serv_orc = '.$this->id_serv_orc);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getServiceById($id_orcamento){
        return self::getservices('id_orcamento ='.$id_orcamento)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getServiceBudgetById($id_serv_orc){
        return self::getservices('id_serv_orc ='.$id_serv_orc)->fetchObject(self::class);
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
        return (new Database('tbl_serv_orc'))->select($where,$order,$limit,$fields);
    }
}