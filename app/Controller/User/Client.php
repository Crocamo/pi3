<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\Client as EntityClient;
use \App\Utils\Pagination;

class Client extends Page
{
    private static function formatar($valor, $campo){

        $valor = preg_replace('/[^0-9]/', '', $valor); // Remove todos os caracteres não numéricos
        switch ($campo) {
            case 'cpf':
                return substr($valor, 0, 3) . '.' . substr($valor, 3, 3) . '.' . substr($valor, 6, 3) . '-' . substr($valor, 9, 2);

            case 'cep':
                return substr($valor, 0, 5) . '-' . substr($valor, 5, 3);

            case 'tel':
                if (strlen($valor) <= 8) {
                    return substr($valor, 0, 4) . '-' . substr($valor, 4, 4);
                } elseif (strlen($valor) == 9) {
                    return substr($valor, 0, 1) . ' ' . substr($valor, 1, 4) . '-' . substr($valor, 5, 4);
                }elseif (strlen($valor) == 10) {
                    return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 4) . '-' . substr($valor, 6, 4);
                }elseif (strlen($valor) == 11) {
                    return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 1) . ' ' . substr($valor, 3, 4) . '-' . substr($valor, 7, 4);
                }elseif (strlen($valor) == 12) {
                    return '+' .substr($valor, 0, 2) .' (' . substr($valor, 2, 2) . ') ' .  substr($valor, 4, 4) . '-' . substr($valor, 8, 4);
                }elseif (strlen($valor) >= 13) {
                    return '+' .substr($valor, 0, 2) .'(' . substr($valor, 2, 2) . ') ' . substr($valor, 4, 1) . ' ' . substr($valor, 5, 4) . '-' . substr($valor, 9, 4);
                }
    
                return $valor; // Retorna sem formatação se o comprimento não corresponder aos padrões esperados
            
