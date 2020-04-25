<?php
/**
 * Classe com propriedadedes e metodos utilizado por mais de uma Classe dessa aplicação
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\Utils;

class Statement
{
    /**
     * Referencia do objeto MyDatabase.
     *
     * @property MyDatabase
     */
    protected $mydatabase;

    /**
     * Início da declaração. Ex.: SELECT|INSERT|UPDATE|DELETE FROM.
     *
     * @property string
     */
    private $init;

    /**
     * Limite padrão da declaração.
     *
     * @property int
     */
    private $defaultLimit;
    
    /**
     * Limite da declaração.
     *
     * @property string
     */
    protected $limit;

    /**
     * Declaração da consulta a base de dados.
     *
     * @property string
     */
    protected $statement;

    /**
     * Tabela alvo da consulta.
     *
     * @property string|bool
     */
    protected $table;

    /**
     * Mascara(s) para o(s) dado(s) que será(ão) inserido(s).
     *
     * @property string
     */
    protected $tokens;

    /**
     * Dado(s) que será(ão) utilizado(s) na declaração.
     *
     * @property array
     */
    protected $values;

    /**
     * Coluna(s) que será(ão) utilizada(s) na delcaração.
     *
     * @property array
     */
    protected $columns;

    /**
     * Inicia a montagem da declaração
     * Indica qual o limite padrão da declaração
     * @param string  $init  Início da declaração. Ex.: SELECT|INSERT|UPDATE|DELETE FROM
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     * @param int  $defaultLimit  Número inteiro com o limite padrão da consulta
     */
    protected function __construct(string $init, int $defaultLimit, &$mydatabase)
    {
        $this->init         = \strtoupper($init);
        $this->defaultLimit = $defaultLimit;
        $this->mydatabase   = $mydatabase;
        $this->startStatement();
    } // FIM -> __construct

    /**
     * Inicia a montagem da declaração e passa para algumas propriedadedes os valores iniciais
     */
    private function startStatement()
    {
        $this->limit($this->defaultLimit);
        $this->statement($this->init, false);
        
        $this->table   = false;
        $this->tokens  = array();
        $this->values  = array();
        $this->columns = array();

        if (isset($this->where)) {
            $this->where   = array();
        }
    } // FIM -> startStatement
    
    /**
     * Executa o insert
     * 
     * @return \PDOStatement  Retorna a declaração preparada para ser executada 
     */
    protected function prepare(): \PDOStatement
    {
        $query = $this->mydatabase->prepareStatement($this->statement);

        foreach ($this->values as $bind => $value) {
            $query->bindValue($bind, $value /*, PDO::PARAM_STR*/);
        }

        return $query;
    } // FIM -> prepare
        
    /**
     * Separa a(s) coluna(s) e o(s) valor(es) que será(ão) utilizado(s) na declaração
     *
     * @param array $data Array com o(s) dado(s) necessário(s) para realizar a declaração
     */

    protected function maskData(array $data): array
    {
        $dataList = \array_key_exists(0, $data) ? $data : array($data);
        $values   = array();
        $keys     = array();

        foreach ($dataList as $data) {
            $difference = \array_diff(array_keys($data), $keys);
            
            if(empty($difference)) {
                continue;
            }

            $keys = \array_merge($keys, $difference);
        }

        $i = 1;
        do {
            foreach ($keys as $key) {
                $data = $dataList[$i - 1];

                if(isset($data[$key])) {
                    $bind            = ":{$key}_{$i}";
                    $tokenList[$i][] = $bind; 
                    $values[$bind]   = $data[$key];
                } else {
                    $tokenList[$i][] = "DEFAULT";
                }
            }
            $i++;
        } while ($i <= count($dataList));

        $tokens = array();

        foreach ($tokenList as $token) {
            $tokens[] = "(" . \implode(", ", $token) . ")";
        }

        return array(
            "keys"   => $keys,
            "tokens" => $tokens,
            "values" => $values
        );
    } // FIM -> maskData
    
    /**
     * Atribui|Concatena à propriedade $statement uma string passada ao chamar a função
     *
     * @param  string  $statement  String que será atribuida|concatenada na propriedadede $statement
     * @param  bool  $concatenate  Flag que indica se a propriedadede $statement será concatenada ou substituída
     */
    public function statement(string $statement, bool $concatenate = true): void
    {
        $this->statement = $concatenate ? "{$this->statement} {$statement}" : $statement;
    } // FIM -> where
    
    /**
     * Monta a cláusula LIMIT na propriedade $limit
     *
     * @param  int  $limit  Limite para consulta
     * @return $this 
     */
    public function limit(int $limit)
    {
        $this->limit = $limit > 0 ? "LIMIT {$limit}" : "";
        return $this;
    } // FIM -> limit
}