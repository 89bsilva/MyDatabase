<?php
/**
 * Classe para realização de atualizações
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace CRUD;

class Update
{

    /**
     * Referencia do objeto MyDatabase.
     *
     * @property MyDatabase
     */
    private $db;

    /**
     * Declaração da consulta a base de dados.
     *
     * @property string
     */
    private $statement = "UPDATE ";

    /**
     * Limite para atualização.
     *
     * @property int
     */
    private $limit = 1;

    /**
     * Tabela alvo da consulta.
     *
     * @property string|bool
     */
    private $table = false;

    /**
     * Novo(s) dado(s) que será(ão) atualizado(s).
     *
     * @property array
     */
    private $values;

    /**
     * Condicão(ões) para indicar onde o(s) novo(s) valore(s) será(ão) inserido(s).
     *
     * @property string
     */
    private $where;

    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica qual(is) o(s) valor(es) que será(ão) atualizado(s)
     * 
     * @param string  $data  Array associativo com o(s) valor(es) que será(ão) inserido(s)
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(string $table, &$mydatabase)
    {
        $this->db         = $mydatabase;
        $this->statement .= "`{$table}` ";
        $this->table      = $table;
    } // FIM -> __construct
     
    /**
     * Monta a declaração da atualização
     * Insere os valores novos na propriedade $values
     * 
     * @param  array  $from  String com o host ou um Array associativo com o host e porta
     * @return Update  Retorna $this 
     */
    public function set(array $data): Update
    {
        $set = Array();

        foreach ($data as $column => $value) {
            $set[] = "`{$column}` = ?";
        }

        $set = implode(', ', $set);
        $this->values     = array_values($data);
        $this->statement .= "SET {$set}";
        return $this;
    } // FIM -> set
     
    /**
     * Passa para propriedade $where a condição para realizar a atualização
     * 
     * @param  string  $where  Condição para atualização
     * @return Update  Retorna $this 
     */
    public function where(string $where): Update
    {
        $this->where = $where;
        return $this;
    } // FIM -> where
     

    /**
     * Passa para a propriedade $limit um novo limite para consulta.
     *
     * @param  int  $limit  Novo limite para consulta
     * @return Update  Retorna $this 
     */
    public function limit(int $limit): Update
    {
        $this->limit = $limit; 
        return $this;
    } // FIM -> limit
     
    /**
     * Executa a atualização
     * 
     * @return int  Retorna a quantida de linhas afetadas pela atualização 
     */
    public function execute(): int
    {
        if (!$this->where || !$this->values) {
            return 0;
        }

        $this->statement .= " WHERE {$this->where}";
        $this->statement .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";
        $query = $this->db->prepareStatement($this->statement);
        if ($query) {
            try {
                $query->execute($this->values);
                return $query->rowCount();
            } catch (\PDOException $e) {
                $this->db->handleError($e, "UPDATE-EXECUTE", $this->statement);
            }
        }
        
        return 0;
    } // FIM -> execute
}
