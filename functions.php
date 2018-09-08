<?php

use \Hcode\Model\User;
use \Hcode\Model\Cart;

/**
 * 
 * @param int $vlprice
 * @return type
 */
function formatPrice($vlprice)
{

    if (!$vlprice > 0) $vlprice = 0;
    
    return number_format($vlprice, 2, ",", ".");

}

/**
 * 
 * @param type $inadmin
 * @return type
 */
function checkLogin($inadmin = true)
{

    return User::checkLogin($inadmin);

}

/**
 * 
 * @return type
 */
function getUserName()
{

    $user = User::getFromSession();

    return $user->getdesperson();

}

/**
 * 
 * @return type
 */
function getCartNrQtd()
{

    $cart = Cart::getFromSession();

    $totals = $cart->getProductsTotals();

    return $totals['nrqtd'];

}

/**
 * 
 * @return type
 */
function getCartVlSubTotal()
{

    $cart = Cart::getFromSession();

    $totals = $cart->getProductsTotals();

    return formatPrice($totals['vlprice']);

}

?>
