<?php

namespace App\Controller\User;

use \App\model\Entity\Budget as EntityBudget;
use \App\model\Entity\BudgetService as EntityBudgetServ;
use \App\model\Entity\Client as EntityClient;
use \App\Utils\Pagination;

use \App\Utils\View;

class Home extends Page
{

    private static function formatar($valor, $campo){

        $valor = preg_replace('/[^0-9]/', '', $valor); // Remove todos os caracteres não numéricos
        switch ($campo) {
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
     * Método responsável por obter a renderização dos itens de "orçamento aberto" para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItens($request, $ativo, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityBudget::getBudgets('ativo = ' . $ativo, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityBudget::getBudgets('ativo = ' . $ativo, 'id_orcamento DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obBudget = $results->fetchObject(EntityBudget::class)) {
            $total = 0;
            $servicos = '';
            $nome = '';

            if ($obBudget->nome_cli == '') {
                $cli = EntityClient::getClientById($obBudget->id_cli);
                $nome = $cli->nome;
            } else {
                $nome = $obBudget->nome_cli;
            }
            // Verificar o comprimento do CEP
            $cep = strval($obBudget->cep);
            if (strlen($cep) < 8) {
                // Adicionar zero na frente se o comprimento for menor que 8
                $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
            }
            
            switch ($obBudget->ativo) {
                case '0':
                    $status='Aberto';
                    break;

                case '1':
                    $status='Cancelado';
                    break;

                case '2':
                    $status='Concluido';
                    break;
                default:
                    # code...
                    break;
            }

            $results1 = EntityBudgetServ::getservices('id_orcamento = ' . $obBudget->id_orcamento, 'id_orcamento DESC', $obPagination->getLimit());

            while ($obBudgetServ = $results1->fetchObject(EntityBudgetServ::class)) {
                $total += intval($obBudgetServ->preco)*intval($obBudgetServ->qtd_servico); //TODO falta desconto
                $servicos .= 'sv=' . $obBudgetServ->servico . '/rs=' . $obBudgetServ->preco . '/obs=' . $obBudgetServ->observacao . '/'; //TODO calcular preco x qtd
            }
            $data='';
            if ($ativo==0) {
                $data=$obBudget->data_ini;
            }else {
                $data=$obBudget->data_fim;
            }
            $telefone_formatado = self::formatar($obBudget->telefone,'tel');
            $cep_formatado = self::formatar($cep,'cep');
            $itens .= View::render('user/modules/home/itens', [
                'total'     => $total,
                'nomeCli'   => $nome,
                'data_ini'  => date('d/m/Y', strtotime($data)),
                'id_orcamento'=> $obBudget->id_orcamento,
                'telefone'  => $telefone_formatado,
                'endereco'  => $obBudget->endereco,
                'cep'       => $cep_formatado,
                'cidade'    => $obBudget->cidade,
                'estado'    => $obBudget->estado,
                'status'    => $status,
                'servicos'  => $servicos,
            ]);
        }
        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getHome($request)
    {

        //CONTEÚDO DA HOME
        $content = View::render('user/modules/home/index', [
            'budgetOpen' => self::getItens($request, 0, $obPaginationOpen),
            'budgetOpenPagination' => parent::getPagination($request, $obPaginationOpen),
            'budgetClose' => self::getItens($request, 2, $obPaginationClose),
            'budgetClosePagination' => parent::getPagination($request, $obPaginationClose)
        ]);
        //exit;
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'home');
    }
}
