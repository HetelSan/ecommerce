<?php 

namespace Hcode\Model;

// use Rain\Tpl\Exception;
use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model
{

    const SESSION = "User";

    /**
     * 
     * Esta função verifica se o usuário informado existe no banco de bados
     * 
     */
    public static function login($login, $password)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE des_login = :LOGIN", array(
            ":LOGIN" => $login
        ));

        if (count($results) === 0)
        {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
        
        $data = $results[0];  // pegando a primeira ocorrência do array $results.
        
        if (password_verify($password, $data["des_password"]) === true)
        {
            $user = new User();

            $user->setData($data);

            // var_dump($user);
            // exit;

            // criar uma sessão 
            $_SESSION[User::SESSION] = $user->getValues();

            return $user;

        } else {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }

    }

    public static function verifyLogin($inadmin = true)
    {

        if (
            !isset($_SESSION[User::SESSION])                            // a sessão existe?
            ||
            !$_SESSION[User::SESSION]                                   // a sessão é diferente de vazia
            ||
            !(int)$_SESSION[User::SESSION]["id_user"] > 0               // o id do usuário logado é maior que zero
            ||
            (bool)$_SESSION[User::SESSION]["in_admin"] !== $inadmin     // o usuário logado não é administrador
        ) {
        
            header("Location: /admin/login");
            exit;

        }

    }

    public static function logout()
    {

        $_SESSION[User::SESSION] = NULL;

    }

}

?>
