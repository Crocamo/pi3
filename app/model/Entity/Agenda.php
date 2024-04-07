<?php

namespace App\model\Entity;

use \App\Db\Database;

class Agenda{

    /**
     * ID da agenda
     * @var interger
     */
    public $idAgenda;

    /**
     * ID do Usuário
     * @var interger
     */
    public $idUser;

    /**
     * ID do serviço
     * @var String
     */
    public $idServico;

    /**
     * ID do Profissional
     * @var String
     */
    public $idProfissional;

    /**
     * ID do Comércio
     * @var interger
     */
    public $idComercio;

    /**
     * horario do serviço agendado
     * @var String
     */
    public $agendaHora;

    /**
     * data do serviço agendado
     * @var String
     */
    public $agendaData;

    /**
     * Status do serviço
     * @var String
     */
    public $status;

    /**
     * Motivo de cancelamento/outros
     * @var String
     */
    public $motivo;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->idAgenda = (new Database('tbagenda'))->insert([
            'idUser'        => $this->idUser,
            'idServico'     => $this->idServico,
            'idProfissional'=> $this->idProfissional,
            'idComercio'    => $this->idComercio,
            'agendaHora'    => $this->agendaHora,
            'agendaData'    => $this->agendaData,
            'status'        => $this->status,
            'motivo'        => $this->motivo
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbagenda'))->update('idAgenda = '.$this->idAgenda,[
            'idUser'        => $this->idUser,
            'idServico'     => $this->idServico,
            'idProfissional'=> $this->idProfissional,
            'idComercio'    => $this->idComercio,
            'agendaHora'    => $this->agendaHora,
            'agendaData'    => $this->agendaData,
            'status'        => $this->status,
            'motivo'        => $this->motivo
        ]);
    }

    /**
     * Método responsável por excluir um agendamento do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbagenda'))->delete('idAgenda = '.$this->idAgenda);
    }

     /**
     * Método responsável por retornar um agendamento com base em seu e-mail
     * @param interger $id
     * @return User
     */
    public static function getAgendaPById($idAgenda){
        return self::getAgendas('idAgenda ='.$idAgenda)->fetchObject(self::class);
    }
 
    
    /**
     * Método responsavel por retornar agendamento
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getAgendas($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbagenda'))->select($where,$order,$limit,$fields);
    }
}