            default:
                # code...
                break;
        }
    }


    /**
     * Método responsável por obter a renderização dos itens de Clientes para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getClientItens($request, &$obPagination, $where=null)
    {
        //OBJETOS
        $itens = '';

        if (!$where==null) {
            $where='nome="'.$where.'"';
        } 
        
        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = entityClient::getClients($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 3);

        //RESULTADOS DA PÁGINA
        $results = entityClient::getClients($where, 'id_cli DESC', $obPagination->getLimit());
        
        //RENDERIZA O ITEM
        while ($obClient = $results->fetchObject(entityClient::class)) {
   
            // Verificar o comprimento do CEP
            $cep = strval($obClient->cep);
            if (strlen($cep) < 8) {
                // Adicionar zero na frente se o comprimento for menor que 8
                $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
            }

            // Exemplo de uso:
            $telefone_formatado = self::formatar($obClient->telefone,'tel');
            // Exemplo de uso:
            $cep_formatado = self::formatar($cep,'cep');
            $cpf_formatado = self::formatar($obClient->cpf,'cpf');

            $itens .= View::render('user/modules/client/item', [
                'id_cli'   => $obClient->id_cli,
                'nome'     => $obClient->nome, 
                'cpf'      => $cpf_formatado,
                'tel'      => $telefone_formatado, 
                'email'    => $obClient->email, 
                'endereco' => $obClient->endereco, 
                'cep'      => $cep_formatado,
                'cidade'   => $obClient->cidade, 
                'estado'   => $obClient->estado, //TODO criar select
            ]);
        }
        
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de Clientes
     * @return string
     */
    public static function getClient($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('user/modules/client/index', [
            'title'      => 'Área de Administração de Clientes',
            'itens'      => self::getClientItens($request, $obPagination, null),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        // //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Clientes > Univesp', $content, 'client');
    }

    public static function setClient($request){
        //POST VARS
        $postVars = $request->getPostVars();

        $where= $postVars['InputSearch'];
        //CONTEÚDO DA HOME
        
        $content = View::render('user/modules/client/index', [
            'title'      => 'Área de Administração de Clientes',
            'itens'      => self::getClientItens($request, $obPagination, $where),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        // //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Clientes > Univesp', $content, 'client');

    }


    /**
     * Método responsável por retornar o formulário de cadastro de um novo Cliente
     * @param Request $request
     * @return string
     */
    public static function getNewClient($request)
    {

        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/client/form', [
            'title'    => 'Cadastrar Cliente',
            'id_cli'   =>  '',
            'nome'     =>  '', 
            'cpf'      =>  '', 
            'tel'      =>  '', 
            'email'    =>  '', 
            'endereco' =>  '', 
            'cep'      =>  '', 
            'cidade'   =>  '', 
            'estado'   =>  '', //TODO criar select
            'status'   => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar Cliente > Univesp', $content, 'client');
    }

    /**
     * Método responsável por cadastrar um selecao no banco
     * @param Request $request
     * @return string
     */
    public static function setNewClient($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        $cpf = $postVars['cpf'];
        $tel = $postVars['tel'];
        $cep = $postVars['cep'];

        // Remover todos os caracteres não numéricos
        $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
        $tel_limpo = preg_replace('/[^0-9]/', '', $tel);
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep);

        //NOVA INSTANCIA DE DEPOIMENTO
        $obClient            = new EntityClient;
        $obClient->nome      = $postVars['nome']    ?? '';
        $obClient->cpf       = $cpf_limpo     ?? '';
        $obClient->telefone  = $tel_limpo     ?? '';
        $obClient->email     = $postVars['email']   ?? '';
        $obClient->endereco  = $postVars['endereco']?? '';
        $obClient->cep       = $cep_limpo     ?? '';
        $obClient->cidade    = $postVars['cidade']  ?? '';
        $obClient->estado    = $postVars['estado']  ?? '';

        $obClient->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/client?status=created');
    }

    /**
     * Método responsável por retornar o formulário de edição de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getEditClient($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obClient = EntityClient::getClientById($id);

        //VALIDA A INSTANCIA
        if (!$obClient instanceof EntityClient) {
            $request->getRouter()->redirect('/user/client');
        }
        // Verificar o comprimento do CEP
        $cep = strval($obClient->cep);
        if (strlen($cep) < 8) {
            // Adicionar zero na frente se o comprimento for menor que 8
            $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
        }

        // Exemplo de uso:
        $telefone_formatado = self::formatar($obClient->telefone,'tel');
        // Exemplo de uso:
        $cep_formatado = self::formatar($cep,'cep');
        $cpf_formatado = self::formatar($obClient->cpf,'cpf');
        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/client/form', [
            'title'    => 'Editar Cliente',
            'nome'     =>  $obClient->nome      ?? '',
            'cpf'      =>  $cpf_formatado       ?? '',
            'tel'      =>  $telefone_formatado  ?? '',
            'email'    =>  $obClient->email     ?? '',
            'endereco' =>  $obClient->endereco  ?? '',
            'cep'      =>  $cep_formatado       ?? '',
            'cidade'   =>  $obClient->cidade    ?? '',
            'estado'   =>  $obClient->estado    ?? '',//TODO criar select

            'status'   => self::getStatus($request)
        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Atualizar Cliente > Univesp', $content, 'Client');
    }

    /**
     * Método responsável por gravar a atualização de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setEditClient($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obClient = EntityClient::getClientById($id);

        //VALIDA A INSTANCIA
        if (!$obClient instanceof EntityClient) {
            $request->getRouter()->redirect('/user/client');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $cpf = $postVars['cpf'];
        $tel = $postVars['tel'];
        $cep = $postVars['cep'];

        // Remover todos os caracteres não numéricos
        $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
        $tel_limpo = preg_replace('/[^0-9]/', '', $tel);
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep);

        //Atualiza INSTANCIA 
        $obClient->nome      = $postVars['nome']    ?? $obClient->nome;
        $obClient->cpf       = $cpf_limpo     ?? $obClient->cpf;
        $obClient->telefone  = $tel_limpo     ?? $obClient->telefone;
        $obClient->email     = $postVars['email']   ?? $obClient->email;
        $obClient->endereco  = $postVars['endereco']?? $obClient->endereco;
        $obClient->cep       = $cep_limpo     ?? $obClient->cep;
        $obClient->cidade    = $postVars['cidade']  ?? $obClient->cidade;
        $obClient->estado    = $postVars['estado']  ?? $obClient->estado;
        $obClient->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/client?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getDeleteClient($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obClient = EntityClient::getClientById($id);

        //VALIDA A INSTANCIA
        if (!$obClient instanceof EntityClient) {
            $request->getRouter()->redirect('/user/client');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/client/delete', [
            'nome'      => $obClient->nome
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir Cliente > Univesp', $content, 'client');
    }

    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setDeleteClient($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obClient = EntityClient::getClientById($id);

        //VALIDA A INSTANCIA
        if (!$obClient instanceof EntityClient) {
            $request->getRouter()->redirect('/user/client');
        }

        //EXCLUI O DEPOIMENTO
        $obClient->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/client?status=deleted');
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
                return Alert::getError('Cliente Excluido!');
                break;
            case 'created':
                return Alert::getSuccess('Cliente Criado');
                break;
            case 'updated':
                return Alert::getSuccess('Cliente Atualizado');
                break;
        }
    }
}
