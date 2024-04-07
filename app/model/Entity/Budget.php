<?php

namespace App\model\Entity;

use \App\Db\Database;

class Budget{
    /**
     * ID do orçamento
     * @var interger
     */
    public $id_orcamento;

    /**
     * ID do cliente
     * @var interger
     */
    public $id_cli;

    /**
     * Endereco do orçamento
     * @var String
     */
    public $nome_cli;

    /**
     * Ativo = 0
     * Cancelado = 1
     * concluido = 2
     * @var tiniint
     */
    public $ativo;

    /**
     * CPF do cliente
     * @var Double
     */
    public $valor_bonus;

    /**
     * Data de criação do orçamento
     * @var Date
     */
    public $data_ini;

    /**
     * Data de finalização do orçamento
     * @var Date
     */
    public $data_fim;

    /**
     * Endereco do orçamento
     * @var String
     */
    public $endereco;

    /**
     * CEP do orçamento
     * @var interger
     */
    public $cep;

    /**
     * Cidade do orçamento
     * @var String
     */
    public $cidade;

    /**
     * Estado do orçamento
     * @var String
     */
    public $estado;

    /**
     * telefone do orçamento
     * @var interger
     */
    public $telefone;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        $this->data_ini = date('Y-m-d H:i:s');
        //INSERE A INSTANCIA NO BANCO
        $this->id_orcamento = (new Database('tbl_orcamento'))->insert([
            'id_cli'      => $this->id_cli,
            'nome_cli'    => $this->nome_cli,
            'ativo'       => $this->ativo,
            'valor_bonus' => $this->valor_bonus,
            'data_ini'    => $this->data_ini,
            'data_fim'    => $this->data_fim,
            'endereco'    => $this->endereco,
            'telefone'    => $this->telefone,
            'cep'         => $this->cep,
            'cidade'      => $this->cidade,
            'estado'      => $this->estado
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbl_orcamento'))->update('id_orcamento = '.$this->id_orcamento,[
            'id_cli'      => $this->id_cli,
            'nome_cli'    => $this->nome_cli,
            'ativo'       => $this->ativo,
            'valor_bonus' => $this->valor_bonus,
            'data_ini'    => $this->data_ini,
            'data_fim'    => $this->data_fim,
            'endereco'    => $this->endereco,
            'telefone'    => $this->telefone,
            'cep'         => $this->cep,
            'cidade'      => $this->cidade,
            'estado'      => $this->estado
        ]);
    }

    /**
     * Método responsável por excluir um Serviços do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbl_orcamento'))->delete('id_orcamento = '.$this->id_orcamento);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getBudgetById($id_orcamento){
        return self::getBudgets('id_orcamento ='.$id_orcamento)->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar Serviços
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getBudgets($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbl_orcamento'))->select($where,$order,$limit,$fields);
    }
}