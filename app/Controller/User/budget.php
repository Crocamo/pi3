<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\Client as EntityClient;
use \App\model\Entity\service as EntityService;
use \App\model\Entity\Budget as EntityBudget;
use \App\model\Entity\BudgetService as EntityBudgetServ;


class Budget extends Page
{

    private static function formatar($valor, $campo)
    {

        $valor = preg_replace('/[^0-9]/', '', $valor); // Remove todos os caracteres não numéricos
        switch ($campo) {
            case 'cep':
                return substr($valor, 0, 5) . '-' . substr($valor, 5, 3);

            case 'tel':
                if (strlen($valor) <= 8) {
                    return substr($valor, 0, 4) . '-' . substr($valor, 4, 4);
                } elseif (strlen($valor) == 9) {
                    return substr($valor, 0, 1) . ' ' . substr($valor, 1, 4) . '-' . substr($valor, 5, 4);
                } elseif (strlen($valor) == 10) {
                    return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 4) . '-' . substr($valor, 6, 4);
                } elseif (strlen($valor) == 11) {
                    return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 1) . ' ' . substr($valor, 3, 4) . '-' . substr($valor, 7, 4);
                } elseif (strlen($valor) == 12) {
                    return '+' . substr($valor, 0, 2) . ' (' . substr($valor, 2, 2) . ') ' .  substr($valor, 4, 4) . '-' . substr($valor, 8, 4);
                } elseif (strlen($valor) >= 13) {
                    return '+' . substr($valor, 0, 2) . '(' . substr($valor, 2, 2) . ') ' . substr($valor, 4, 1) . ' ' . substr($valor, 5, 4) . '-' . substr($valor, 9, 4);
                }

                return $valor; // Retorna sem formatação se o comprimento não corresponder aos padrões esperados

