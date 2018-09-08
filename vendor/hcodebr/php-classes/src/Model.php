<?php

namespace Hcode;

class Model 
{
    private $values = [];

    /**
     * 
     * @param type $name
     * @param type $arguments
     * @return type
     */
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
     * @param type $data
     */
    public function setData($data = array())
    {

        foreach ($data as $key => $value) {
            
            $this->{"set" . $key}($value);

        }
    }

    /**
     * 
     * @return type
     */
    public function getValues()
    {

        return $this->values;

    }
}

?>
