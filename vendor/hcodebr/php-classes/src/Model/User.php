<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use Hcode\Mailer;

class User extends Model
{

    const SESSION = "User";
    const SECRET = "HcodePhp7_Secret";
    const ERROR = "UserError";
    const ERROR_REGISTER = "UserErrorRegister";
    const SUCCESS = "UserSuccess";

    /**
     * 
     * @return \Hcode\Model\User
     */
    public static function getFromSession()
    {

        $user = new User();

        if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0) {

            $user->setData($_SESSION[User::SESSION]);

        }

        return $user;

    }

    /**
     * 
     * @param type $inadmin
     * @return boolean
     */
    public static function checkLogin($inadmin = true)
    {

        if (!isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0) {

            // não está logado
            return false;

        } else {

            if ($inadmin === true && (bool)$_SESSION[User::SESSION]["inadmin"] === true) {

                return true;

            } else if ($inadmin === false) {

                return true;

            } else {

                return false;

            }
        }

    }

    /**
     * 
     * @param type $login
     * @param type $password
     * @return \Hcode\Model\User
     * @throws \Exception
     */
    public static function login($login, $password)
    {

        $sql = new Sql();

        $results = $sql->select("
            SELECT * 
              FROM tb_users a 
             INNER JOIN tb_persons b ON a.idperson = b.idperson 
             WHERE a.deslogin = :login
        ", [
            ":login" => $login
        ]);

        if (count($results) === 0) {

            throw new \Exception("Usuário inexistente ou senha inválida.");

        }

        $data = $results[0];

        if (password_verify($password, $data["despassword"]) === true) {
            $user = new User();

            $data['desperson'] = utf8_encode($data['desperson']);

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;

        } else {

            throw new \Exception("Usuário inexistente ou senha inválida.");
        }

    }

    /**
     * 
     * @param type $inadmin
     */
    public static function verifyLogin($inadmin = true)
    {

        if (!User::checkLogin($inadmin)) {

            if ($inadmin) {

                header("Location: /admin/login");

            } else {

                header("Location: /login");

            }

            exit;

        }

    }

    /**
     * 
     */
    public static function logout()
    {

        $_SESSION[User::SESSION] = null;

    }

    /**
     * 
     * @return type
     */
    public static function listAll()
    {

        $sql = new Sql();

        $results = $sql->select("
            SELECT * 
            FROM tb_users a 
            INNER JOIN tb_persons b USING (idperson) 
            ORDER BY b.desperson
        ");

        return $results;

    }

    /**
     * 
     */
    public function save()
    {

        $sql = new Sql();

        $results = $sql->select("
            CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)
        ", [
            ":desperson" => utf8_decode($this->getdesperson()),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => User::getPasswordHash($this->getdespassword()),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ]);

        $this->setData($results[0]);

    }

    /**
     * 
     * @param type $iduser
     */
    public function get($iduser)
    {

        $sql = new Sql();

        $results = $sql->select("
            SELECT * 
              FROM tb_users a 
             INNER JOIN tb_persons b USING (idperson) 
             WHERE a.iduser = :iduser
        ", [
            ":iduser" => $iduser
        ]);

        $data = $results[0];

        $data['desperson'] = utf8_encode($data['desperson']);

        $this->setData($data);

    }

    /**
     * 
     */
    public function update()
    {

        $sql = new Sql();

        $results = $sql->select("
            CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)
        ", [
            ":iduser" => $this->getiduser(),
            ":desperson" => utf8_decode($this->getdesperson()),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => User::getPasswordHash($this->getdespassword()),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ]);

        $this->setData($results[0]);

    }

    /**
     * 
     */
    public function delete()
    {

        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)", [
            ":iduser" => $this->getiduser()
        ]);

    }

    /*

    public static function getForgot($email, $inadmin = true)
    {
        $sql = new Sql();

        $results = $sql->select("
            SELECT * 
              FROM tb_persons a 
             INNER JOIN tb_users b USING(idperson) 
             WHERE a.desemail = :email
        ", [
            ":email" => $email
        ]);

        if (count($results) === 0) {

            throw new \Exception("Não foi possível recuperar a senha(1).");

        } else {

            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", [
                ":iduser" => $data['iduser'],
                ":desip" => $_SERVER['REMOTE_ADDR']
            ]);

            if (count($results2) === 0) {

                throw new \Exception("Não foi possível recuperar a senha(2).");

            } else {

                $dataRecovery = $results2[0];
                $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
                $result = base64_encode($iv . $code);

                if ($inadmin === true) {

                    $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";

                } else {

                    $link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";

                }

                $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
                    "name" => $data['desperson'],
                    "link" => $link
                ));

                $mailer->send();

                return $link;
            }
        }
    }

    public static function validForgotDecrypt($result)
    {

        $result = base64_decode($result);
        $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
        $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
        $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);

        $sql = new Sql();

        $results = $sql->select("
            SELECT * 
              FROM tb_userspasswordsrecoveries a 
             INNER JOIN tb_users b USING (iduser) 
             INNER JOIN tb_persons c USING (idperson) 
             WHERE a.idrecovery = :idrecovery 
               AND a.dtrecovery IS NULL 
               AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()
        ", [
            ":idrecovery" => $idrecovery
        ]);

        if (count($results) === 0) {

            throw new \Exception("Não foi possível recuperar a senha(3).");

        } else {

            return $results[0];
            exit;

        }
    }

     */

    /**
     * 
     * @param type $email
     * @param type $inadmin
     * @return type
     * @throws \Exception
     */
    public static function getForgot($email, $inadmin = true)
    {

        $sql = new Sql();

        $results = $sql->select("
			SELECT *
			  FROM tb_persons a
			 INNER JOIN tb_users b USING(idperson)
             WHERE a.desemail = :email;
        ", [
            ":email" => $email
        ]);

        if (count($results) === 0) {

            throw new \Exception("Não foi possivel recuperar a senha");

        } else {

            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", [
                ":iduser" => $data["iduser"],
                ":desip" => $_SERVER["REMOTE_ADDR"]
            ]);

            if (count($results2) === 0) {

                throw new \Exception("Não foi possivel recuperar a senha");

            } else {

                $dataRecovery = $results2[0];

                $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
                $result = base64_encode($iv . $code);

                if ($inadmin === true) {

                    $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";

                } else {

                    $link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";

                }

                $mailer = new Mailer(
                    $data["desemail"],
                    $data["desperson"],
                    "Redefinir senha da Hcode",
                    "forgot",
                    array(
                        "name" => $data["desperson"],
                        "link" => $link
                    )
                );

                $mailer->send();

                return $data;
            }
        }
    }

    /**
     * 
     * @param type $result
     * @return type
     * @throws \Exception
     */
    public static function validForgotDecrypt($result)
    {
        $result = base64_decode($result);
        $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
        $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');;
        $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);
        
        $sql = new Sql();

        $results = $sql->select("
            SELECT *
              FROM tb_userspasswordsrecoveries a
             INNER JOIN tb_users b USING(iduser)
             INNER JOIN tb_persons c USING(idperson)
             WHERE a.idrecovery = :idrecovery
               AND a.dtrecovery IS NULL
               AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
        ", array(
            ":idrecovery" => $idrecovery
        ));

        if (count($results) === 0) {

            throw new \Exception("Não foi possível recuperar a senha.");

        } else {

            return $results[0];

        }
    }

    /**
     * 
     * @param type $idrecovery
     */
    public static function setForgotUsed($idrecovery)
    {

        $sql = new Sql();

        $sql->query("
            UPDATE tb_userspasswordsrecoveries 
               SET dtrecovery = NOW() 
             WHERE idrecovery = :idrecovery
        ", [
            ":idrecovery" => $idrecovery
        ]);
    }

    /**
     * 
     * @param type $password
     */
    public function setPassword($password)
    {

        $sql = new Sql();

        $sql->query("
            UPDATE tb_users 
               SET despassword = :password 
             WHERE iduser = :iduser
        ", [
            ":password" => $password,
            ":iduser" => $this->getiduser()
        ]);

    }

    /**
     * 
     * @param type $msg
     */
    public static function setError($msg)
    {

        $_SESSION[User::ERROR] = $msg;

    }

    /**
     * 
     * @return type
     */
    public static function getError()
    {

        $msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

        User::clearError();

        return $msg;

    }

    /**
     * 
     */
    public static function clearError()
    {

        $_SESSION[User::ERROR] = null;

    }

    /**
     * 
     * @param type $msg
     */
    public static function setSuccess($msg)
    {

        $_SESSION[User::SUCCESS] = $msg;

    }

    /**
     * 
     * @return type
     */
    public static function getSuccess()
    {

        $msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';

        User::clearSuccess();

        return $msg;

    }

    /**
     * 
     */
    public static function clearSuccess()
    {

        $_SESSION[User::SUCCESS] = null;

    }

    /**
     * 
     * @param type $msg
     */
    public static function setErrorRegister($msg)
    {

        $_SESSION[User::ERROR_REGISTER] = $msg;

    }

    /**
     * 
     * @return type
     */
    public static function getErrorRegister()
    {

        $msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[USER::ERROR_REGISTER] : '';

        User::clearErrorRegister();

        return $msg;

    }

    /**
     * 
     */
    public static function clearErrorRegister()
    {

        $_SESSION[User::ERROR_REGISTER] = null;

    }

    /**
     * 
     * @param type $login
     * @return type
     */
    public static function checkLoginExists($login)
    {

        $sql = new Sql();

        $results = $sql->select("
            SELECT *
              FROM tb_users
             WHERE deslogin = :deslogin
        ", [
            ':deslogin' => $login
        ]);

        return (count($results) > 0);

    }

    /**
     * 
     * @param type $password
     * @return type
     */
    public static function getPasswordHash($password)
    {

        return password_hash($password, PASSWORD_DEFAULT, [
            'cost' => 12
        ]);

    }

    /**
     * 
     */
    public function getOrders()
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
             WHERE a.iduser = :iduser
        ", [
            ':iduser' => $this->getiduser()
        ]);

        return $results;

    }

    /**
     * 
     * @param type $page
     * @param type $itemsPerPage
     * @return type
     */
    public static function getUsersPage($page = 1, $itemsPerPage = 10)
    {

        $start = ($page - 1) * $itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_users a 
            INNER JOIN tb_persons b USING (idperson) 
            ORDER BY b.desperson
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
    public static function getUsersPageSearch($search, $page = 1, $itemsPerPage = 10)
    {

        $start = ($page - 1) * $itemsPerPage;

        $sql = new Sql();

        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_users a 
            INNER JOIN tb_persons b USING (idperson) 
            WHERE b.desperson LIKE :search 
            OR    b.desemail = :search 
            OR    a.deslogin LIKE :search
            ORDER BY b.desperson
            LIMIT $start, $itemsPerPage;
        ", [
            ':search' => '%' . $search . '%'
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
