<?php

namespace Hcode;

class Model 
{
    private $values = [];

    public function __call($name, $arguments)
    {

        $method    = substr($name, 0, 3);
        $fieldName = substr($name, 3, strlen($name));

        switch ($method)
        {

            case "get":
                return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;  // a primeira vez que etá inserindo, a variável não existe, portanto é passado null.
            break;

            case "set":
                $this->values[$fieldName] = $arguments[0];
            break;
        }

    }

    /**
     * 
     * Faz um set automático de todos os campos que vieram do banco de dados.
     * 
     */
    public function setData($data = array())
    {

        foreach ($data as $key => $value) {
            
            $this->{"set" . $key}($value);

        }
    }

    /**
     * 
     * Função utilizada para retornar o atributo do banco de dados.
     * Não acessar o atributo diretamente pois não é uma boa prática. 
     * O atributo é privado. 
     * E por maior segurança
     * 
     */
    public function getValues()
    {

        return $this->values;

    }
}

?>
