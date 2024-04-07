<?php

namespace App\Controller\Api;

use \App\model\Entity\User;
use \App\model\Entity\Profissional as Prof;
use \App\model\Entity\Horarios;
use \App\Utils\Pagination;

class Profissional extends Api{

    
    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItens($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            $itens[] = [
                'id'        => (int)$obTestimony->id,
                'nome'      => $obTestimony->nome,
                'mensagem'  => $obTestimony->mensagem,
                'data'      => $obTestimony->data
            ];
        }

        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por retornar os depoimentos cadastrados
     * @param Request $request
     * @return
     */
    public static function getTestimonies($request)
    {
        return [
            'depoimentos'   => self::getTestimonyItens($request, $obPagination),
            'paginacao'     => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     */
    public static function getProfissional($request, $id)
    {
        //VALIDA O ID DO DEPOIMENTO
        if (!is_numeric($id)) {
            throw new \Exception("o id '" . $id . "' não é valido", 400);
        }

        //BUSCA DEPOIMENTO
        $obProf = Prof::getProfissionalById($id);

        //VALIDA SE O DEPOIMENTO EXISTE
        if (!$obProf instanceof Prof) {
            throw new \Exception("O depoimento " . $id . " não foi encontrato", 404);
        }

        //RETORNA OS DETALHES DO DEPOIMENTO
        return [
            'id'        => (int)$obTestimony->id,
            'nome'      => $obTestimony->nome,
            'mensagem'  => $obTestimony->mensagem,
            'data'      => $obTestimony->data
        ];
    }

    /**
     * Método responsável por cadastrar um novo depoimento
     * @param Request $request
     */
    public static function setNewTestimony($request)
    {

        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) or !isset($postVars['mensagem'])) {
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
        }

        //NOVO DEPOIMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();

        //RETORNA OS DETALHES DO DEPOIMENTO CADASTRADO
        return [
            'id'        => (int)$obTestimony->id,
            'nome'      => $obTestimony->nome,
            'mensagem'  => $obTestimony->mensagem,
            'data'      => $obTestimony->data
        ];
    }

    /**
     * Método responsável por atualizar um depoimento
     * @param Request $request
     */
    public static function setEditTestimony($request, $id)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) or !isset($postVars['mensagem'])) {
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
        }

        //BUSCA O DEPOIMENTO NO BANCO
        $obTestimony = EntityTestimony::getTestimoniesById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento " . $id . " não foi encontrato", 404);
        }

        //ATUALIZA O DEPOIMENTO
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->atualizar();

        //RETORNA OS DETALHES DO DEPOIMENTO ATUALIZAR
        return [
            'id'        => (int)$obTestimony->id,
            'nome'      => $obTestimony->nome,
            'mensagem'  => $obTestimony->mensagem,
            'data'      => $obTestimony->data
        ];
    }

    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     */
    public static function setDeleteTestimony($request, $id)
    {
        //BUSCA O DEPOIMENTO NO BANCO
        $obTestimony = EntityTestimony::getTestimoniesById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento " . $id . " não foi encontrato", 404);
        }

        //EXCLUI O DEPOIMENTO
        $obTestimony->excluir();

        //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso' => true
        ];
    }
}