            default:
                # code...
                break;
        }
    }

    /**
     * Método responsável por retornar a renderização do select
     * @param String $selecao
     * @return string
     */
    private static function getSelect($obj)
    {
        switch ($obj) {

            case 'Client':
                // OBTÉM OS CLIENTES DO BANCO DE DADOS
                $clientResults = entityClient::getClients(null, 'nome ASC');
                $options = '<option value="" selected>Selecione um cliente</option>';
                //RENDERIZA O ITEM
                while ($obClient = $clientResults->fetchObject(entityClient::class)) {

                    // Verificar o comprimento do CEP
                    $cep = strval($obClient->cep);

                    if (strlen($cep) < 8) {
                        // Adicionar zero na frente se o comprimento for menor que 8
                        $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
                    }

                    $telefone_formatado = self::formatar($obClient->telefone, 'tel');
                    $cep_formatado = self::formatar($cep, 'cep');

                    $obValue = $obClient->id_cli . '//' . $obClient->nome . '//' . $obClient->endereco . '//' . $obClient->estado . '//' . $obClient->cidade . '//' . $cep_formatado . '//' . $telefone_formatado;
                    $options .= View::render('user/modules/budget/option', [
                        'value'     => $obValue,
                        'label'     => $obClient->nome
                    ]);
                }
                return $options;

            case 'Service':
                // OBTÉM OS SERVICOS DO BANCO DE DADOS
                $serviceResults = EntityService::getservices(null, 'servico ASC');
                $options = '<option value="" selected>Selecione um Serviço</option> ';
                //RENDERIZA O ITEM
                while ($obService = $serviceResults->fetchObject(entityService::class)) {
                    $obValue = $obService->id_servico . '//' . $obService->servico . '//' . $obService->tipo . '//' . $obService->area_servico . '//' . $obService->preco;
                    $options .= View::render('user/modules/budget/option', [
                        'value'     => $obValue,
                        'label'     => $obService->servico
                    ]);
                }
                return $options;
            case 'ServiceAdd':
                // OBTÉM OS SERVICOS DO BANCO DE DADOS
                $serviceResults = EntityService::getservices(null, 'servico ASC');
                $options = '<option value="0" selected>Selecione um Serviço</option> ';
                //RENDERIZA O ITEM
                while ($obService = $serviceResults->fetchObject(entityService::class)) {
                    $options .= View::render('user/modules/budget/option', [
                        'value'     => $obService->id_servico,
                        'label'     => $obService->servico
                    ]);
                }
                return $options;
    
            default:
                # code...
                break;
        }
    }

    /**
     * Método responsável por obter a renderização dos itens para a página de orçamento
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getBudgetItens()
    {
        //RENDERIZA O ITEM
        $itens = View::render('user/modules/budget/item', [
            'optionsC' => self::getSelect('Client'),
            'optionsS' => self::getSelect('Service')
        ]);
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de orçamentos
     * @return string
     */
    public static function getBudget($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('user/modules/budget/index', [
            'title'      => 'Orçamento',
            'itens'      => self::getBudgetItens(),
            'status'     => self::getStatus($request),
        ]);

        // //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Orçamentos > Univesp', $content, 'budget');
    }

    /**
     * Método responsável por quebrar o text enviado e  retorna o primeiro valor antes do separador //
     * @return id
     */
    static function removeIdClientePrefix($idCliente)
    {
        $parts = explode('//', $idCliente);
        return $parts[0];
    }

    /**
     * Método responsável por cadastrar um selecao no banco
     * @param Request $request
     * @return string
     */
    public static function setBudget($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $tel = $postVars['telefone'];
        $cep = $postVars['cep'];
        $valor = '';
        if (isset($postVars['desconto'])) {
            $valor = '-';
        } else {
            $valor = '+';
        }

        if (isset($postVars['porcentagem'])) {
            $valor .= '%';
        } else {
            $valor .= '/';
        }
        $t = $postVars['desconto-acrescimo'];
        $val = preg_replace('/[^0-9]/', '', $t);
        $valor .= $val;

        // Remover todos os caracteres não numéricos
        $tel_limpo = preg_replace('/[^0-9]/', '', $tel);
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep);

        $idcliente = $postVars['selectCliente'];
        $idCliSemPrefixo = self::removeIdClientePrefix($idcliente);

        //NOVA INSTANCIA DE DEPOIMENTO
        $obBludgetCli               = new EntityBudget;
        $obBludgetCli->id_cli       = $idCliSemPrefixo;
        $obBludgetCli->nome_cli     = $postVars['nome_cli']     ?? '';
        $obBludgetCli->ativo        = 0;
        $obBludgetCli->valor_bonus  = $valor     ?? 0;
        $obBludgetCli->data_fim     = 0;
        $obBludgetCli->endereco     = $postVars['endereco']     ?? '';
        $obBludgetCli->cep          = $cep_limpo  ?? '';
        $obBludgetCli->cidade       = $postVars['cidade']  ?? '';
        $obBludgetCli->estado       = $postVars['estado']  ?? '';
        $obBludgetCli->telefone     = $tel_limpo  ?? '';

        //VALIDA A INSTANCIA
        if (!$obBludgetCli->endereco || !$obBludgetCli->telefone) {
            $request->getRouter()->redirect('/user/budget');
        }

        $obBludgetCli->cadastrar();

        // Inicializa um array para armazenar os dados dinâmicos
        $dadosDinamicos = array();

        // Itera sobre as chaves do array $_POST
        foreach ($postVars as $chave => $valor) {
            // Verifica se a chave começa com o prefixo desejado, por exemplo, 'tipo'
            if (strpos($chave, 'tipo') === 0) {
                // Extrai o índice do tipo (0, 1, 2, ...)
                $indice = substr($chave, 4); // 'tipo' tem 4 caracteres
                // Armazena o valor na posição correspondente do array $dadosDinamicos
                $dadosDinamicos[$indice]['tipo'] = $valor;
            } elseif (strpos($chave, 'servico') === 0) {
                // Repita o mesmo para 'servico'
                $indice = substr($chave, 7); // 'servico' tem 7 caracteres
                $dadosDinamicos[$indice]['servico'] = $valor;
            } elseif (strpos($chave, 'qtd_servico') === 0) {
                if (!$valor) {
                    $valor = 1;
                }
                // Repita o mesmo para 'qtd_servico'
                $indice = substr($chave, 11); // 'qtd_servico' tem 11 caracteres
                $dadosDinamicos[$indice]['qtd_servico'] = $valor;
            } elseif (strpos($chave, 'obs') === 0) {
                $indice = substr($chave, 3); // 'obs' tem 3 caracteres
                $dadosDinamicos[$indice]['obs'] = $valor;
            } elseif (strpos($chave, 'preco') === 0) {
                // Repita o mesmo para 'qtd_servico'
                $indice = substr($chave, 5); // 'preco' tem 5 caracteres
                $parts = explode('R$ ', $valor);
                $value = $parts[1];
                $dadosDinamicos[$indice]['preco'] = $value;
            }
        }

        for ($i = 0; $i < count($dadosDinamicos); $i++) {
            //NOVA INSTANCIA DE DEPOIMENTO

            $obBludgetServ              = new EntityBudgetServ;
            $obBludgetServ->tipo        = $dadosDinamicos[$i]['tipo'];
            $obBludgetServ->servico     = $dadosDinamicos[$i]['servico'];
            $obBludgetServ->qtd_servico = $dadosDinamicos[$i]['qtd_servico'] ?? 1;
            $obBludgetServ->preco       = $dadosDinamicos[$i]['preco'];
            $obBludgetServ->observacao  = $dadosDinamicos[$i]['obs'];
            $obBludgetServ->id_orcamento = $obBludgetCli->id_orcamento;

            $obBludgetServ->cadastrar();
        }
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/budget?status=created');
    }


    public static function services($id)
    {
        $content = '';
        $count = 0;
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $budgetResults = EntityBudgetServ::getservices('id_orcamento =' . $id);
        while ($obBudgetServ = $budgetResults->fetchObject(EntityBudgetServ::class)) {
            //VALIDA A INSTANCIA
            if ($obBudgetServ instanceof EntityBudgetServ) {
            $preco = $obBudgetServ->preco;
            // Convertendo o número em string
            $precoString = strval($preco);

            // Verificando se $preco é um número inteiro
            if (preg_match('/^\d+$/', $precoString)) {
                // Se for um número inteiro, adiciona '.00' ao final
                $preco = $precoString . '.00';
            }   

                //CONTEÚDO DO FORMULÁRIO 
                $content .= View::render('user/modules/budget/formServ', [
                    'idBludServ'=> $obBudgetServ->id_serv_orc,
                    'servico'   => $obBudgetServ->servico,
                    'tipo'      => $obBudgetServ->tipo,
                    'preco'     => $preco,
                    'observacao'=> $obBudgetServ->observacao    ?? '',
                    'qtd_serv'  => $obBudgetServ->qtd_servico,
                    'count'     => $count
                ]);
            }
            $count += 1;
        }
        //RETORNA A PÁGINA COMPLETA
        return  $content;
    }


    /**
     * Método responsável por retornar o formulário de edição de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getEditBudget($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obBudget = EntityBudget::getBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBudget instanceof EntityBudget) {
            $request->getRouter()->redirect('/user');
        }

        // Verificar o comprimento do CEP
        $cep = strval($obBudget->cep);
        if (strlen($cep) < 8) {
            // Adicionar zero na frente se o comprimento for menor que 8
            $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
        }

        $telefone_formatado = self::formatar($obBudget->telefone, 'tel');
        $cep_formatado = self::formatar($cep, 'cep');
        $ativo = '';
        switch ($obBudget->ativo) {
            case 0:
                $ativo = 'Ativo';
                break;
            case '1':
                $ativo = 'Cancelado';
                break;
            default:
                $ativo = 'Concluido';
                break;
        }
 
        $valor_bonus = $obBudget->valor_bonus;
        // Extrair os dois primeiros dígitos especiais
        $digito1 = $valor_bonus[0]; // Primeiro dígito
        $digito2 = $valor_bonus[1]; // Segundo dígito

        // Extrair o restante da string (após os dois dígitos especiais)
        $restante = doubleval(substr($valor_bonus, 2));
        
        $checkedPercent = '';
        $checked = '';
        
        if ($digito1 == '-') {
            $checked = 'checked';
        }

        if ($digito2 == '%') {
            $checkedPercent = 'checked';
        }

        if ($digito2 == '/') {
            // Convertendo o número em string
            $desconto = strval($restante);

            // Verificando se $preco é um número inteiro
            if (preg_match('/^\d+$/', $desconto)) {
                // Se for um número inteiro, adiciona '.00' ao final
                $restante = $desconto . '.00';
            }   
            $restante = number_format(($restante/100), 2, '.', ',');
        }

        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/budget/form', [
            'title'      => 'Editar Orçamento',
            'id_budget'  =>  $obBudget->id_orcamento,
            'nome_cli'   =>  $obBudget->nome_cli    ?? '',
            'ativo'      =>  $ativo, 
            'valor_bonus'=>  $restante              ?? '',
            'endereco'   =>  $obBudget->endereco    ?? '',
            'cep'        =>  $cep_formatado         ?? '',
            'cidade'     =>  $obBudget->cidade      ?? '',
            'estado'     =>  $obBudget->estado      ?? '', //TODO criar select
            'telefone'   =>  $telefone_formatado    ?? '',
            'servicos'   =>  self::services($obBudget->id_orcamento),
            'checked'    =>  $checked,
            'checkedPercent' => $checkedPercent,
            'optionS' => self::getSelect('ServiceAdd'),
            

            'status'   => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Editar Orçamento > Univesp', $content, 'budget');
    }

    /**
     * Método responsável por gravar a atualização de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setEditBudget($request, $id)
    {
        // OBTÉM O ORÇAMENTO DO BANCO DE DADOS
        $obBudget = EntityBudget::getBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBudget instanceof EntityBudget) {
            $request->getRouter()->redirect('/user/budget/' . $id . '/edit');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        // Remover todos os caracteres não numéricos
        $tel_limpo = preg_replace('/[^0-9]/', '', $postVars['telefone']);
        $cep_limpo = preg_replace('/[^0-9]/', '', $postVars['cep']);

        // Obtenha os valores dos campos do formulário
        $desconto = isset($postVars['desconto']) && $postVars['desconto'] == 'on';
        $porcentagem = isset($postVars['porcentagem']) && $postVars['porcentagem'] == 'on';
        $valor_bonus = preg_replace('/[^0-9]/', '', $postVars['valor_bonus']);

        // Construa a string $valorBonus com base nas condições
        $valorBonus = ($desconto ? '-' : '+') . ($porcentagem ? '%' : '/') . $valor_bonus;

        $ativo = '';
        switch ($postVars['ativo']) {
            case 'Ativo':
                $ativo = 0;
                break;
            case 'Cancelado':
                $ativo = 1;
                break;
            default:
                $ativo = 2;
                break;
        }
        //Atualiza INSTANCIA 
        $obBudget->nome_cli     = $postVars['nome'] ?? $obBudget->nome_cli;
        $obBudget->ativo        = $ativo ?? $obBudget->ativo;
        $obBudget->valor_bonus  = $valorBonus ?? $obBudget->valor_bonus;
        $obBudget->telefone     = $tel_limpo ?? $obBudget->telefone;
        $obBudget->endereco     = $postVars['endereco'] ?? $obBudget->endereco;
        $obBudget->cep          = $cep_limpo ?? $obBudget->cep;
        $obBudget->cidade       = $postVars['cidade'] ?? $obBudget->cidade;
        $obBudget->estado       = $postVars['estado'] ?? $obBudget->estado;

        $obBudget->atualizar();

        $dadosDinamicos = array();
        // Itera sobre as chaves do array $_POST
        foreach ($postVars as $chave => $valor) {

            // Verifica se a chave começa com o prefixo desejado, por exemplo, 'tipo'
            if (strpos($chave, 'id') === 0) {
                // Extrai o índice do tipo (0, 1, 2, ...)
                $indice = substr($chave, 2); // 'id' tem 2 caracteres
                // Armazena o valor na posição correspondente do array $dadosDinamicos
                $dadosDinamicos[$indice]['id'] = $valor;
            } elseif (strpos($chave, 'tipo') === 0) {
                $indice = substr($chave, 4); // 'tipo' tem 4 caracteres
                $dadosDinamicos[$indice]['tipo'] = $valor;
            } elseif (strpos($chave, 'servico') === 0) {
                $indice = substr($chave, 7); // 'servico' tem 7 caracteres
                $dadosDinamicos[$indice]['servico'] = $valor;
            } elseif (strpos($chave, 'qtd_serv') === 0) {
                $indice = substr($chave, 8); // 'qtd_serv' tem 8 caracteres
                $dadosDinamicos[$indice]['qtd_serv'] = $valor;
            } elseif (strpos($chave, 'observacao') === 0) {
                $indice = substr($chave, 10); // 'observacao' tem 10 caracteres
                $dadosDinamicos[$indice]['observacao'] = $valor;
            } elseif (strpos($chave, 'preco') === 0) {
                $indice = substr($chave, 5); // 'preco' tem 5 caracteres
                // $parts = explode('R$ ', $valor);
                // $value = $parts[1];
                $dadosDinamicos[$indice]['preco'] = $valor;
            }
        }

        $results = EntityBudgetServ::getservices('id_orcamento=' . $id);

        $index = 0; // Variável para rastrear o índice em $dadosDinamicos
        while ($obBludgetServ = $results->fetchObject(EntityBudgetServ::class)) {

            // Verifica se ainda há dados dinâmicos para atualizar
            if (isset($dadosDinamicos[$index])) {

                // Atualiza o objeto $obBludgetServ com os valores de $dadosDinamicos
                $obBludgetServ->tipo        = $dadosDinamicos[$index]['tipo']           ?? $obBludgetServ->tipo;
                $obBludgetServ->servico     = $dadosDinamicos[$index]['servico']        ?? $obBludgetServ->servico;
                $obBludgetServ->qtd_servico = $dadosDinamicos[$index]['qtd_serv']    ?? $obBludgetServ->qtd_servico;
                $obBludgetServ->preco       = $dadosDinamicos[$index]['preco']          ?? $obBludgetServ->preco;
                $obBludgetServ->observacao  = $dadosDinamicos[$index]['observacao']     ?? $obBludgetServ->observacao;

                // Incrementa o índice para o próximo conjunto de dados dinâmicos
                $index++;
            }
            // Atualiza o objeto no banco de dados
            $obBludgetServ->atualizar();
        }
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user?status=updated');
    }

    
    /**
     * Método responsável por remover serviço de um orçamento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getAddBlutServ($request, $idServ,$idBudget)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obServ = EntityService::getServiceById($idServ);       

        //VALIDA A INSTANCIA
        if (!$obServ instanceof EntityService) {
           $request->getRouter()->redirect('/user/budget/'.$idBudget.'/edit');
        }
        
        $obBludgetServ              = new EntityBudgetServ;
        $obBludgetServ->tipo        = $obServ->tipo;
        $obBludgetServ->servico     = $obServ->servico;
        $obBludgetServ->qtd_servico = 1;
        $obBludgetServ->preco       = $obServ->preco;
        $obBludgetServ->observacao  = '';
        $obBludgetServ->id_orcamento= $idBudget;

        $obBludgetServ->cadastrar();
    
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/budget/'.$idBudget.'/edit?status=servCreated');
    }

    /**
     * Método responsável por remover serviço de um orçamento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getdeleteBlutServ($request, $id)
    {
        $obBludgetServ = EntityBudgetServ::getServiceBudgetById($id);
        $id_orcamento= $obBludgetServ->id_orcamento;

        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obBludgetServ = EntityBudgetServ::getServiceBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBludgetServ instanceof EntityBudgetServ) {
           $request->getRouter()->redirect('/user/budget/{'.$id_orcamento.'}/edit');
        }

         //EXCLUI O DEPOIMENTO
         $obBludgetServ->excluir();
 
         //REDIRECIONA O USUÁRIO
         $request->getRouter()->redirect('/user/budget/'.$id_orcamento.'/edit?status=ServiceDeleted');
        //user/budget/78/edit
    }

    /**
     * Método responsável por retornar o formulário de conclusão de um orçamento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getConcluirBudget($request, $id)
    {
        $obBudget = EntityBudget::getBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBudget instanceof EntityBudget) {
            $request->getRouter()->redirect('/user');
        }

        $nome = '';
        $obCli = EntityClient::getClientById($obBudget->id_cli);
        if (!$obBudget->nome_cli) {
            $obCli = EntityClient::getClientById($obBudget->id_cli);
            $nome = $obCli->nome;
        } else {
            $nome = $obBudget->nome_cli;
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/budget/concluir', [
            'nome'      => $nome
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Concluir Serviço > Univesp', $content, 'budget');
    }


    /**
     * Método responsável por concluir um orçamento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setConcluirBudget($request, $id)
    {
        $obBudget = EntityBudget::getBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBudget instanceof EntityBudget) {
            $request->getRouter()->redirect('/user');
        }

        $obBudget->ativo = 2;
        $obBudget->data_fim = date('Y-m-d H:i:s');

        //EXCLUI O DEPOIMENTO
        $obBudget->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user?status=complete');
    }

    /**
     * Método responsável por retornar o formulário de cancelamento de um orçamento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getCancelBudget($request, $id)
    {
        $obBudget = EntityBudget::getBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBudget instanceof EntityBudget) {
            $request->getRouter()->redirect('/user');
        }

        $nome = '';
        $obCli = EntityClient::getClientById($obBudget->id_cli);
        if (!$obBudget->nome_cli) {
            $obCli = EntityClient::getClientById($obBudget->id_cli);
            $nome = $obCli->nome;
        } else {
            $nome = $obBudget->nome_cli;
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/budget/cancel', [
            'nome'      => $nome
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cancelar Serviço > Univesp', $content, 'budget');
    }

    /**
     * Método responsável por cancelar um orçamento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setCancelBudget($request, $id)
    {
        $obBudget = EntityBudget::getBudgetById($id);

        //VALIDA A INSTANCIA
        if (!$obBudget instanceof EntityBudget) {
            $request->getRouter()->redirect('/user');
        }

        $obBudget->ativo = 1;
        $obBudget->data_fim = date('Y-m-d H:i:s');

        //EXCLUI O DEPOIMENTO
        $obBudget->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user?status=cancel');
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
                return Alert::getError('Orçamento Excluido!');
                break;
            case 'created':
                return Alert::getSuccess('Orçamento Criado');
                break;
            case 'servCreated':
                return Alert::getSuccess('Serviço Adicionado');
                break;
            case 'updated':
                return Alert::getSuccess('Orçamento atualizado');
                break;
            case 'complete':
                return Alert::getSuccess('Orçamento concluido');
                break;
            case 'cancel':
                return Alert::getSuccess('Orçamento cancelado');
                break;
            case 'ServiceDeleted':
                return Alert::getSuccess('Serviço Removido');
                break;
        }
    }
}
