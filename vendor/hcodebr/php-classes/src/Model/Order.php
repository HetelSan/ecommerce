<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class Order extends Model
{

    const ERROR = "OrderError";
    const SUCCESS = "OrderSuccess";

    /**
     * 
     */
    public function save()
    {

        $sql = new Sql();

        $results = $sql->select("CALL sp_orders_save(:idorder, :idcart, :iduser, :idstatus, :idaddress, :vltotal)", [
            ':idorder' => $this->getidorder(),
            ':idcart' => $this->getidcart(),
            ':iduser' => $this->getiduser(),
            ':idstatus' => $this->getidstatus(),
            ':idaddress' => $this->getidaddress(),
            ':vltotal' => $this->getvltotal()
        ]);

        if (count($results) > 0) {

            $this->setData($results[0]);

        }

    }

    /**
     * 
     * @param type $idorder
     */
    public function get($idorder)
    {

        $sql = new Sql();

        $results = $sql->select("
            SELECT *
            FROM tb_orders a
            INNER JOIN tb_ordersstatus b USING(idstatus)
            INNER JOIN tb_carts c USING(idcart)
            INNER JOIN tb_users d ON d.iduser = a.iduser
            INNER JOIN tb_addresses e USING(idaddress)
            INNER JOIN tb_persons f ON f.idperson = d.idperson
            WHERE a.idorder = :idorder
        ", [
            ':idorder' => $idorder
        ]);

        if (count($results) > 0) {

            $this->setData($results[0]);
        }

    }

    public static function listAll()
    {

        $sql = new Sql();

        return $sql->select("
            SELECT *
            FROM tb_orders a
            INNER JOIN tb_ordersstatus b USING(idstatus)
            INNER JOIN tb_carts c USING(idcart)
            INNER JOIN tb_users d ON d.iduser = a.iduser
            INNER JOIN tb_addresses e USING(idaddress)
            INNER JOIN tb_persons f ON f.idperson = d.idperson
            ORDER BY a.dtregister DESC
        ");

    }

    public function delete()
    {

        $sql = new Sql();

        $sql->query("DELETE FROM tb_orders WHERE idorder = :idorder", [
            ':idorder' => $this->getidorder()
        ]);

    }

    // esta método retorna uma instância da classe cart
    public function getCart():Cart
    {

        $cart = new Cart();

        $cart->get((int)$this->getidcart());

        return $cart;

    }

    /**
     * 
     * @param type $msg
     */
    public static function setError($msg)
    {

        $_SESSION[Order::ERROR] = $msg;

    }

    /**
     * 
     * @return type
     */
    public static function getError()
    {

        $msg = (isset($_SESSION[Order::ERROR]) && $_SESSION[Order::ERROR]) ? $_SESSION[Order::ERROR] : '';

        Order::clearError();

        return $msg;

    }

    /**
     * 
     */
    public static function clearError()
    {

        $_SESSION[Order::ERROR] = null;

    }

    /**
     * 
     * @param type $msg
     */
    public static function setSuccess($msg)
    {

        $_SESSION[Order::SUCCESS] = $msg;

    }

    /**
     * 
     * @return type
     */
    public static function getSuccess()
    {

        $msg = (isset($_SESSION[Order::SUCCESS]) && $_SESSION[Order::SUCCESS]) ? $_SESSION[Order::SUCCESS] : '';

        Order::clearSuccess();

        return $msg;

    }

    /**
     * 
     */
    public static function clearSuccess()
    {

        $_SESSION[Order::SUCCESS] = null;

    }

    /**
     * 
     * @param type $page
     * @param type $itemsPerPage
     * @return type
     */
    public static function getOrdersPage($page = 1, $itemsPerPage = 10)
    {

        $start = ($page - 1) * $itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_orders a
            INNER JOIN tb_ordersstatus b USING(idstatus)
            INNER JOIN tb_carts c USING(idcart)
            INNER JOIN tb_users d ON d.iduser = a.iduser
            INNER JOIN tb_addresses e USING(idaddress)
            INNER JOIN tb_persons f ON f.idperson = d.idperson
            ORDER BY a.dtregister DESC
            LIMIT $start, $itemsPerPage;
        ");

        $resultTotal = $sql->select("SELECT found_rows() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int)$resultTotal[0]["nrtotal"],
            'pages' => ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
        ];

    }

    /**
     * 
     * @param type $page
     * @param type $itemsPerPage
     * @return type
     */
    public static function getOrdersPageSearch($search, $page = 1, $itemsPerPage = 10)
    {

        $start = ($page - 1) * $itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_orders a
            INNER JOIN tb_ordersstatus b USING(idstatus)
            INNER JOIN tb_carts c USING(idcart)
            INNER JOIN tb_users d ON d.iduser = a.iduser
            INNER JOIN tb_addresses e USING(idaddress)
            INNER JOIN tb_persons f ON f.idperson = d.idperson
            WHERE a.idorder = :id OR f.desperson LIKE :search
            ORDER BY a.dtregister DESC
            LIMIT $start, $itemsPerPage;
        ", [
            ':search' => '%' . $search . '%',
            ':id' => $search
        ]);

        $resultTotal = $sql->select("SELECT found_rows() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int)$resultTotal[0]["nrtotal"],
            'pages' => ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
        ];

    }
    
}

?>
