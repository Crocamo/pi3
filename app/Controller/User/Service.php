<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\Service as entityService;
use \App\Utils\Pagination;


class Service extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getServiceItens($request, &$obPagination)
    {
        //OBJETOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = entityService::getServices(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 3);

        //RESULTADOS DA PÁGINA
        $results = entityService::getServices(null, 'id_servico DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obServico = $results->fetchObject(entityService::class)) {
            
            $itens .= View::render('user/modules/service/item', [
                'id_servico'    => $obServico->id_servico,
                'tipo'          => $obServico->tipo, //ATENÇÃO CRIAR FUNÇÃO
                'servico'       => $obServico->servico, //ATENÇÃO CRIAR FUNÇÃO
                'area_servico'  => $obServico->area_servico,//ATENÇÃO CRIAR FUNÇÃO
                'preco'         => $obServico->preco,//ATENÇÃO CRIAR FUNÇÃO
            ]); //**ATENÇÃO CRIAR CONTROLE BOTÃO EDITAR */
        }
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de serviços
     * @return string
     */
    public static function getService($request)
    {
        //CONTEÚDO DA PÁGINA DE NOVO SERVIÇO
        $content = View::render('user/modules/service/index', [
            'title'      => 'Área de Administração de Serviço',
            'itens'      => self::getServiceItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        // //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Serviços > Univesp', $content, 'service');
    }
    

    /**
     * Método responsável por retornar o formulário de cadastro de um novo Serviços
     * @param Request $request
     * @return string
     */
    public static function getNewService($request)
    {

        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/service/form', [
            'title'         => 'Cadastrar Serviço',
            'tipo'          => '', //ATENÇÃO CRIAR FUNÇÃO
            'servico'       => '', //ATENÇÃO CRIAR FUNÇÃO
            'area_servico'  => '',//ATENÇÃO CRIAR FUNÇÃO
            'preco'         => '',//ATENÇÃO CRIAR FUNÇÃO
            'status'        => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar Serviço > Univesp', $content, 'service');
    }


    /**
     * Método responsável por cadastrar um selecao no banco
     * @param Request $request
     * @return string
     */
    public static function setNewService($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //NOVA INSTANCIA DE DEPOIMENTO
        $obServ                 = new entityService;
        $obServ->tipo           = $postVars['tipo'];
        $obServ->servico        = $postVars['servico'];
        $obServ->area_servico   = $postVars['area_servico'];
        $obServ->preco          = strval($postVars['preco']);

        $obServ->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/service?status=created');
    }


    /**
     * Método responsável por retornar o formulário de edição de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getEditService($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obServ = EntityService::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof EntityService) {
            $request->getRouter()->redirect('/user/service');
        }
        
        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/service/form', [
            'title'         => 'Editar Serviço',
            'tipo'          => $obServ->tipo        ?? '', //ATENÇÃO CRIAR FUNÇÃO
            'servico'       => $obServ->servico     ?? '', //ATENÇÃO CRIAR FUNÇÃO
            'area_servico'  => $obServ->area_servico?? '',//ATENÇÃO CRIAR FUNÇÃO
            'preco'         => $obServ->preco       ?? '',//ATENÇÃO CRIAR FUNÇÃO
            'status'        => self::getStatus($request)

        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar Serviço > Univesp', $content, 'service');
    }

    /**
     * Método responsável por gravar a atualização de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setEditService($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obServ = EntityService::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof EntityService) {
            $request->getRouter()->redirect('/user/service');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //Atualiza INSTANCIA 
        $obServ->tipo           = $postVars['tipo']         ?? $obServ->tipo;
        $obServ->servico        = $postVars['servico']      ?? $obServ->servico;
        $obServ->area_servico   = $postVars['area_servico'] ?? $obServ->area_servico;
        $obServ->preco          = $postVars['preco']        ?? $obServ->preco;

        $obServ->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/service?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getDeleteService($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obService = entityService::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obService instanceof entityService) {
            $request->getRouter()->redirect('/user/service');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/service/delete', [
            'servico'=> $obService->servico,
            'preco'  => $obService->preco
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir Serviço > Univesp', $content, 'service');
    }
    
    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setDeleteService($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obService = EntityService::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obService instanceof EntityService) {
            $request->getRouter()->redirect('/user/Service');
        }

        //EXCLUI O DEPOIMENTO
        $obService->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/service?status=deleted');
    }

     /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //  QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if (!isset($queryParams['status'])) return '';

        //MENSAGEM DE STATUS
        switch ($queryParams['status']) {
            case 'deleted':
                return Alert::getError('Serviço Excluido!');
                break;
            case 'created':
                return Alert::getSuccess('Serviço Criado');
                break;
            case 'updated':
                return Alert::getSuccess('Serviço Atualizado');
                break;
            case 'updated':
                return Alert::getSuccess('Serviço Atualizado');
                break;
        }
    }
}
