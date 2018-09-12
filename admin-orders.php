<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use Hcode\Model\OrderStatus;

// rota para listar o status de um pedido
$app->get('/admin/orders/:idorder/status', function ($idorder) {

    User::verifyLogin();

    $order = new Order();

    $order->get((int)$idorder);

    $page = new PageAdmin();

    $page->setTpl("order-status", [
        'order' => $order->getValues(),
        'status' => OrderStatus::listAll(),
        'msgError' => Order::getError(),
        'msgSuccess' => Order::getSuccess()
    ]);

});

// rota para alterar o status de um pedido
$app->post('/admin/orders/:idorder/status', function ($idorder){

    User::verifyLogin();

    if (!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0) {

        Order::setError("Informe o status atual.");
        header("Location: /admin/orders/" . $idorder . "/status");
        exit;
        
    }
    
    $order = new Order();
    
    $order->get((int)$idorder);
    
    $order->setidstatus((int)$_POST['idstatus']);
    
    $order->save();

    Order::setSuccess("Status atualizado.");
    
    header("Location: /admin/orders/" . $idorder . "/status");
    exit;

});

// rota para excluir um pedido
$app->get('/admin/orders/:idorder/delete', function ($idorder) {

    // verifica se o usário está logado e tem permissão de administrador
    User::verifyLogin();

    // cria uma instância do pedido
    $order = new Order();

    // verificar se o pedido ainda existe no banco de dados
    $order->get((int)$idorder);

    $order->delete();

    header("Location: /admin/orders");
    exit;

});

// rota para o status do pedido
$app->get('/admin/orders/:idorder', function ($idorder) {

    User::verifyLogin();

    $order = new Order();

    $order->get((int)$idorder);

    $cart = $order->getCart();

    $page = new PageAdmin();

    $page->setTpl("order", [
        'order' => $order->getValues(),
        'cart' => $cart->getValues(),
        'products' => $cart->getProducts()
    ]);

});


// rota para listar todos os pedidos
$app->get('/admin/orders', function () {

    User::verifyLogin();

    // se a variável $search existir, retorna ela mesma, caso contrário retorna vazio
	$search = (isset($_GET['search'])) ? $_GET['search'] : ''; 
	
	// definindo a página atual
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') {

		$pagination = Order::getOrdersPageSearch($search, $page);
		
	} else {
		
		$pagination = Order::getOrdersPage($page);
	}

	// var_dump($pagination['data']);
	// exit;

	$pages = [];

	for ($i=0; $i < $pagination['pages']; $i++) 
	{ 

		array_push($pages, [
			'href' => '/admin/orders?' . http_build_query([
				'page' => $i + 1,
				'search' => $search
			]),
			'text' => $i + 1
		]);

	}

    $page = new PageAdmin();

    $page->setTpl("orders", [
        'orders' => $pagination['data'],
        'search' => $search,
        'pages' => $pages
    ]);

});

?>
