<?php

namespace App\Controller\User;

use \App\model\Entity\Budget as EntityBudget;
use \App\model\Entity\BudgetService as EntityBudgetServ;
use \App\model\Entity\Client as EntityClient;
use \App\Utils\Pagination;
// Incluir a biblioteca TCPDF
use TCPDF;

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
                $total += doubleval($obBudgetServ->preco)*doubleval($obBudgetServ->qtd_servico); //TODO falta desconto
                $servicos .= 'sv=' . $obBudgetServ->servico . '/rs=' . $obBudgetServ->preco . '/obs=' . $obBudgetServ->observacao . '/'; //TODO calcular preco x qtd
            }
            $data='';
            if ($ativo==0) {
                $data = $obBudget->data_ini;
            }else {
                $data = $obBudget->data_fim;
            }
 

            $valor_bonus = $obBudget->valor_bonus;
            // Extrair os dois primeiros dígitos especiais
            $digito1 = $valor_bonus[0]; // Primeiro dígito
            $digito2 = $valor_bonus[1]; // Segundo dígito

            // Extrair o restante da string (após os dois dígitos especiais)
            $restante = doubleval(substr($valor_bonus, 2)) / 100;

            $totalF = 0;
           // Realizando operações com base nos dígitos especiais
            if ($digito2 == '%') {
                if ($digito1 == '-') {
                    $totalF = number_format($total - ($total * $restante), 2, '.', '');

                } elseif ($digito1 == '+') {
                    $totalF = number_format($total + ($total * $restante), 2, '.', '');
                }
            } else {
                if ($digito1 == '-') {
                    $totalF = $total-$restante; // subtrai o valor do total
                } elseif ($digito1 == '+') {
                    $totalF = $total+$restante; // adiciona o valor ao total
                }
            }
            

            $telefone_formatado = self::formatar($obBudget->telefone,'tel');
            $cep_formatado = self::formatar($cep,'cep');

            $itens .= View::render('user/modules/home/itens', [
                'total'     => $total,
                'totalF'    => $totalF,
                'final'     => $totalF,
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

    public static function getPrintBudget($request, $id)
    {
        // Obter os dados do orçamento e do cliente
        $obBudget = EntityBudget::getBudgetById($id);

        $obClient = EntityClient::getClientById($obBudget->id_cli);
        $totalGeral=0;
        // Criar uma nova instância do TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
        // Configurar informações do documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Seu Nome');
        $pdf->SetTitle('Detalhes do Orçamento');
        $pdf->SetSubject('Detalhes do Orçamento');
    
        // Adicionar uma página
        $pdf->AddPage();
    
        $pdf->Ln(10); // Espaço em branco entre seções
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 10, 'Orçamento de Prestação de Serviço', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Area do profissional de', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(55, 10, 'Nome do profissional', 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(15, 10, 'CNPJ:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(100, 10, '000000000/0000', 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(20, 10, 'Telefone:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(50, 10, '55 (11) 9 9999-9999', 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(15, 10, 'E-Mail:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(100, 10, 'email@email.com.br', 0, 1, 'L');

        // detalhes do cliente do orçamento
        $pdf->Ln(10); // Espaço em branco entre seções
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Detalhes do Cliente', 0, 1, 'L');
    
        // Tabela para exibir detalhes dos serviços
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(100, 10, 'Nome', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Telefone', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Data de Expedição:', 1, 1, 'C');
     
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(100, 10, $obClient->nome, 1, 0, 'C');
        $pdf->Cell(40, 10, $obBudget->telefone, 1, 0, 'C');
        $pdf->Cell(40, 10, date('d/m/Y', strtotime($obBudget->data_ini)), 1, 1, 'C');
    

        // Tabela para exibir detalhes dos serviços
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(90, 10, 'Endereço:', 1, 0, 'C');
        $pdf->Cell(30, 10, 'CEP:', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Cidade:', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Estado:', 1, 1, 'C');
     
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(90, 10, $obBudget->endereco, 1, 0, 'C');
        $pdf->Cell(30, 10, self::formatar($obBudget->cep, 'cep'), 1, 0, 'C');
        $pdf->Cell(40, 10, $obBudget->cidade, 1, 0, 'C');
        $pdf->Cell(20, 10, $obBudget->estado, 1, 1, 'C');

    
        // Serviços do orçamento
        $pdf->Ln(5); // Espaço em branco entre seções
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Serviços', 0, 1, 'L');
    
        // Tabela para exibir detalhes dos serviços
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(55, 10, 'Serviço', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Tipo', 1, 0, 'C');
        $pdf->Cell(15, 10, 'QTD:', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Preço', 1, 0, 'C');
        $pdf->Cell(65, 10, 'Observação', 1, 1, 'C');
    
        // Obter e exibir os serviços associados ao orçamento
        $budgetResults = EntityBudgetServ::getservices('id_orcamento =' . $id);
        while ($obBudgetServ = $budgetResults->fetchObject(EntityBudgetServ::class)) {
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(55, 10, $obBudgetServ->servico, 1, 0, 'L');
            $pdf->Cell(20, 10, $obBudgetServ->tipo, 1, 0, 'L');
            $pdf->Cell(15, 10, $obBudgetServ->qtd_servico, 1, 0, 'C');
            $pdf->Cell(25, 10, 'R$ ' . number_format($obBudgetServ->preco, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell(65, 10, $obBudgetServ->observacao, 1, 1, 'L');
            // Calcular total para este serviço
            $totalServico = $obBudgetServ->preco * $obBudgetServ->qtd_servico;

            // Adicionar ao total geral
            $totalGeral += $totalServico;
        }
    
        // Rodapé com total geral
        $pdf->Ln(2); // Espaço em branco antes do rodapé
        $pdf->Cell(105, 10, ' ', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Total Serv.:', 1, 0, 'C');
        $pdf->Cell(40, 10, 'R$ ' . number_format($totalGeral, 2, ',', '.'), 1, 1, 'R');

        // Aplicar desconto ou acréscimo ao total do serviço com base no valor_bonus
        $totalFinalServico = self::calcularTotalComBonus($totalGeral, $obBudget->valor_bonus);

        // Rodapé com total geral
        $pdf->Ln(2); // Espaço em branco antes do rodapé
        $pdf->Cell(75, 10, ' ', 1, 0, 'C');
        $pdf->Cell(65, 10, 'Total com Desconto/Acrescimo:', 1, 0, 'C');
        $pdf->Cell(40, 10, 'R$ ' . number_format($totalFinalServico, 2, ',', '.'), 1, 1, 'R');
    
        // Gerar o PDF como download ou visualização
        $pdf->Output('detalhes_orcamento.pdf', 'D');  // 'D' para download, 'I' para visualização inline
    
        // Saída do script
        exit;
    }
        
    /**
     * Função para calcular o total final com desconto/acréscimo com base no valor_bonus
     * @param float $total
     * @param string $valor_bonus
     * @return float
     */
    private static function calcularTotalComBonus($total, $valor_bonus)
    {
        $operador = substr($valor_bonus, 0, 1); // Obter o primeiro caractere (- ou +)
        $tipo = substr($valor_bonus, 1, 1); // Obter o segundo caractere (/ ou %)
        $restante = substr($valor_bonus, 2); // Obter o restante como valor numérico

        if ($operador == '-') {
            if ($tipo == '%') {
                // Desconto percentual
                $totalFinal = floatval($total - ($total * ($restante / 100)));
            } else {
                // Desconto fixo
                $totalFinal = floatval($total - (float) $restante / 100);
            }
        } elseif ($operador == '+') {
            if ($tipo == '%') {
                // Acréscimo percentual
                $totalFinal = floatval($total + ($total * ($restante / 100)));
            } else {
                // Acréscimo fixo
                $totalFinal = floatval($total + (float) $restante / 100);
            }
        } else {
            // Nenhum desconto ou acréscimo
            $totalFinal = floatval($total);
        }

        return $totalFinal;
    }

}
