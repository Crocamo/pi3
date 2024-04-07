<?php
require __DIR__ . '/../vendor/autoload.php';

use \App\Utils\View;
use \App\DotEnv\Environment;
use \App\Db\Database;
use \App\Http\Middleware\Queue as MiddlewareQueue;

//CARREGA VARIÁVEIS DE AMBIENTE
Environment::load(__DIR__.'/../');

//DEFINE AS CONFIGURAÇÕES DE BANCO DE DADOS
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

//DEFINE A CONSTANTE DE URL
define('URL', getenv('URL'));
 
//DEFINE O VALOR PADRÃO DAS VARIÁVEIS
View::init([
    'URL'=>URL
]);

//DEFINE O MAPEAMENTO DE MIDLLEWARES
MiddlewareQueue::setMap([
    'maintenance'           => \App\Http\Middleware\Maintenance::class,
    'required-user-logout'  => \App\Http\Middleware\RequireUserLogout::class,
    'required-user-login'   => \App\Http\Middleware\RequireUserLogin::class,
    'api'                   => \App\Http\Middleware\Api::class,
    'user-basic-auth'       => \App\Http\Middleware\UserBasicAuth::class,
    'jwt-auth'              => \App\Http\Middleware\JWTAuth::class,
    'cache'                 => \App\Http\Middleware\Cache::class,

]);

//DEFINE O MAPEAMENTO DE MIDLLEWARES PADRÕES (EXECUTADAS EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
    'maintenance'
]);