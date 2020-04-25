<?php
/**
 * Classe com propriedadedes e metodos utilizado por mais de uma Classe dessa aplicação
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\Utils;

class Where extends Statement
{
    /**
     * Condicão(ões) para declaração.
     *
     * @property array
     */
    protected $where;

    /**
     * Inicia a montagem da declaração
     * Indica qual o limite padrão da declaração
     * @param string  $type  Tipo da declaração. Ex.: SELECT|INSERT|UPDATE|DELETE
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     * @param int  $defaultLimit  Número inteiro com o limite padrão da consulta
     */
    protected function __construct(string $type, int $defaultLimit, &$mydatabase)
    {
        parent::__construct($type, $defaultLimit, $mydatabase);
    } // FIM -> __construct

     /**
     * Indica qual o nome da coluna que será utilizada na cláusula where
     *
     * @param  string  $column  nome da coluna que será utilizada na cláusula where
     * @return $this 
     */
    public function where(string $column)
    {
        $i = isset($this->where["column"]) ? \count($this->where["column"]) : 0;

        $this->where["column"][$i] = $column;
        $this->where["complete"]   = false;
        
        return $this;
    } // FIM -> where
    
    /**
     * Indica qual o nome da coluna que será utilizada na cláusula where
     *
     * @param  string  $column  nome da coluna que será utilizada na cláusula where
     * @return $this 
     */
    public function and(string $column)
    {
        $this->setLogicalOperators("AND", $column);
        return $this;
    } // FIM -> and
    
    /**
     * Indica qual o nome da coluna que será utilizada na cláusula where
     *
     * @param  string  $column  nome da coluna que será utilizada na cláusula where
     * @return $this 
     */
    public function or(string $column)
    {
        $this->setLogicalOperators("OR", $column);
        return $this;
    } // FIM -> or
    
    
    /**
     * Indica uma negação lógica na cláusula where
     *
     * @return $this 
     */
    public function not()
    {  
        if (isset($this->where["logicalOperators"])) {
            $i = \count($this->where["logicalOperators"]) - 1;

            $this->where["logicalOperators"][$i] .= " NOT";
        } else if (isset($this->where["comparisonOperator"])) {
            $i = \count($this->where["comparisonOperator"]) - 1;

            $this->where["comparisonOperator"][$i] = "!{$this->where["comparisonOperator"][$i]}";
        }
        return $this;
    } // FIM -> not
    

    # NOT
    
    /**
     * Indica o operador de comparação = que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function notEquals($value)
    {
        $this->setComparisonOperator("!=", $value);
        return $this;
    } // FIM -> notEquals

    /**
     * Indica o operador de lógico e o nome da coluna necessário para cláusula where
     *
     * @param  string  $operator  Operador lógico para montar a cláusula where
     * @param  string  $column  nome da coluna que será utilizada na cláusula where
     * @return $this 
     */
    public function setLogicalOperators(string $operator, string $column)
    {
        $i = isset($this->where["logicalOperators"]) ? \count($this->where["logicalOperators"]) : 0;
        $j = \count($this->where["column"]);

        $this->where["logicalOperators"][$i] = $operator;

        $this->where["complete"]   = false;
        $this->where["column"][$j] = $column;
        
        return $this;
    } // FIM -> setLogicalOperators

    
    /**
     * Indica o operador de comparação = que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function equals($value)
    {
        $this->setComparisonOperator("=", $value);
        return $this;
    } // FIM -> equals
    
    /**
     * Indica o operador de comparação < que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function less($value)
    {
        $this->setComparisonOperator("<", $value);
        return $this;
    } // FIM -> less
    
    /**
     * Indica o operador de comparação <= que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function lessEqual($value)
    {
        $this->setComparisonOperator("<=", $value);
        return $this;
    } // FIM -> lessEqual
    
    /**
     * Indica o operador de comparação > que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function bigger($value)
    {
        $this->setComparisonOperator(">", $value);
        return $this;
    } // FIM -> bigger
    
    /**
     * Indica o operador de comparação >= que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function biggerEqual($value)
    {
        $this->setComparisonOperator(">=", $value);
        return $this;
    } // FIM -> biggerEqual

    /**
     * Indica o operador de comparação <> que será utilizada na cláusula where
     *
     * @param  string|bool|int  $value  Valor de comparação necessário para montar a cláusula where
     * @return $this 
     */
    public function different($value)
    {
        $this->setComparisonOperator("<>", $value);
        return $this;
    } // FIM -> different

    
    /**
     * Prepara operador BETWEEN para ser inserido na declaração 
     *
     * @param  string|bool|int  $value1  Valor inicial necessário para montar o operador BETWEEN na declaração
     * @param  string|bool|int  $value2  Valor final necessário para montar o operador BETWEEN na declaração
     * @return $this 
     */
    public function between($value1, $value2)
    {
        $this->setComparisonOperator("BETWEEN", [$value1, $value2]);
        return $this;
    } // FIM -> between

    /**
     * Prepara operador NOT BETWEEN para ser inserido na declaração 
     *
     * @param  string|bool|int  $value1  Valor inicial necessário para montar o operador BETWEEN na declaração
     * @param  string|bool|int  $value2  Valor final necessário para montar o operador BETWEEN na declaração
     * @return $this 
     */
    public function notBetween($value1, $value2)
    {
        $this->setComparisonOperator("NOT BETWEEN", [$value1, $value2]);
        return $this;
    } // FIM -> notBetween

    /**
     * Prepara condição IS NULL como operador para ser inserido na declaração 
     * 
     * @return $this 
     */
    public function isNull()
    {
        $this->setComparisonOperator("IS NULL", "IS NULL");
        return $this;
    } // FIM -> isNull

    /**
     * Prepara condição IS NOT NULL como operador para ser inserido na declaração 
     * 
     * @return $this 
     */
    public function isNotNull()
    {
        $this->setComparisonOperator("IS NOT NULL", "IS NOT NULL");
        return $this;
    } // FIM -> isNull
    
    /**
     * Prepara operador IN para ser inserido na declaração 
     *
     * @param  array  $list  Lista com os valores necessários para montar o operador IN na declaração
     * @return $this 
     */
    public function in(array $list) 
    {
        $this->setComparisonOperator("IN", $list);
        return $this;
    } // FIM -> in

    /**
     * Prepara operador NOT IN para ser inserido na declaração 
     *
     * @param  array  $list  Lista com os valores necessários para montar o operador NOT IN na declaração
     * @return $this 
     */
    public function notIn(array $list) 
    {
        $this->setComparisonOperator("NOT IN", $list);
        return $this;
    } // FIM -> notIn
    
    /**
     * Prepara operador LIKE para ser inserido na declaração 
     *
     * @param  string  $list  Valor necessários para montar o operador LIKE com o coringa %$value% na declaração
     * @return $this 
     */
    public function like(string $value) 
    {
        $this->setComparisonOperator("LIKE", $value);
        return $this;
    } // FIM -> like
    
    /**
     * Prepara operador LIKE para ser inserido na declaração 
     *
     * @param  string  $list  Valor necessários para montar o operador LIKE com o coringa $value% na declaração
     * @return $this 
     */
    public function likeStart(string $value) 
    {
        $this->setComparisonOperator("LIKE START", $value);
        return $this;
    } // FIM -> likeStart
    
    /**
     * Prepara operador LIKE para ser inserido na declaração 
     *
     * @param  string  $list  Valor necessários para montar o operador LIKE com o coringa %$value na declaração
     * @return $this 
     */
    public function likeEnd(string $value) 
    {
        $this->setComparisonOperator("LIKE END", $value);
        return $this;
    } // FIM -> likeEnd
    
    /**
     * Prepara operador LIKE para ser inserido na declaração 
     *
     * @param  string  $list  Valor necessários para montar o operador LIKE com o coringa $value1%$value2 na declaração
     * @return $this 
     */
    public function likeStartEnd(string $value1, string $value2) 
    {
        $this->setComparisonOperator("LIKE START END", array($value1, $value2));
        return $this;
    } // FIM -> likeEnd
    
    /**
     * Monta a cláusula where e a retorna
     * @return string 
     */
    protected function getWhere(): string
    {
        if (isset($this->where["complete"]) && $this->where["complete"]) {
            $where  = array();
            $values = array();
            $mount  = "";
            foreach ($this->where["column"] as $key => $column) {
                $bind = ":{$column}_w{$key}";
                $op   = $this->where["comparisonOperator"][$key];
                
                switch ($op) {
                    case 'BETWEEN':
                    case 'NOT BETWEEN':
                        $where[] = "{$column} {$op} {$bind}_b1 AND {$bind}_b2";
        
                        $this->values["{$bind}_b1"] = $this->where["values"][$key][0];
                        $this->values["{$bind}_b2"] = $this->where["values"][$key][1];
                        break;

                    case 'IN':
                    case 'NOT IN':
                        $in = [];

                        foreach ($this->where["values"][$key] as $key => $value) {
                            $key++;
                            $in[] = "{$bind}_i{$key}";
                            $this->values["{$bind}_i{$key}"] = $value;
                        }

                        $in      = "(" . \implode(", ", $in) . ")";
                        $where[] = "{$column} {$op} {$in}";
                        break;

                    case 'IS NULL':
                    case 'IS NOT NULL':
                        $where[] = "{$column} {$op}";
                        break;

                    case 'LIKE START':
                        if (!isset($value)) {
                            $value = "{$this->where["values"][$key]}%";
                        }
                    case 'LIKE END':
                        if (!isset($value)) {
                            $value = "%{$this->where["values"][$key]}";
                        }
                    case 'LIKE START END':
                        if (!isset($value)) {
                            $value = "{$this->where["values"][$key][0]}%{$this->where["values"][$key][1]}";
                        }
                    case 'LIKE':
                        if (!isset($value)) {
                            $value = "%{$this->where["values"][$key]}%";
                        }
                        
                        $where[] = "{$column} LIKE '{$value}'";
                        break;
                        
                    default:
                        $where[] = "{$column} {$op} {$bind}";
        
                        $this->values[$bind] = $this->where["values"][$key];
                        break;
                }

            }

            $mount = "WHERE ";

            foreach ($where as $key => $statement) {
                $mount .= $statement;

                if (
                    !isset($this->where["logicalOperators"]) 
                    || !isset($this->where["logicalOperators"][$key])
                ) {
                    break;
                }

                $mount .= " {$this->where["logicalOperators"][$key]} ";
            }
        }
        
        return $this->where["clause"] = $mount;
    }

    /**
     * Indica o operador de comparação e o valor de comparação necessário para cláusula where
     *
     * @param  string  $operator  Operador de comparação para montar a cláusula where
     * @return $this 
     */
    private function setComparisonOperator(string $operator, $value)
    {
        if ($this->where["complete"]) {
            return;
        }

        $i = isset($this->where["column"]) ? \count($this->where["column"]) - 1 : 0;
        
        $this->where["comparisonOperator"][$i] = $operator;
        $this->where["values"][$i] = $value;
        $this->where["complete"]   = true;
        
        return $this;
    } // FIM -> setComparisonOperator
}