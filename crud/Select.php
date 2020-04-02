<?php
/**
 * Classe para realização de consultas
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace CRUD;

class Select
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
    private $statement = "SELECT ";

    /**
     * Tabela alvo da consulta.
     *
     * @property string|bool
     */
    private $table = false;

    /**
     * Limite para consulta.
     *
     * @property int
     */
    private $limit = 100;

    /**
     * Ordenação da consulta.
     *
     * @property string
     */
    private $order = "";

    /**
     * Coloca a referência do objeto MyDatabase na propriedade $db
     * Indica qual(is) são a(s) coluna(s) da declaração
     * 
     * @param string  $column  String com a(s) coluna(s) requisitada(s) na consulta
     * @param MyDatabase  $mydatabase  Referencia do objeto que esta criando um novo objeto dessa classe
     */
    public function __construct(string $column, &$mydatabase)
    {
        $this->db         = $mydatabase;
        $this->statement .= $column;
    } // FIM -> __construct
     
    /**
     * Passa para propriedade $table o nome da tabela em que a consulta será realizada
     * Monta na propriedade $statement o nome da tabela
     * 
     * @param  string  $table  String com o nome da tabela em que a consulta será realizada
     * @return Select  Retorna $this 
     */
    public function from(string $table): Select
    {
        $this->table      = $table; 
        $this->statement .= " FROM {$table}";
        return $this;
    } // FIM -> from
     
    /**
     * Monta a cláusula where na proprieda $statement
     *
     * @param  string  $conditions  Condições necessária para montar a cláusula where
     * @return Select  Retorna  $this 
     */
    public function where(string $conditions): Select
    {
        $this->statement .= " WHERE {$conditions}";
        return $this;
    } // FIM -> where
     
    /**
     * Passa para a propriedade $limit um novo limite para consulta.
     *
     * @param  int  $limit  Novo limite para consulta
     * @return Select  Retorna $this 
     */
    public function limit(int $limit): Select
    {
        $this->limit = $limit; 
        return $this;
    } // FIM -> limit
     
    /**
     * Como deve ser ordenado a consulta.
     *
     * @param  string  $column  Regra para ordenar
     * @param  string  $sort  Orientação da ordenação
     * @return Select  Retorna $this 
     */
    public function order(string $column, string $sort = "ASC"): Select
    {
        $sort  = strtoupper($sort) === "DESC" ? "DESC" : "ASC";
        $this->order = "ORDER BY {$column} {$sort}"; 
        return $this;
    } // FIM -> order
     
    /**
     * Executa o select
     * 
     * @return array  Retorna os dados da consulta ou um array vazio 
     */
    public function execute(): array
    {
        if (!$this->table) {
            return Array();
        }

        $this->statement .= " {$this->order}";
        $this->statement .= $this->limit > 0 ? " LIMIT {$this->limit}" : "";
        $query = $this->db->prepareStatement($this->statement);
        if ($query) {
            try {
                $query->execute();

                return $query->fetchAll();
            } catch (\PDOException $e) {
                $this->db->handleError($e, "SELECT-EXECUTE", $this->statement);
            }
        }
        
        return Array();
    } // FIM -> execute
}
