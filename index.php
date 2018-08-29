<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

// Criando a rota(aquilo que é informado na URL do navegador)
$app->get('/', function() {

	// echo "OK";
	$page = new Page();

	$page->setTpl("index");

});

// Criando uma nova rota(aquilo que é informado na URL do navegador)
$app->get('/admin', function() {

	User::verifyLogin();	

	$page = new PageAdmin();   // classe que irá produrar os templates corretos

	$page->setTpl("index");

});

$app->get('/admin/login', function() {

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("login");
	
});

// após informar o login, o método utilizado é post
$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;

});

// método para fazer o logout
$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;
	
});


$app->run();

?>
