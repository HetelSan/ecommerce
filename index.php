<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;

$app = new Slim();

$app->config('debug', true);

// Criando a rota(aquilo que é informado na URL do navegador)
$app->get('/', function() {

	// echo "OK";
	$page = new Page();

	$page->setTpl("index");

});

// Criando uma nova rota(aquilo que é informado na URL do navegador)
$app->get('/Admin', function() {

	$page = new PageAdmin();   // classe que irá produrar os templates corretos

	$page->setTpl("index");

});

$app->run();

?>
