<?php

namespace App\Controller\User;

use \App\Utils\View;

class Page
{

    /**
     * Módulos disponíveis no painel
     * @var array
     */
    private static $userModules = [
        'home' => [
            'label' =>  'Página Inicial',
            'link'  =>  URL . '/user'
        ],
        'service' => [
            'label' =>  'Serviços',
            'link'  =>  URL . '/user/service'
        ],
        'client' => [
            'label' =>  'Clientes',
            'link'  =>  URL . '/user/client'
        ],
        'budget' => [
            'label' =>  'Orçamentos',
            'link'  =>  URL . '/user/budget'
        ]
    ];

    /**
     * Método responsável por retornar o conteúdo (view) da estrutura genérica de página do painel
     * @param string $title
     * @param string $content
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render('user/page', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * Método responsavel por renderizar a view do menu do painel
     * @param string $currentModule
     * @return string
     */
    private static function getMenu($currentModule)
    {
        $links = '';

        //ITERA OS MÓDULOS
        foreach (self::$userModules as $hash => $module) {
            $links .= View::render('user/menu/link', [
                'label'     => $module['label'],
                'link'      => $module['link'],
                'current'   => $hash == $currentModule ? 'text-danger' : ''
            ]);
        }

        //RETORNA A RENDERIZAÇÃO DO MENU
        return View::render('user/menu/box', [
            'links' => $links
        ]);
    }

    /**
     * Método responsável por renderizar a view do painel com conteúdos dinámicos
     * @param   string $title
     * @param   string $content
     * @param   string $currentModule
     * @return  string
     */
    public static function getPainel($title, $content, $currentModule)
    {
        //RENDERIZA A VIEW DO PAINEL
        $contentPanel = View::render('user/painel', [
            'menu' => self::getMenu($currentModule),
            'content' => $content
        ]);

        //RETORNA A PÁGINA RENDERIZADA
        return self::getPage($title, $contentPanel);
    }

    /**
     * Método responsável por renderizar o layout de paginação
     * @param Request $request
     * @param Pagination @obPagination
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        //PÁGINAS
        $pages = $obPagination->getPages();

        //VERIFICA A QUANTIDADE DE PÁGINAS
        if (count($pages) <= 1) return '';

        //LINKS
        $links = '';

        //URL ATUAL (SEM GETS)
        $url = $request->getRouter()->getCurrentUrl();

        //GET
        $queryParams = $request->getQueryParams();

        //RENDERIZA OS LINKS
        foreach ($pages as $page) {
            //ALTERA A PÁGINA
            $queryParams['page'] = $page['page'];

            //LINK
            $link = $url . '?' . http_build_query($queryParams);

            //VIEW
            $links .= View::render('user/pagination/link', [
                'page'  => $page['page'],
                'link'  => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);
        }
        //RENDERIZA BOX DE PAGINAÇÃO
        return View::render('user/pagination/box', [
            'links'  => $links
        ]);
    }
}
