<?php
/**
 * Classe para realização de consultas
 * 
 * @author Bruno Silva Santana <brunoss.789@gmail.com>
 */

namespace MyDatabase\CRUD;

class Select extends \MyDatabase\Utils\Where
{
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
        parent::__construct("SELECT", 100, $mydatabase);
        
        $this->statement($column);
    } // FIM -> __construct
     
    /**
     * Passa para propriedade $table o nome da tabela em que a consulta será realizada
     * Monta na propriedade $statement o nome da tabela
     * 
     * @param  string  $table  String com o nome da tabela em que a consulta será realizada
     * @return Select  Retorna $this 
     */
    public function from(string $table): \MyDatabase\CRUD\Select
    {
        $this->table = $table; 
        $this->statement("FROM {$table}");
        return $this;
    } // FIM -> from
     
    /**
     * Como deve ser ordenado a consulta.
     *
     * @param  string  $column  Regra para ordenar
     * @param  string  $sort  Orientação da ordenação
     * @return Select  Retorna $this 
     */
    public function order(string $column, string $sort = "ASC"): \MyDatabase\CRUD\Select
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
        $where = $this->getWhere();

        $this->statement("{$where} {$this->order} {$this->limit}");
        $query = $this->prepare();
        
        if ($query) {
            try {
                $query->execute();

                return $query->fetchAll();
            } catch (\PDOException $e) {
                $this->mydatabase->handleError($e, "SELECT-EXECUTE", $this->statement);
            }
        }
        
        return Array();
    } // FIM -> execute
}
